<?php

namespace App\Console\Commands;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SyncSponsorsToUsers extends Command
{
    protected $signature = 'sponsors:sync-to-users';

    protected $description = 'Sync all Sponsor records to their corresponding User records';

    public function handle()
    {
        $sponsors = Sponsor::all();
        $count = 0;

        foreach ($sponsors as $sponsor) {
            if ($sponsor->user) {
                $user = $sponsor->user;
                $user->update([
                    'phone' => $user->phone ?: $sponsor->phone,
                    'address' => $user->address ?: $sponsor->address,
                ]);
                $this->info("Updated User #{$user->id} ({$user->email}) with sponsor data");
            } else {
                $password = \Illuminate\Support\Str::random(12);
                $user = User::create([
                    'name' => $sponsor->name,
                    'email' => $sponsor->email,
                    'phone' => $sponsor->phone,
                    'address' => $sponsor->address,
                    'password' => Hash::make($password),
                    'role' => 'sponsor',
                    'sponsor_id' => $sponsor->id,
                ]);
                $user->assignRole('sponsor');
                $sponsor->update(['user_id' => $user->id]);
                $this->info("Created User #{$user->id} for Sponsor #{$sponsor->id} ({$sponsor->email})");
            }
            $count++;
        }

        $this->info("Synced {$count} sponsor(s) to users table.");
        return Command::SUCCESS;
    }
}
