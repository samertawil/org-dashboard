<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Features | مميزات النظام</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .font-arabic {
            font-family: 'Tajawal', sans-serif;
        }

        .font-english {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900" x-data="{ lang: 'en', dir: 'ltr' }"
    :class="lang === 'ar' ? 'font-arabic' : 'font-english'" :dir="dir">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <span class="text-2xl font-bold text-blue-600">OrgDashboard</span>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="lang = lang === 'en' ? 'ar' : 'en'; dir = lang === 'ar' ? 'rtl' : 'ltr'"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition-colors flex items-center gap-2">
                        <span x-text="lang === 'en' ? '🇺🇸 English' : '🇸🇦 العربية'"></span>
                    </button>
                    <a href="{{ route('login') }}"
                        class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors">
                        <span x-text="lang === 'en' ? 'Login' : 'تسجيل الدخول'"></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="  mx-auto main.mx-auto.max-w-7xl.px-4.sm\:mt-12.sm\:px-6.md\:mt-16.lg\:mt-20.lg\:px-8.xl\:mt-28">
                    <div class="sm:text-center lg:text-start"
                        :class="lang === 'ar' ? 'lg:text-right' : 'lg:text-left'">
                        <h1 class=" ml-5 text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <br>
                            <span class="block xl:inline "
                                x-text="lang === 'en' ? 'Comprehensive Management' : 'إدارة شاملة ومتكاملة'"></span>
                            <span class="block text-blue-600 xl:inline  mr-5"
                                x-text="lang === 'en' ? 'For AFSC Organization' : 'لمؤسستك ومشاريعك'"></span>
                        </h1>
                        <p class=" ml-5 mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0"
                            x-text="lang === 'en' 
                           ? 'A complete solution for Activity Management, HR, Educational Programs, and Financial Reporting. Designed for NGOs and Educational Institutions.' 
                           : 'حل متكامل لإدارة الأنشطة، الموارد البشرية، البرامج التعليمية، والتقارير المالية. مصمم خصيصاً للمؤسسات غير الحكومية والمؤسسات التعليمية.'">
                        </p>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-gray-50 flex items-center justify-center"
            :class="lang === 'ar' ? 'lg:left-0 lg:right-auto' : ''">
            <!-- Placeholder for a dashboard screenshot -->
            <div
                class="w-full h-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-200 p-10">
                <img src="{{ asset('logo.png') }}" alt="AFSC Logo"
                    class="max-h-80 w-auto object-contain drop-shadow-xl" />
            </div>
        </div>


    </div>
    <div 
    x-data="{ lang: 'ar' }"
    :dir="lang === 'ar' ? 'rtl' : 'ltr'"
    class="relative bg-white overflow-hidden"
>
 
</div>

    <!-- Features Grid -->
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase"
                    x-text="lang === 'en' ? 'Features' : 'المميزات'"></h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl"
                    x-text="lang === 'en' ? 'Everything you need to succeed' : 'كل ما تحتاجه للنجاح'">
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">

                    <!-- Feature 1 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Activity & Project Management' : 'إدارة الأنشطة والمشاريع'">
                            </h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Track activities with full details (Cost, Sector, Dates). Link to geographic locations, manage parcels, beneficiaries, and support multiple sectors (Education, Relief).' 
                           : 'سجل كامل لكل نشاط (التكلفة، القطاع، التواريخ). ربط بالمواقع الجغرافية، إدارة الطرود والمستفيدين، ودعم قطاعات متعددة (تعليمي، إغاثي).'">
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'HR & Employees' : 'إدارة الموارد البشرية'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Complete employee profiles, organizational structure (Departments, Positions), and task assignment tracking.' 
                           : 'ملفات متكاملة للموظفين، الهيكل التنظيمي (أقسام، مسميات وظيفية)، ومتابعة إسناد المهام.'">
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Smart Calendar & Tasks' : 'التقويم الذكي وإدارة المهام'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Interactive calendar, multi-employee task assignment, \'Assigned By\' tracking, and dedicated personal task dashboard widgets.' 
                           : 'تقويم تفاعلي، إسناد مهام متعددة للموظفين، تتبع \'مكلف من قبل\'، ولوحة مهام شخصية لكل موظف.'">
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Educational Module' : 'النظام التعليمي'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Student management, Learning Groups, Subject-Teacher linking, and Teaching Points management.' 
                           : 'إدارة الطلاب، المجموعات التعليمية، ربط المواد بالمدرسين، وإدارة مراكز ونقاط التعليم.'">
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Reports & Analytics' : 'التقارير والتحليلات'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Real-time KPIs, Visual Charts (Pie/Bar), Financial Summaries, and Advanced Filters for data extraction.' 
                           : 'مؤشرات أداء لحظية، رسوم بيانية تفاعلية، ملخصات مالية، وفلاتر متقدمة لاستخراج البيانات.'">
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Security & Permissions' : 'الأمان والصلاحيات'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Dynamic Roles & Abilities system. control exactly who sees what data. Secure data isolation for sensitive information.' 
                           : 'نظام صلاحيات وأدوار ديناميكي. تحكم كامل في من يرى ماذا. عزل آمن للبيانات الحساسة.'">
                        </p>
                    </div>

                    <!-- Feature 7 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-pink-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Flexibility & Virtual Status' : 'المرونة والحالات الافتراضية'">
                            </h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Dynamic \'Virtual Status\' system allowing you to create new categories and hierarchies on the fly without coding.' 
                           : 'نظام \'حالات افتراضية\' ديناميكي يسمح بإنشاء تصنيفات هرمية جديدة فوراً دون الحاجة لتدخل برمجي.'">
                        </p>
                    </div>

                    <!-- Feature 8 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Bulk Actions (Import)' : 'المعالجة الجماعية (استيراد)'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Import large datasets (e.g., Students) via Excel to save time and reduce errors. Get set up in minutes.' 
                           : 'استيراد بيانات ضخمة (مثل الطلاب) عبر ملفات Excel لتوفير الوقت وتقليل الأخطاء. إعداد النظام في دقائق.'">
                        </p>
                    </div>

                    <!-- Feature 9 -->
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-teal-500 rounded-md p-3 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="ml-4 text-lg font-medium text-gray-900"
                                :class="lang === 'ar' ? 'mr-4 ml-0' : 'ml-4'"
                                x-text="lang === 'en' ? 'Mobile Responsive' : 'متوافق مع الجوال'"></h3>
                        </div>
                        <p class="text-base text-gray-500"
                            x-text="lang === 'en' 
                           ? 'Fully responsive design optimized for Mobile and Tablet use in the field. Access your dashboard from anywhere.' 
                           : 'تصميم متجاوب بالكامل ومحسن للاستخدام عبر الجوال والتابلت في الميدان. يمكنك الوصول للوحة التحكم من أي مكان.'">
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-700">
        <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                <span class="block"
                    x-text="lang === 'en' ? 'Ready to boost productivity?' : 'جاهز لزيادة الإنتاجية؟'"></span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-blue-200"
                x-text="lang === 'en' ? 'Start managing your organization effectively today.' : 'ابدأ بإدارة مؤسستك بفعالية اليوم.'">
            </p>
            <a href="{{ route('login') }}"
                class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto">
                <span x-text="lang === 'en' ? 'Get Started' : 'ابدأ الآن'"></span>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-400">
            &copy; {{ date('Y') }} OrgDashboard. <span
                x-text="lang === 'en' ? 'All rights reserved.' : 'جميع الحقوق محفوظة.'"></span>
        </div>
    </footer>

</body>

</html>
