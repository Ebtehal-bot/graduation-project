<?php

namespace App\Filament\Resources\SponsorResource\Pages;

use App\Filament\Resources\SponsorResource;
use App\Models\Sponsor;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSponsor extends EditRecord
{
    protected static string $resource = SponsorResource::class;

    protected function afterSave(): void
    {
        $user = $this->record;

        if ($user->sponsor) {
            $user->sponsor->update([
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'address' => $user->address,
            ]);
        } else {
            $sponsor = Sponsor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone ?? '',
                'email' => $user->email,
                'address' => $user->address ?? '',
            ]);

            $user->update(['sponsor_id' => $sponsor->id]);
        }
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
