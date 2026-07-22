<x-filament::page>
    <x-filament::form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="flex justify-center mt-8">
            <x-filament::button type="submit" class="w-64" size="lg">
                حفظ الإعدادات
            </x-filament::button>
        </div>
    </x-filament::form>
</x-filament::page>
