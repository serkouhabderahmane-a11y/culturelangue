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
            ['key' => 'site_description', 'value' => 'Formation linguistique & PrÃ©paration aux examens', 'type' => 'text', 'group' => 'general'],
            ['key' => 'hero_badge', 'value' => 'Ã‰cole certifiÃ©e â€” +950 Ã©tudiants', 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_title', 'value' => 'Chez CultuLangues, nous construisons<br>votre rÃ©ussite et donnons un nouvel<br>Ã©lan Ã  vos <span class="text-gradient">projets</span>.', 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_subtitle', 'value' => "Vous souhaitez apprendre l'une des langues officielles du Canada pour :", 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_cta_primary', 'value' => "DÃ©couvrez nos parcours d'apprentissage", 'type' => 'text', 'group' => 'home'],
            ['key' => 'hero_cta_secondary', 'value' => 'Choisissez le format qui vous ressemble', 'type' => 'text', 'group' => 'home'],
            ['key' => 'address', 'value' => '468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'email', 'value' => 'admin@cultulangues.ca', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'phone', 'value' => '873-973-0513', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'google_rating', 'value' => '4.9', 'type' => 'text', 'group' => 'home'],
            ['key' => 'cta_title', 'value' => 'PrÃªt Ã  commencer votre parcours ?', 'type' => 'text', 'group' => 'home'],
            ['key' => 'cta_description', 'value' => "Rejoignez plus de 950 Ã©tudiants qui nous font confiance pour leur prÃ©paration linguistique.", 'type' => 'text', 'group' => 'home'],
            ['key' => 'cta_button_text', 'value' => 'CrÃ©er mon compte gratuit', 'type' => 'text', 'group' => 'home'],
        ];

        foreach ($settings as $data) {
            Setting::updateOrCreate(['key' => $data['key']], $data);
        }
    }
}
