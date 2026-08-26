<?php

namespace Database\Seeders;

use App\Models\NavigationItem;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'label_fr' => 'Accueil',
                'label_en' => 'Home',
                'route' => 'home',
                'order' => 1,
            ],
            [
                'label_fr' => 'Programmes',
                'label_en' => 'Programs',
                'order' => 2,
            ],
            [
                'label_fr' => 'Parcours linguistique',
                'label_en' => 'Linguistic Pathway',
                'description_fr' => 'Apprendre et progresser en petit groupe',
                'route' => 'services.category',
                'url' => '/services/category/parcours-linguistique',
                'parent_id' => 2,
                'order' => 1,
            ],
            [
                'label_fr' => 'English Linguistic Pathway',
                'label_en' => 'English Linguistic Pathway',
                'description_fr' => 'English courses designed to help you make real progress',
                'route' => 'services.category',
                'url' => '/services/category/english-linguistic-pathway',
                'parent_id' => 2,
                'order' => 2,
            ],
            [
                'label_fr' => 'Cap sur l\'oral',
                'label_en' => 'Oral Focus',
                'description_fr' => 'Développer une expression orale fluide et stratégique',
                'route' => 'services.category',
                'url' => '/services/category/cap-sur-l-oral',
                'parent_id' => 2,
                'order' => 3,
            ],
            [
                'label_fr' => 'TCF Québec',
                'label_en' => 'TCF Québec',
                'description_fr' => 'Préparation au TCF Québec pour l\'immigration',
                'route' => 'services.category',
                'url' => '/services/category/tcf-quebec',
                'parent_id' => 2,
                'order' => 4,
            ],
            [
                'label_fr' => 'TCF Canada',
                'label_en' => 'TCF Canada',
                'description_fr' => 'Préparation au TCF Canada pour l\'immigration IRCC',
                'route' => 'services.category',
                'url' => '/services/category/tcf-canada',
                'parent_id' => 2,
                'order' => 5,
            ],
            [
                'label_fr' => 'Formation en solo',
                'label_en' => 'Private Lessons',
                'description_fr' => 'Un accompagnement 100 % personnalisé',
                'route' => 'services.category',
                'url' => '/services/category/formation-en-solo',
                'parent_id' => 2,
                'order' => 6,
            ],
            [
                'label_fr' => 'Ateliers',
                'label_en' => 'Workshops',
                'description_fr' => 'Pratiquer, échanger et perfectionner votre français',
                'route' => 'services.category',
                'url' => '/services/category/ateliers',
                'parent_id' => 2,
                'order' => 7,
            ],
            [
                'label_fr' => 'À propos',
                'label_en' => 'About',
                'route' => 'page.show',
                'url' => '/pages/about',
                'order' => 3,
            ],
            [
                'label_fr' => 'Contact',
                'label_en' => 'Contact',
                'route' => 'contact',
                'url' => '/contact',
                'order' => 4,
            ],
        ];

        foreach ($items as $data) {
            NavigationItem::updateOrCreate(['label_fr' => $data['label_fr']], $data);
        }
    }
}
