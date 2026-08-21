<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name_fr' => 'Sophie L.',
                'name_en' => 'Sophie L.',
                'role_fr' => 'Étudiante du programme Français Express',
                'role_en' => 'Student of Français Express',
                'content_fr' => "Grâce à Cultulangues, j'ai pu améliorer mon français rapidement. Les cours en petit groupe m'ont permis de pratiquer et de gagner en confiance.",
                'content_en' => "Thanks to Cultulangues, I was able to improve my French quickly. The small group classes allowed me to practice and gain confidence.",
                'rating' => 5,
            ],
            [
                'name_fr' => 'Marc T.',
                'name_en' => 'Marc T.',
                'role_fr' => 'Étudiant du programme TCF Québec',
                'role_en' => 'Student of TCF Québec program',
                'content_fr' => "La préparation au TCF Québec était très complète. J'ai obtenu le niveau requis pour mon immigration grâce à l'accompagnement personnalisé.",
                'content_en' => "The TCF Québec preparation was very thorough. I achieved the required level for my immigration thanks to the personalized support.",
                'rating' => 5,
            ],
            [
                'name_fr' => 'Julie R.',
                'name_en' => 'Julie R.',
                'role_fr' => 'Étudiante des ateliers de conversation',
                'role_en' => 'Workshop student',
                'content_fr' => "Les ateliers de conversation sont formidables ! J'ai rencontré des gens formidables et j'ai amélioré mon français oral dans une ambiance détendue.",
                'content_en' => "The conversation workshops are wonderful! I met great people and improved my oral French in a relaxed atmosphere.",
                'rating' => 5,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }
    }
}
