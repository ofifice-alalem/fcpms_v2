<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 | غير مصرح بالوصول</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#090d16] text-white min-h-screen flex items-center justify-center font-sans relative overflow-hidden p-4 dir-rtl">
    
    <!-- Spatial Glow Orbs -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-red-600/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-lg relative z-10">
        <div class="p-8 sm:p-10 rounded-3xl bg-slate-900/80 border border-white/10 backdrop-blur-2xl shadow-2xl space-y-6 text-center">
            
            <!-- Icon Badge -->
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-red-500/15 border border-red-500/30 text-red-400 mb-2 shadow-lg shadow-red-500/10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <div class="space-y-2">
                <span class="inline-block px-3.5 py-1 rounded-full text-xs font-mono font-black bg-red-500/20 text-red-300 border border-red-500/30">
                    خطأ 403 - غير مصرح
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                    غير مصرح لك بالوصول لهذه الصفحة
                </h1>
                <p class="text-sm font-bold text-slate-300/80 leading-relaxed">
                    عذراً، حسابك الحالي لا يمتلك الأدوار أو الصلاحيات الكافية لدخول هذه البوابة. يمكنك تسجيل الخروج والدخول بالحساب المناسب.
                </p>
            </div>

            @auth
            <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between text-right text-xs">
                <div class="space-y-0.5 min-w-0 flex-1">
                    <span class="font-black text-white block truncate">{{ auth()->user()->name }}</span>
                    <span class="text-[11px] font-bold text-slate-400 block truncate">{{ auth()->user()->email }}</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-white/10 text-[10px] font-black text-slate-300 shrink-0">حساب مسجّل</span>
            </div>
            @endauth

            <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                <a href="{{ route('dashboard') }}" class="w-full sm:flex-1 py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>العودة للبوابة الرئيسية</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="w-full sm:flex-1">
                    @csrf
                    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-red-500/20 hover:bg-red-600 text-red-300 hover:text-white border border-red-500/30 font-black text-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>تسجيل الخروج 🚪</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</body>
</html>
