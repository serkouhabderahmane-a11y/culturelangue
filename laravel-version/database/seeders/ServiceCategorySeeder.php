<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'parcours-linguistique',
                'name_fr' => 'Parcours linguistique',
                'name_en' => 'Linguistic Pathway',
                'description_fr' => 'Cours de groupe en petit groupe pour progresser avec confiance',
                'description_en' => 'Group courses in small groups to progress with confidence',
                'image' => 'img/hero-parcours-linguistiques.png',
                'order' => 1,
            ],
            [
                'slug' => 'english-linguistic-pathway',
                'name_fr' => 'English Linguistic Pathway',
                'name_en' => 'English Linguistic Pathway',
                'description_fr' => 'Cours d\'anglais en petit groupe pour progresser avec confiance',
                'description_en' => 'Join a program designed to help you make real progress in a motivating and stimulating environment',
                'image' => 'img/hero-english.png',
                'order' => 2,
            ],
            [
                'slug' => 'cap-sur-l-oral',
                'name_fr' => 'Cap sur l\'oral',
                'name_en' => 'Oral Focus',
                'description_fr' => 'Maîtrisez l\'expression orale avec des parcours collaboratifs',
                'description_en' => 'Master oral expression with collaborative pathways',
                'image' => 'img/home/banner-linguotest.png',
                'order' => 3,
            ],
            [
                'slug' => 'tcf-quebec',
                'name_fr' => 'TCF Québec',
                'name_en' => 'TCF Québec',
                'description_fr' => 'Préparation au TCF Québec pour l\'immigration',
                'description_en' => 'TCF Québec preparation for immigration',
                'image' => 'img/hero-tcf-quebec.png',
                'order' => 4,
            ],
            [
                'slug' => 'tcf-canada',
                'name_fr' => 'TCF Canada',
                'name_en' => 'TCF Canada',
                'description_fr' => 'Préparation au TCF Canada pour l\'immigration IRCC',
                'description_en' => 'TCF Canada preparation for IRCC immigration',
                'image' => 'img/hero-tcf-canada.png',
                'order' => 5,
            ],
            [
                'slug' => 'formation-en-solo',
                'name_fr' => 'Formation en solo',
                'name_en' => 'Private Lessons',
                'description_fr' => 'Accompagnement 1-to-1 flexible et 100% personnalisé',
                'description_en' => 'Flexible 1-to-1 support, 100% personalized',
                'image' => 'img/hero-maitrisez-langues.png',
                'order' => 6,
            ],
            [
                'slug' => 'ateliers',
                'name_fr' => 'Ateliers',
                'name_en' => 'Workshops',
                'description_fr' => 'Ateliers thématiques pour pratiquer et échanger en groupe',
                'description_en' => 'Thematic workshops to practice and exchange in a group',
                'image' => 'img/atelier-culture-canada.png',
                'order' => 7,
            ],
        ];

        foreach ($categories as $data) {
            ServiceCategory::create($data);
        }
    }
}
