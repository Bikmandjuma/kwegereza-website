<?php

namespace Database\Seeders;

use App\Models\ChatQuestion;
use Illuminate\Database\Seeder;

class ChatQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'question' => "Islam ni iki?",
                'answer'   => "Islam ni idini rishingiye ku kwemera Imana imwe rukumbi (Allah), rikaba rishingiye kuri Qur'an na Sunnah y'Intumwa Muhammad (SAW).",
                'category' => 'Ibanze',
                'keywords' => 'islam, idini, imana, allah, qur\'an, sunnah',
            ],
            [
                'question' => "Nabatizwa nte muri Islam?",
                'answer'   => "Kwinjira muri Islam bisaba kuvuga Shahada (icyemezo cy'ukwemera) mu mutima wuzuye. Kubona ubufasha bwimbitse, vugana n'umwe mu Bayobozi b'Idini bacu binyuze muri 'Twandikire'.",
                'category' => 'Ibanze',
                'keywords' => 'kwinjira, shahada, kubatizwa, islam',
            ],
            [
                'question' => "Nabona nte amasomo (Darsat)?",
                'answer'   => "Amasomo yose (Darsat) uyasanga ku rubuga rwacu munsi ya menu 'Amasomo'. Buri somo rigaragaza umwigisha n'ibirimo.",
                'category' => 'Urubuga',
                'keywords' => 'amasomo, darsat, kwiga, isomo',
            ],
            [
                'question' => "Nabona nte ibitabo?",
                'answer'   => "Ibitabo biri ku rubuga rwacu munsi ya 'Ibitabo'. Ushobora gusoma cyangwa gukurura (download) ibitabo bimwe, bitewe n'uburenganzira wahawe.",
                'category' => 'Urubuga',
                'keywords' => 'ibitabo, gusoma, gukurura, download, book',
            ],
            [
                'question' => "Nagera nte ku muyobozi w'idini?",
                'answer'   => "Kanda 'Twandikire' hejuru ku rubuga, uzafungura ikiganiro n'Abayobozi b'Idini bacu bemewe, ushobora kubaza ibibazo byawe hafi ako kanya.",
                'category' => 'Ubufasha',
                'keywords' => 'twandikire, umuyobozi, sheikh, ubufasha, contact',
            ],
        ];

        foreach ($items as $item) {
            ChatQuestion::firstOrCreate(
                ['question' => $item['question']],
                [
                    'answer'   => $item['answer'],
                    'category' => $item['category'],
                    'keywords' => $item['keywords'],
                    'language' => 'rw',
                    'status'   => 'active',
                ]
            );
        }
    }
}
