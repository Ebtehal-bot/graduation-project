<x-filament::page>
    <form wire:submit.prevent="generateReport" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-printer" size="lg" color="success">
                توليد التقرير وطباعة الكشف فوراً
            </x-filament::button>
        </div>
    </form>
</x-filament::page>