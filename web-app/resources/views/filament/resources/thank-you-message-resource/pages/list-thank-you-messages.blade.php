<x-filament::page
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-list-records-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])"
>
    {{-- التقارير المتقدمة --}}
    <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-white/20 flex items-center justify-center">
                    <x-heroicon-o-document-report class="w-8 h-8 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">التقارير المتقدمة</h2>
                    <p class="text-primary-100 text-sm mt-1">تقارير ذكية - كشوفات صرف مستحقات - بطاقات تعريفية - إنذارات الكفالات - أداء الفروع</p>
                </div>
            </div>
            <a href="{{ \App\Filament\Pages\OrphanReports::getUrl() }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary-700 rounded-lg hover:bg-primary-50 transition font-bold shadow-lg">
                <span>📊</span>
                فتح التقارير المتقدمة
                <x-heroicon-s-arrow-left class="w-5 h-5" />
            </a>
        </div>
    </div>

    {{-- استمارات الأيتام --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center text-xl">📋</div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">استمارات الأيتام</h2>
                <p class="text-sm text-gray-500">طباعة استمارات الأيتام الفارغة أو المعبأة ببيانات النظام.</p>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('orphans.form.empty') }}" target="_blank"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm font-medium">
                <span>🖨️</span>
                طباعة استمارة يتيم فارغة
            </a>
            <div class="flex-1 flex gap-3 items-center">
                <select id="orphan-select-embedded"
                        class="flex-1 rounded-lg border-gray-300 p-2.5 text-sm border focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-center"
                        style="text-align: center; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                    <option value="">-- اختر يتيماً --</option>
                    @foreach(\App\Models\Orphan::orderBy('name')->get() as $orphan)
                        <option value="{{ $orphan->id }}">{{ $orphan->name }} ({{ $orphan->file_number }})</option>
                    @endforeach
                </select>
                <button type="button" id="print-filled-embedded"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm font-medium whitespace-nowrap">
                    <span>🖨️</span>
                    طباعة استمارة اليتيم
                </button>
            </div>
        </div>
    </div>

    {{ $this->table }}

    @push('scripts')
    <script>
        document.getElementById('print-filled-embedded')?.addEventListener('click', function(e) {
            var id = document.getElementById('orphan-select-embedded').value;
            if (id) {
                window.open('{{ route("orphans.form.filled") }}?record=' + id, '_blank');
            }
        });
    </script>
    @endpush
</x-filament::page>
