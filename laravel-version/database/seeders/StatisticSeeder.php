<?php

namespace Database\Seeders;

use App\Models\Statistic;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    public function run(): void
    {
        $statistics = [
            [
                'label_fr' => "Années d'expérience",
                'label_en' => 'Years of experience',
                'value' => '12',
                'suffix_fr' => '',
                'suffix_en' => '',
            ],
            [
                'label_fr' => 'de réussite aux examens',
                'label_en' => 'exam success rate',
                'value' => '98',
                'suffix_fr' => '%',
                'suffix_en' => '%',
            ],
            [
                'label_fr' => 'Étudiants accompagnés',
                'label_en' => 'Students accompanied',
                'value' => '+950',
                'suffix_fr' => '',
                'suffix_en' => '',
            ],
            [
                'label_fr' => 'Avis Google',
                'label_en' => 'Google rating',
                'value' => '4.9',
                'suffix_fr' => '*',
                'suffix_en' => '*',
            ],
        ];

        foreach ($statistics as $data) {
            Statistic::create($data);
        }
    }
}
