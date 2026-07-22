<x-filament::page
    :widget-data="['record' => $record]"
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-view-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'filament-resources-record-' . $record->getKey(),
    ])"
>
    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagerTabsWithForm()) || (! count($relationManagers)))
        {{ $this->form }}
    @endif

    @if (count($relationManagers))
        @if (! $this->hasCombinedRelationManagerTabsWithForm())
            <x-filament::hr />
        @endif

        <x-filament::resources.relation-managers
            :active-manager="$activeRelationManager"
            :form-tab-label="$this->getFormTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($this->hasCombinedRelationManagerTabsWithForm())
                <x-slot name="form">
                    {{ $this->form }}
                </x-slot>
            @endif
        </x-filament::resources.relation-managers>
    @endif

    <x-filament::hr />

    <h2 style="font-size:18px;font-weight:bold;margin-bottom:16px;padding:0 0 8px 0;border-bottom:2px solid #3b82f6;color:#1f2937;">
        المستندات الرسمية
    </h2>

    @include('filament.orphan-documents', ['orphan' => $orphan])
</x-filament::page>
