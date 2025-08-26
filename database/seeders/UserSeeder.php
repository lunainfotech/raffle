<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;
use Spatie\Permission\Models\Role;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Ensure the admin role exists
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Define your default users:
        $users = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => 'Admin@123',      // plaintext here; we'll hash below
                'role'     => 'admin',          // optional, if you're assigning roles
            ]
            // add more users as needed...
        ];

        foreach ($users as $data) {
            // firstOrCreate ensures we only insert once per email
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );

            // If using roles (Spatie/Backpack), assign them here:
            if (isset($data['role']) && method_exists($user, 'assignRole')) {
                $user->assignRole($data['role']);
            }
        }

        $this->command->info('✅  Default users have been seeded.');
    }
}
