<?php

namespace App\Filament\Resources\ThankYouMessageResource\Pages;

use App\Filament\Resources\ThankYouMessageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThankYouMessage extends EditRecord
{
    protected static string $resource = ThankYouMessageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
