<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role_name' => 'Super Admin',
                'description' => 'Super Admin has full system access',
                'role_color' => '#DC2626',
                'role_hover_color' => '#B91C1C',
            ],
            [
                'role_name' => 'student',
                'description' => 'Student role',
                'role_color' => '#1F8BFF',
                'role_hover_color' => '#1A7AE6',
            ],
            [
                'role_name' => 'university_admin',
                'description' => 'University Administrator',
                'role_color' => '#16A34A',
                'role_hover_color' => '#15803D',
            ],
            [
                'role_name' => 'college_admin',
                'description' => 'College Administrator',
                'role_color' => '#F59E0B',
                'role_hover_color' => '#D97706',
            ],
            [
                'role_name' => 'faculty',
                'description' => 'Faculty member',
                'role_color' => '#7C3AED',
                'role_hover_color' => '#6D28D9',
            ],
            [
                'role_name' => 'account',
                'description' => 'Account role',
                'role_color' => '#EF4444',
                'role_hover_color' => '#DC2626',
            ],
            [
                'role_name' => 'general_user',
                'description' => 'General User',
                'role_color' => '#64748B',
                'role_hover_color' => '#475569',
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::where('role_name', $roleData['role_name'])->first();
            
            if ($role) {
                // Update existing role with colors
                $role->update([
                    'role_color' => $roleData['role_color'],
                    'role_hover_color' => $roleData['role_hover_color'],
                ]);
            } else {
                // Create new role if it doesn't exist
                Role::create($roleData);
            }
        }
    }
}
