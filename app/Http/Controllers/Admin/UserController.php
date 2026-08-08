<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-users');

        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');

        $query = User::with(['roles', 'consultant'])->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($roleFilter) && $roleFilter !== 'all') {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $users = $query->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'status' => $user->status ?? 'active',
                'roles' => $user->roles->pluck('name'),
                'role_names' => $user->roles->pluck('name')->map(function($r) {
                    return match($r) {
                        'admin' => 'مدير نظام الفائق',
                        'hr' => 'مدير موارد بشرية',
                        'consultant' => 'استشاري ميداني',
                        default => $r,
                    };
                }),
                'consultant_id' => $user->consultant ? $user->consultant->id : null,
                'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i') : null,
            ];
        });

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);

        $roles = Role::all()->map(fn($r) => [
            'name' => $r->name,
            'label' => match($r->name) {
                'admin' => 'مدير نظام الفائق (Admin)',
                'hr' => 'مدير موارد بشرية (HR)',
                'consultant' => 'استشاري ميداني (Consultant)',
                default => $r->name,
            }
        ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'availableRoles' => $roles,
            'filters' => [
                'search' => $search ?? '',
                'role' => $roleFilter ?? 'all',
                'status' => $statusFilter ?? 'all',
            ],
            'stats' => [
                'total' => User::count(),
                'admins' => User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->count(),
                'hr_staff' => User::whereHas('roles', fn($q) => $q->where('name', 'hr'))->count(),
                'consultants' => User::whereHas('roles', fn($q) => $q->where('name', 'consultant'))->count(),
                'active' => User::where('status', 'active')->count(),
            ]
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'حقل الاسم الكامل مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد الإلكتروني مسجل سابقاً',
            'username.unique' => 'اسم المستخدم هذا مُستخدم سابقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 6 خانات',
            'role.required' => 'يلزم اختيار دور الصلاحيات للمستخدم',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'] ?? strstr($validated['email'], '@', true),
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        ActivityLogger::log(
            'create_user',
            'User',
            $user->id,
            "تم إنشاء حساب مستخدم جديد: {$user->name} ({$validated['role']})",
            null,
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $validated['role'],
                'status' => $user->status,
            ]
        );

        return redirect()->back()->with('success', 'تم إنشاء حساب المستخدم بنجاح وتعيين الصلاحيات.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'حقل الاسم الكامل مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم لحساب آخر',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 6 خانات',
            'role.required' => 'يلزم اختيار دور الصلاحيات للمستخدم',
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'role' => $user->roles->pluck('name')->first(),
            'status' => $user->status,
        ];

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['username'])) {
            $user->username = $validated['username'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->status = $validated['status'];
        $user->save();

        $user->syncRoles([$validated['role']]);

        ActivityLogger::log(
            'update_user',
            'User',
            $user->id,
            "تم تحديث بيانات حساب المستخدم: {$user->name}",
            $oldValues,
            [
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $validated['role'],
                'status' => $user->status,
                'password_changed' => !empty($validated['password']) ? 'نعم (تم التغيير)' : 'لم تتغير',
            ]
        );

        return redirect()->back()->with('success', 'تم تحديث بيانات حساب المستخدم بنجاح.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'لا يمكنك تعطيل حسابك الشخصي الذي تستخدمه حالياً.']);
        }

        $oldStatus = $user->status ?? 'active';
        $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

        $user->status = $newStatus;
        $user->save();

        ActivityLogger::log(
            'toggle_user_status',
            'User',
            $user->id,
            "تم تغيير حالة حساب المستخدم {$user->name} إلى: {$newStatus}",
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        return redirect()->back()->with('success', 'تم تغيير حالة الحساب بنجاح.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'محظور: لا يمكنك حذف حسابك الشخصي الحلي.']);
        }

        if ($user->consultant) {
            return redirect()->back()->withErrors(['error' => 'لا يمكنك حذف هذا الحساب لأنه مرتبط بملف استشاري ميداني. يلزم حذفه من دليل الاستشاريين أولاً.']);
        }

        $userName = $user->name;
        $oldData = $user->toArray();
        $user->delete();

        ActivityLogger::log(
            'delete_user',
            'User',
            $user->id,
            "تم حذف حساب المستخدم النهائي: {$userName}",
            $oldData,
            null
        );

        return redirect()->back()->with('success', 'تم حذف حساب المستخدم بنجاح.');
    }
}
