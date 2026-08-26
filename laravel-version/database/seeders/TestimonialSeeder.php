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
                'name_fr' => 'Maria S.',
                'name_en' => 'Maria S.',
                'role_fr' => 'TCF Québec — Niveau B2',
                'role_en' => 'TCF Québec — Level B2',
                'content_fr' => "Grâce à Cultulangues, j'ai obtenu mon TCF Québec avec mention B2. L'accompagnement personnalisé et les simulations d'examen m'ont vraiment préparée.",
                'content_en' => "Thanks to Cultulangues, I obtained my TCF Québec with a B2 distinction. The personalized support and exam simulations really prepared me.",
                'rating' => 5,
                'order' => 1,
            ],
            [
                'name_fr' => 'Ahmed K.',
                'name_en' => 'Ahmed K.',
                'role_fr' => 'Préparation Orale — Niveau C1',
                'role_en' => 'Oral Preparation — Level C1',
                'content_fr' => "Une équipe formidable qui m'a aidé à reprendre confiance en mon français oral. Les ateliers de conversation ont été une vraie révélation.",
                'content_en' => "A wonderful team that helped me regain confidence in my spoken French. The conversation workshops were a real revelation.",
                'rating' => 5,
                'order' => 2,
            ],
            [
                'name_fr' => 'Laura P.',
                'name_en' => 'Laura P.',
                'role_fr' => 'TCF Canada — Niveau C1',
                'role_en' => 'TCF Canada — Level C1',
                'content_fr' => "Je recommande sans hésiter. La plateforme est claire, les cours sont bien structurés et les professeurs sont à l'écoute. Exactement ce qu'il me fallait.",
                'content_en' => "I recommend without hesitation. The platform is clear, the courses are well structured and the teachers are attentive. Exactly what I needed.",
                'rating' => 5,
                'order' => 3,
            ],
        ];

        Testimonial::query()->delete();

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }
    }
}
