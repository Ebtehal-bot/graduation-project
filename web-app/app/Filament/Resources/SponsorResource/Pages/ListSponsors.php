<?php

namespace App\Filament\Resources\SponsorResource\Pages;

use App\Filament\Resources\SponsorResource;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSponsors extends ListRecords
{
    protected static string $resource = SponsorResource::class;

    protected function getTableQuery(): Builder
    {
        return User::role('sponsor');
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            \Filament\Tables\Actions\Action::make('export_pdf')
                ->label('التقرير الجماعي للكفلاء')
                ->icon('heroicon-o-users')
                ->color('success')
                ->url(fn (): string => route('sponsors.export'))
                ->openUrlInNewTab(),
        ];
    }
}
