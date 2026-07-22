<x-filament::page class="filament-dashboard-page">
    @php
        $dashboardWidgets = array_filter($this->getWidgets(), fn($w) => $w !== \App\Filament\Widgets\BranchResource\BranchOrphansChart::class);
    @endphp
    <x-filament::widgets
        :widgets="$dashboardWidgets"
        :columns="$this->getColumns()"
    />
</x-filament::page>
