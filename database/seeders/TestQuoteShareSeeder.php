<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteShare;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestQuoteShareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = Quote::all();
        $users = User::where('is_admin', false)->get();
        $platforms = ['facebook', 'twitter', 'instagram', 'whatsapp', 'telegram'];

        foreach ($quotes as $quote) {
            // Generate random shares for each quote
            $shareCount = rand(0, 15);

            for ($i = 0; $i < $shareCount; $i++) {
                $user = $users->random();
                $platform = $platforms[array_rand($platforms)];

                QuoteShare::create([
                    'user_id' => $user->id,
                    'quote_id' => $quote->id,
                    'platform' => $platform,
                    'shared_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    'created_at' => now()->subDays(rand(0, 30)),
                    'updated_at' => now(),
                ]);
            }

            // Update quote view and share counts
            $quote->update([
                'view_count' => rand(50, 500),
                'share_count' => $shareCount,
            ]);
        }
    }
}
