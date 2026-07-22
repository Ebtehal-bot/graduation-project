<?php

namespace App\Filament\Resources\SponsorshipResource\Pages;

use App\Filament\Resources\SponsorshipResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSponsorships extends ListRecords
{
    protected static string $resource = SponsorshipResource::class;

    protected function getActions(): array
    {
        return [
            // هذا التعديل سيجعل الزر يفتح صفحة جديدة بدلاً من النافذة المنبثقة الفارغة
            Actions\CreateAction::make(), 
        ];
    }
}