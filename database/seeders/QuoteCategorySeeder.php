<?php

namespace Database\Seeders;

use App\Models\QuoteCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuoteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Motivational',
                'description' => 'Quotes that inspire and motivate people to achieve their goals',
                'color' => '#667eea',
                'is_active' => true
            ],
            [
                'name' => 'Inspirational',
                'description' => 'Quotes that inspire creativity and positive thinking',
                'color' => '#764ba2',
                'is_active' => true
            ],
            [
                'name' => 'Life',
                'description' => 'Quotes about life lessons and wisdom',
                'color' => '#f093fb',
                'is_active' => true
            ],
            [
                'name' => 'Success',
                'description' => 'Quotes about achieving success and overcoming challenges',
                'color' => '#f5576c',
                'is_active' => true
            ],
            [
                'name' => 'Love',
                'description' => 'Quotes about love, relationships, and emotions',
                'color' => '#4facfe',
                'is_active' => true
            ],
            [
                'name' => 'Wisdom',
                'description' => 'Ancient wisdom and philosophical quotes',
                'color' => '#00f2fe',
                'is_active' => true
            ],
            [
                'name' => 'Leadership',
                'description' => 'Quotes about leadership and management',
                'color' => '#43e97b',
                'is_active' => true
            ],
            [
                'name' => 'Happiness',
                'description' => 'Quotes about finding happiness and joy in life',
                'color' => '#38f9d7',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            QuoteCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
                'is_active' => $category['is_active']
            ]);
        }
    }
}
