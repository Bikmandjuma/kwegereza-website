<?php

namespace Database\Seeders;

use App\Models\Amatangazo;
use Illuminate\Database\Seeder;

class AmatangazoSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Hadith', 'presenter' => 'Sheikh ABOUBAKAR', 'status' => 'live', 'description' => "Isomo rya Hadith rikomeje ubu."],
            ['title' => 'Tawhid', 'presenter' => 'Aqida lesson', 'status' => 'upcoming', 'description' => "Isomo ry'Aqida rizatangira vuba."],
            ['title' => "Fiqh Salah", 'presenter' => 'Isengesho', 'status' => 'upcoming', 'description' => "Amabwiriza y'isengesho mu buryo bwuzuye."],
            ['title' => "Qur'an", 'presenter' => 'Sheikh Ndahayo Halid', 'status' => 'done', 'description' => "Isomo ryarangiye neza."],
            ["title" => "Shafi'i", 'presenter' => 'Fiqh class', 'status' => 'done', 'description' => "Icyiciro cya Fiqh cyarangiye."],
        ];

        foreach ($items as $item) {
            Amatangazo::firstOrCreate(
                ['title' => $item['title']],
                [
                    'presenter'    => $item['presenter'],
                    'description'  => $item['description'],
                    'status'       => $item['status'],
                    'is_published' => true,
                    'published_at' => now(),
                ]
            );
        }
    }
}
