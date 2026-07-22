<?php

namespace App\Filament\Resources\SponsorshipResource\Pages;

use App\Filament\Resources\SponsorshipResource;
use App\Notifications\SponsorshipStatusNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateSponsorship extends CreateRecord
{
    protected static string $resource = SponsorshipResource::class;

    protected function afterCreate(): void
    {
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
