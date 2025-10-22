<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        Staff::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@chart.local',
            'password' => Hash::make('password'),
            'active' => true,
            'admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create regular staff user
        Staff::create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'staff@chart.local',
            'password' => Hash::make('password'),
            'active' => true,
            'admin' => false,
            'email_verified_at' => now(),
        ]);

        // Create another staff user
        Staff::create([
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'jane@chart.local',
            'password' => Hash::make('password'),
            'active' => true,
            'admin' => false,
            'email_verified_at' => now(),
        ]);
    }
}
