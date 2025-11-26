<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class SyncUserRolesToSpatie extends Command
{
    protected $signature = 'permissions:sync-user-roles';
    protected $description = 'Sync existing user role_id assignments to Spatie model_has_roles table';

    public function handle()
    {
        $this->info('Syncing user roles to Spatie...');

        $users = User::whereNotNull('role_id')->with('role')->get();
        $syncedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            if (!$user->role) {
                $this->warn("User {$user->email} has invalid role_id: {$user->role_id}");
                $skippedCount++;
                continue;
            }

            // Get Spatie role by name
            $spatieRole = Role::where('name', $user->role->name)
                ->orWhere('name', $user->role->role_name)
                ->first();

            if (!$spatieRole) {
                $this->warn("Spatie role not found for: {$user->role->role_name}");
                $skippedCount++;
                continue;
            }

            // Check if user already has this role in Spatie
            if (!$user->hasRole($spatieRole)) {
                $user->assignRole($spatieRole);
                $this->line("Assigned role '{$spatieRole->name}' to user: {$user->email}");
                $syncedCount++;
            } else {
                $this->comment("User {$user->email} already has role '{$spatieRole->name}'");
            }
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->info("Sync complete! Synced: {$syncedCount}, Skipped: {$skippedCount}");
    }
}

