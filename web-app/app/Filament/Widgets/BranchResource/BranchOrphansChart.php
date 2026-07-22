<?php

namespace App\Filament\Widgets\BranchResource;

use Filament\Widgets\DoughnutChartWidget;

class BranchOrphansChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }
}
