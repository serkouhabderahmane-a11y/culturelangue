<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Cultulangues', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Formation linguistique & Préparation aux examens', 'type' => 'text', 'group' => 'general'],
            ['key' => 'hero_badge', 'value' => 'École certifiée - +950 étudiants', 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_title', 'value' => 'Chez CultuLangues, nous construisons votre réussite et donnons un nouvel élan à vos projets.', 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_subtitle', 'value' => 'Vous souhaitez apprendre l\'une des langues officielles du Canada pour :', 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_cta_primary', 'value' => 'Découvrez nos parcours d\'apprentissage', 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_cta_secondary', 'value' => 'Choisissez le format qui vous ressemble', 'type' => 'text', 'group' => 'home'],
            ['key' => 'address', 'value' => '468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'email', 'value' => 'Admin@cultulangues.ca', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'phone', 'value' => 'à venir', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'google_rating', 'value' => '4.9', 'type' => 'text', 'group' => 'home'],
        ];

        foreach ($settings as $data) {
            Setting::create($data);
        }
    }
}
