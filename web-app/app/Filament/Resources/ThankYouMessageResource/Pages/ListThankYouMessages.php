<?php

namespace App\Filament\Resources\ThankYouMessageResource\Pages;

use App\Filament\Resources\ThankYouMessageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThankYouMessages extends ListRecords
{
    protected static string $resource = ThankYouMessageResource::class;

    protected static string $view = 'filament.resources.thank-you-message-resource.pages.list-thank-you-messages';

    protected function getActions(): array
    {
        return [

        ];
    }
}
