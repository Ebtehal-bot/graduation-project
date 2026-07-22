<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>منصة كفيل لرعاية وكفالة الأيتام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a237e',
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    },
                    fontFamily: {
                        sans: ['Tajawal', 'Cairo', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', 'Cairo', sans-serif; }
        .gradient-hero { background: linear-gradient(135deg, #1a237e 0%, #0d1b4a 100%); }
        .gradient-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .btn-primary { transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4); }
        .nav-blur { backdrop-filter: blur(12px); background: rgba(255,255,255,0.92); }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="nav-blur fixed w-full top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-primary tracking-wide">منصة كفيل لرعاية الأيتام</span>
                </div>
                <!-- Temporary: Login button moved to Hero section for graduation presentation. Restore Navbar position after presentation. -->
                {{-- <div>
                    <a href="/admin/login"
                       class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg btn-primary text-sm">
                       الدخول للنظام
                    </a>
                </div> --}}
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-hero min-h-screen flex items-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" viewBox="0 0 1440 800" xmlns="http://www.w3.org/2000/svg">
                <circle cx="200" cy="100" r="250" fill="white" opacity="0.15" />
                <circle cx="1200" cy="600" r="300" fill="white" opacity="0.10" />
                <circle cx="700" cy="200" r="200" fill="white" opacity="0.08" />
                <circle cx="100" cy="600" r="180" fill="white" opacity="0.12" />
                <circle cx="1300" cy="150" r="150" fill="white" opacity="0.10" />
            </svg>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10 w-full">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    معاً لنصنع الفرق في حياة اليتيم
                </h1>
                <p class="text-lg md:text-xl text-indigo-200 max-w-3xl mx-auto leading-relaxed mb-10">
                    منصة متكاملة لإدارة شؤون الأيتام وربط الكفلاء بهم بشفافية تامة.
                    نسعى لبناء مستقبل أفضل من خلال التكافل المجتمعي والرعاية الشاملة.
                </p>
                <!-- Temporary: Login button moved to Hero section for graduation presentation. Restore Navbar position after presentation. -->
                <a href="/admin/login"
                   class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg btn-primary text-sm">
                   الدخول للنظام
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 md:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">مميزات المنصة</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">أدوات متكاملة تصمم خصيصاً لإدارة فعالة ومتابعة دقيقة لجميع العمليات</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">

                <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100 gradient-card transition-all duration-300">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">إدارة شاملة للأيتام</h3>
                    <p class="text-gray-500 leading-relaxed">قاعدة بيانات متكاملة لتسجيل ومتابعة جميع بيانات الأيتام مع إمكانية رفع المرفقات والتقارير الدورية.</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100 gradient-card transition-all duration-300">
                    <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">الشفافية المالية</h3>
                    <p class="text-gray-500 leading-relaxed">متابعة دقيقة للكفالات والمبالغ الشهرية مع تقارير مالية شاملة تضمن الشفافية الكاملة بين الكفيل واليتيم.</p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-8 text-center border border-gray-100 gradient-card transition-all duration-300">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-3">تقارير تحليلية ومخططات</h3>
                    <p class="text-gray-500 leading-relaxed">لوحة تحكم تفاعلية مع رسوم بيانية وإحصائيات دقيقة تساعد في اتخاذ القرارات وتحليل الأداء بشكل احترافي.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white">
        <div class="container mx-auto px-6 py-12">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                <div class="text-right md:w-1/3">
                    <h4 class="text-lg font-bold mb-4">منصة كفيل</h4>
                    <p class="text-indigo-200 leading-relaxed text-sm">منصة رائدة في مجال رعاية وكفالة الأيتام، نسعى لتحقيق التكافل المجتمعي وبناء مستقبل أفضل للأيتام.</p>
                </div>
                <div class="text-center md:w-1/3">
                    <h4 class="text-lg font-bold mb-3">المشرف الأكاديمي</h4>
                    <p class="text-indigo-200 leading-relaxed text-sm mb-5">د. محمد شبيل</p>
                    <h4 class="text-lg font-bold mb-3">فريق التطوير</h4>
                    <div class="text-indigo-200 leading-relaxed text-sm space-y-1">
                        <p>إبتهال طاهر بركات</p>
                        <p>براءة محمد النهمي </p>
                       <p>هناء سعيد </p>
                    </div>
                </div>
                <div class="text-left md:text-left">
                    <h4 class="text-2xl font-bold mb-6">معلومات الاتصال</h4>
                    <ul class="space-y-4 text-lg">
                        @if(!empty($settings->phone))
                        <li class="flex items-center justify-start gap-3 flex-row-reverse">
                            <svg class="w-6 h-6 text-indigo-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span dir="ltr" class="text-indigo-200">{{ $settings->phone }}</span>
                        </li>
                        @endif
                        @if(!empty($settings->email))
                        <li class="flex items-center justify-start gap-3 flex-row-reverse">
                            <svg class="w-6 h-6 text-indigo-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $settings->email }}" class="text-indigo-200 hover:text-white transition">{{ $settings->email }}</a>
                        </li>
                        @endif
                        @if(!empty($settings->whatsapp))
                        <li class="flex items-center justify-start gap-3 flex-row-reverse">
                            <svg class="w-6 h-6 text-indigo-300 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <a href="https://wa.me/{{ $settings->whatsapp }}" target="_blank" class="text-indigo-200 hover:text-white transition">{{ $settings->whatsapp }}</a>
                        </li>
                        @endif
                    </ul>
                    <div class="flex gap-6 justify-start mt-4">
                        @if(!empty($settings->facebook))
                        <a href="{{ $settings->facebook }}" target="_blank" class="text-indigo-300 hover:text-white transition">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        @endif
                        @if(!empty($settings->twitter))
                        <a href="{{ $settings->twitter }}" target="_blank" class="text-indigo-300 hover:text-white transition">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        @endif
                        @if(!empty($settings->instagram))
                        <a href="{{ $settings->instagram }}" target="_blank" class="text-indigo-300 hover:text-white transition">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        @endif
                        @if(!empty($settings->youtube))
                        <a href="{{ $settings->youtube }}" target="_blank" class="text-indigo-300 hover:text-white transition">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="border-t border-indigo-800 mt-8 pt-8 text-center text-indigo-300 text-sm">
                &copy; {{ date('Y') }} منصة كفيل لرعاية الأيتام. جميع الحقوق محفوظة.
            </div>
        </div>
    </footer>

</body>
</html>
