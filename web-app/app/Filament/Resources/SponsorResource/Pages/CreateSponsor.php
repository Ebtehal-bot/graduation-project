<?php

namespace App\Filament\Resources\SponsorResource\Pages;

use App\Filament\Resources\SponsorResource;
use App\Models\Sponsor;
use App\Models\User;
use App\Notifications\SponsorNotification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateSponsor extends CreateRecord
{
    protected static string $resource = SponsorResource::class;

    protected string $createdPassword = '';

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $this->createdPassword = \Illuminate\Support\Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'password' => Hash::make($this->createdPassword),
            'role' => 'sponsor',
        ]);

        $user->assignRole('sponsor');

        $sponsor = Sponsor::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
        ]);

        $user->update(['sponsor_id' => $sponsor->id]);

        return $user;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

        $admin = auth()->user();
        if ($admin) {
            $admin->notify(new SponsorNotification(
                title: 'New Sponsor Account Created',
                body: "Sponsor: {$user->name}\nEmail: {$user->email}\nPassword: {$this->createdPassword}",
                type: 'system',
            ));
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
