<?php

namespace App\Filament\Resources\OrphanResource\Pages;

use App\Filament\Resources\OrphanResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOrphan extends ViewRecord
{
    protected static string $resource = OrphanResource::class;
    protected static string $view = 'filament.pages.view-orphan';

    protected function getActions(): array
    {
        return [
            \Filament\Pages\Actions\EditAction::make(),
        ];
    }

    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), ['orphan' => $this->record]);
    }
}
