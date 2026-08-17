<?php

namespace Database\Seeders;

use App\Models\Inyandiko;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InyandikoSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => "Imam Shafi'i", 'category' => 'Imam', 'author' => "Imam wamenyekanye cyane mu fiqh ya Shafi'i.", 'summary' => "Imam wamenyekanye cyane mu fiqh ya Shafi'i."],
            ['title' => 'Imam Malik ibn Anas', 'category' => 'Imam', 'author' => 'Umushinze madhhab ya Maliki mu Hadith.', 'summary' => 'Umushinze madhhab ya Maliki mu Hadith.'],
            ['title' => 'Imam Ahmad ibn Hanbal', 'category' => 'Imam', 'author' => 'Uzwi ku kwishingikiriza kuri Hadith.', 'summary' => 'Uzwi ku kwishingikiriza kuri Hadith.'],
            ['title' => 'Sheikh Ibn Baaz', 'category' => 'Sheikh', 'author' => 'Sheikh uzwi ku fatwa n\'inyigisho za Tawhid.', 'summary' => 'Sheikh uzwi ku fatwa n\'inyigisho za Tawhid.'],
            ['title' => 'Ibn al-Qayyim', 'category' => 'Scholar', 'author' => 'Umunyeshuri wa Ibn Taymiyyah.', 'summary' => 'Umunyeshuri wa Ibn Taymiyyah.'],
        ];

        foreach ($items as $item) {
            Inyandiko::firstOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title'        => $item['title'],
                    'category'     => $item['category'],
                    'author'       => $item['author'],
                    'summary'      => $item['summary'],
                    'content'      => $item['summary'],
                    'status'       => 'published',
                    'published_at' => now(),
                ]
            );
        }
    }
}
