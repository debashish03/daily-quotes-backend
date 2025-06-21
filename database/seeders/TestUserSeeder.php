<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(30),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(25),
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(20),
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(15),
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(10),
            ],
            [
                'name' => 'Lisa Davis',
                'email' => 'lisa@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(5),
            ],
            [
                'name' => 'Tom Miller',
                'email' => 'tom@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(3),
            ],
            [
                'name' => 'Emma Garcia',
                'email' => 'emma@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'created_at' => now()->subDays(1),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
