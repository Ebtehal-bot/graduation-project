<?php

namespace App\Filament\Resources\SponsorshipResource\Pages;

use App\Filament\Resources\SponsorshipResource;
use App\Notifications\SponsorshipStatusNotification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsorship extends EditRecord
{
    protected static string $resource = SponsorshipResource::class;

    protected ?string $originalStatus = null;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->originalStatus = $this->record->status;
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->originalStatus === $this->record->status) {
            return;
        }

        $sponsorship = $this->record->loadMissing(['sponsor.user', 'orphan']);
        $sponsor = $sponsorship->sponsor;
        $user = $sponsor?->user;

        if (!$user) {
            return;
        }

        if ($sponsorship->status === 'active') {
            $user->notify(new SponsorshipStatusNotification(
                title: 'Sponsorship Approved',
                body: 'Your sponsorship request for orphan ' . $sponsorship->orphan->name . ' has been approved.',
                status: 'approved',
                orphanName: $sponsorship->orphan->name,
            ));
        } elseif ($sponsorship->status === 'stopped') {
            $user->notify(new SponsorshipStatusNotification(
                title: 'Sponsorship Rejected',
                body: 'Your sponsorship request for orphan ' . $sponsorship->orphan->name . ' has been rejected.',
                status: 'rejected',
                orphanName: $sponsorship->orphan->name,
            ));
        }
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
