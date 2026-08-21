<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question_fr' => 'Quels sont les prérequis pour suivre un cours ?',
                'question_en' => 'What are the prerequisites for taking a course?',
                'answer_fr' => 'Nos cours sont adaptés à tous les niveaux, du débutant à l\'avancé. Un test de niveau gratuit vous sera proposé avant de commencer pour déterminer le programme le mieux adapté à vos besoins.',
                'answer_en' => 'Our courses are suitable for all levels, from beginner to advanced. A free level test will be offered before starting to determine the most suitable program for your needs.',
            ],
            [
                'question_fr' => 'Quelle est la taille des groupes ?',
                'question_en' => 'What is the group size?',
                'answer_fr' => 'Nos groupes sont limités à 5 participants maximum pour garantir un accompagnement personnalisé et des échanges de qualité.',
                'answer_en' => 'Our groups are limited to a maximum of 5 participants to guarantee personalized support and quality exchanges.',
            ],
            [
                'question_fr' => 'Proposez-vous des cours en ligne ?',
                'question_en' => 'Do you offer online courses?',
                'answer_fr' => 'Oui, nous proposons des cours en ligne via des plateformes interactives pour ceux qui préfèrent apprendre à distance.',
                'answer_en' => 'Yes, we offer online courses via interactive platforms for those who prefer to learn remotely.',
            ],
            [
                'question_fr' => 'Comment puis-je m\'inscrire ?',
                'question_en' => 'How can I register?',
                'answer_fr' => 'Vous pouvez vous inscrire directement via notre formulaire d\'inscription en ligne ou nous contacter par email pour plus d\'informations.',
                'answer_en' => 'You can register directly through our online registration form or contact us by email for more information.',
            ],
            [
                'question_fr' => 'Quels sont les modes de paiement acceptés ?',
                'question_en' => 'What payment methods are accepted?',
                'answer_fr' => 'Nous acceptons les virements bancaires, les cartes de crédit et les paiements en ligne sécurisés.',
                'answer_en' => 'We accept bank transfers, credit cards and secure online payments.',
            ],
        ];

        foreach ($faqs as $data) {
            Faq::create($data);
        }
    }
}
