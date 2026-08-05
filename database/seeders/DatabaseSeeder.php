<?php

namespace Database\Seeders;

use App\Enums\ConsultantStatus;
use App\Enums\SiteStatus;
use App\Enums\UserStatus;
use App\Models\Consultant;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with roles, users, field sites, and consultants.
     */
    public function run(): void
    {
        // 0. Ensure Roles Exist
        $hrRole = Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        $consultantRole = Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);

        // 1. Active HR Admin
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'مدير الموارد البشرية',
                'email' => 'admin@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $admin->syncRoles([$hrRole]);

        // 2. Active Field Consultant
        $consultantUser = User::firstOrCreate(
            ['username' => 'consultant'],
            [
                'name' => 'أحمد السالم (استشاري ميداني)',
                'email' => 'ahmed@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $consultantUser->syncRoles([$consultantRole]);

        // 3. Inactive User (for BR-002 testing)
        $inactive = User::firstOrCreate(
            ['username' => 'inactive_user'],
            [
                'name' => 'مستخدم موقوف',
                'email' => 'inactive@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::INACTIVE,
            ]
        );
        $inactive->syncRoles([$consultantRole]);

        // 4. Seed Initial Field Sites (Phase 01)
        Site::firstOrCreate(
            ['code' => 'SITE-TRIPOLI-01'],
            [
                'name' => 'مجمع خدمات النقل البحري',
                'city' => 'طرابلس',
                'address' => 'طريق الشط، ميناء طرابلس البحرية',
                'status' => SiteStatus::ACTIVE,
                'notes' => 'المبنى الرئيسي ومرافق الشحن والزيارات الميدانية',
            ]
        );

        Site::firstOrCreate(
            ['code' => 'SITE-BENGHAZI-02'],
            [
                'name' => 'مركز تقنية المعلومات والاتصالات',
                'city' => 'بنغازي',
                'address' => 'شارع دبي، بالقرب من الفندق البلدي',
                'status' => SiteStatus::ACTIVE,
                'notes' => 'المقر الفرعي والمختبرات التقنية الميدانية',
            ]
        );

        Site::firstOrCreate(
            ['code' => 'SITE-MISRATA-03'],
            [
                'name' => 'محطة الطاقة الشمسية المتجددة',
                'city' => 'مصراتة',
                'address' => 'المنطقة الصناعية الكبرى',
                'status' => SiteStatus::INACTIVE,
                'notes' => 'تحت الصيانة الدورية وتوقف مؤقت للمهام',
            ]
        );

        // 5. Seed Initial Consultants (Phase 02)
        Consultant::firstOrCreate(
            ['employee_number' => 'EMP-1001'],
            [
                'user_id' => $consultantUser->id,
                'full_name' => 'أحمد السالم',
                'phone' => '091-234-5678',
                'specialization' => 'هندسة مدنية وشبكات',
                'hire_date' => '2026-01-01',
                'employment_status' => ConsultantStatus::ACTIVE,
                'notes' => 'استشاري رئيسي مشرف على مواقع طرابلس',
            ]
        );

        $consultant2User = User::firstOrCreate(
            ['username' => 'salem_bouaishi'],
            [
                'name' => 'سالم البوعيشي',
                'email' => 'salem.b@fcpms.test',
                'password' => Hash::make('password'),
                'status' => UserStatus::ACTIVE,
            ]
        );
        $consultant2User->syncRoles([$consultantRole]);

        Consultant::firstOrCreate(
            ['employee_number' => 'EMP-1002'],
            [
                'user_id' => $consultant2User->id,
                'full_name' => 'سالم البوعيشي',
                'phone' => '092-876-5432',
                'specialization' => 'صيانة ومتابعة تشغيلية',
                'hire_date' => '2026-02-15',
                'employment_status' => ConsultantStatus::ACTIVE,
                'notes' => 'استشاري ميداني لمواقع بنغازي ومصراتة',
            ]
        );
    }
}
