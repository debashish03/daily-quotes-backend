<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing quotes to avoid duplicates
        Quote::query()->delete();

        $categories = QuoteCategory::all();

        foreach ($categories as $category) {
            Quote::factory()->count(200)->create([
                'category_id' => $category->id
            ]);
        }
    }
}
