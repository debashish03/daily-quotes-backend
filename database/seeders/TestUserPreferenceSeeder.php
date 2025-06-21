<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;

class TestUserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $notificationTimes = ['08:00', '12:00', '18:00', '20:00'];
        $categories = [1, 2, 3, 4, 5]; // Assuming we have 5 categories

        foreach ($users as $index => $user) {
            // Create preferences for 70% of users
            if ($index < count($users) * 0.7) {
                UserPreference::create([
                    'user_id' => $user->id,
                    'notifications_enabled' => rand(0, 1),
                    'notification_time' => $notificationTimes[array_rand($notificationTimes)],
                    'preferred_categories' => array_rand(array_flip($categories), rand(1, 3)),
                    'device_token' => $index % 3 == 0 ? 'device_token_' . $user->id : null, // 33% have device tokens
                    'created_at' => $user->created_at->addHours(rand(1, 24)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
