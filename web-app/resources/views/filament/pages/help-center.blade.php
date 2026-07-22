<x-filament::page>
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="text-center py-4">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-50 text-primary-500 mb-4">
                <x-heroicon-o-question-mark-circle class="w-8 h-8" />
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">مركز المساعدة والدعم</h1>
            <p class="text-lg text-gray-500">دليل شامل لاستخدام نظام إدارة كفالة الأيتام</p>
        </div>

        {{-- SEARCH --}}
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <x-heroicon-o-search class="absolute right-4 top-3.5 w-5 h-5 text-gray-400" />
                <input type="text" wire:model.debounce.300ms="search"
                    placeholder="ابحث عن مساعدة... يتيم، كفيل، تقرير، نسخة احتياطية، صلاحيات، إعدادات..."
                    class="w-full pr-12 pl-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-right text-base" />
            </div>
            @if(!empty($search))
                <div class="text-center mt-2 text-sm text-gray-500">
                    @php $count = count($this->getFilteredModules()); @endphp
                    @if($count > 0)
                        تم العثور على {{ $count }} قسم مساعدة
                    @else
                        لم يتم العثور على نتائج. حاول بكلمة بحث مختلفة.
                    @endif
                </div>
            @endif
        </div>

        {{-- QUICK START --}}
        @if(empty($search))
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center">
                    <x-heroicon-o-academic-cap class="w-6 h-6 text-primary-600" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">ابدأ من هنا</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php $steps = [
                    ['num' => '01', 'title' => 'تسجيل الدخول', 'desc' => 'استخدم البريد الإلكتروني وكلمة المرور الممنوحة لك من مدير النظام.', 'icon' => 'heroicon-o-lock-open'],
                    ['num' => '02', 'title' => 'إضافة الأيتام', 'desc' => 'سجل بيانات الأيتام المستفيدين مع الصور والمستندات الرسمية.', 'icon' => 'heroicon-o-user-group'],
                    ['num' => '03', 'title' => 'إضافة الكفلاء', 'desc' => 'سجل بيانات الكفلاء والمحسنين الراغبين في دعم الأيتام.', 'icon' => 'heroicon-o-user-circle'],
                    ['num' => '04', 'title' => 'إنشاء الكفالات', 'desc' => 'اربط الأيتام بالكفلاء من خلال عقود كفالة محددة المبلغ والنوع.', 'icon' => 'heroicon-o-hand'],
                    ['num' => '05', 'title' => 'متابعة التقارير', 'desc' => 'اطبع التقارير الفردية والجماعية وصدرها بصيغ PDF و Excel.', 'icon' => 'heroicon-o-collection'],
                    ['num' => '06', 'title' => 'النسخ الاحتياطي', 'desc' => 'اضبط النسخ الاحتياطي التلقائي لحماية بيانات المؤسسة.', 'icon' => 'heroicon-o-archive'],
                ]; @endphp
                @foreach($steps as $step)
                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-sm font-bold text-primary-700">
                        {{ $step['num'] }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">{{ $step['title'] }}</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- MODULES GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @php $modules = $this->getFilteredModules(); @endphp
            @foreach($modules as $id => $module)
            <div wire:key="module-{{ $id }}"
                class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden transition-all duration-200 hover:shadow-lg">

                {{-- CARD HEADER --}}
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-{{ $module['color'] }}-50 flex items-center justify-center">
                            @php $icon = $module['icon'] ?? 'heroicon-o-information-circle'; @endphp
                            <x-dynamic-component :component="$icon" class="w-7 h-7 text-{{ $module['color'] }}-600" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-900">{{ $module['title'] }}</h3>
                            <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $module['description'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button wire:click="toggleModule('{{ $id }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition">
                            <x-heroicon-s-chevron-down class="w-4 h-4 transition-transform duration-200"
                                style="transform: {{ $activeModule === $id ? 'rotate(180deg)' : 'rotate(0deg)' }}" />
                            {{ $activeModule === $id ? 'إخفاء التفاصيل' : 'عرض التفاصيل' }}
                        </button>
                    </div>
                </div>

                {{-- DETAIL VIEW --}}
                @if($activeModule === $id)
                <div class="border-t border-gray-200 bg-gray-50 p-5 space-y-5">
                    {{-- Summary --}}
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-primary-500" />
                            <h4 class="font-bold text-gray-900">نظرة عامة</h4>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $module['summary'] }}</p>
                    </div>

                    {{-- Detail Sections --}}
                    @foreach($module['details'] ?? [] as $di => $detail)
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700">
                                {{ $di + 1 }}
                            </div>
                            <h4 class="font-bold text-gray-900">{{ $detail['title'] }}</h4>
                        </div>
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line pr-9">{{ $detail['body'] }}</div>
                    </div>
                    @endforeach

                    {{-- Module FAQ --}}
                    @if(!empty($module['faq']))
                    <div class="bg-white rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <x-heroicon-o-question-mark-circle class="w-5 h-5 text-warning-500" />
                            <h4 class="font-bold text-gray-900">أسئلة شائعة</h4>
                        </div>
                        <div class="space-y-2 pr-0">
                            @foreach($module['faq'] as $fi => $faq)
                            @php $faqId = $id . '_faq_' . $fi; @endphp
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button wire:click="toggleFaq('{{ $faqId }}')"
                                    class="w-full flex items-center justify-between gap-2 px-4 py-3 text-right bg-white hover:bg-gray-50 transition text-sm font-medium text-gray-800">
                                    <span>{{ $faq['q'] }}</span>
                                    <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                        style="transform: {{ $activeFaq === $faqId ? 'rotate(180deg)' : 'rotate(0deg)' }}" />
                                </button>
                                @if($activeFaq === $faqId)
                                <div class="px-4 pb-3 text-sm text-gray-600 leading-relaxed bg-gray-50 border-t border-gray-200 pt-3">
                                    {{ $faq['a'] }}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if(empty($modules) && !empty($search))
        <div class="text-center py-12">
            <x-heroicon-o-search class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-bold text-gray-500">لا توجد نتائج</h3>
            <p class="text-sm text-gray-400">لم نعثر على أي قسم مساعدة يطابق بحثك. حاول بكلمة مختلفة.</p>
        </div>
        @endif

        {{-- ALL FAQS SECTION --}}
        @if(empty($search) && !empty($this->getAllFaqs()))
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-lg bg-warning-100 flex items-center justify-center">
                    <x-heroicon-o-question-mark-circle class="w-6 h-6 text-warning-600" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">الأسئلة الشائعة</h2>
            </div>
            <div class="space-y-2">
                @foreach($this->getAllFaqs() as $faq)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button wire:click="toggleFaq('{{ $faq['id'] }}')"
                        class="w-full flex items-center justify-between gap-2 px-4 py-3 text-right bg-white hover:bg-gray-50 transition text-sm font-medium text-gray-800">
                        <span>{{ $faq['q'] }}</span>
                        <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform duration-200"
                            style="transform: {{ $activeFaq === $faq['id'] ? 'rotate(180deg)' : 'rotate(0deg)' }}" />
                    </button>
                    @if($activeFaq === $faq['id'])
                    <div class="px-4 pb-3 text-sm text-gray-600 leading-relaxed bg-gray-50 border-t border-gray-200 pt-3">
                        {{ $faq['a'] }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- CONTACT SUPPORT --}}
        @if(empty($search))
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-danger-100 flex items-center justify-center">
                    <x-heroicon-o-phone class="w-6 h-6 text-danger-600" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">الدعم الفني</h2>
            </div>
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 p-4 rounded-lg bg-gray-50 border border-gray-100">
                <div class="flex-shrink-0">
                    <x-heroicon-o-information-circle class="w-8 h-8 text-gray-400" />
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    إذا واجهت مشكلة في استخدام النظام أو لديك استفسار حول أي من الميزات، يرجى التواصل مع مسؤول النظام للحصول على المساعدة الفنية اللازمة.
                </p>
            </div>
        </div>
        @endif

    </div>
</x-filament::page>
