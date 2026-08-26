<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title_fr' => 'À propos',
                'title_en' => 'About',
                'content_fr' => "<p>Bienvenue à l'Académie Internationale Cultulangues, votre partenaire de confiance pour l'apprentissage des langues à Gatineau, Québec.</p><p>Fondée il y a plus de 12 ans, notre académie a accompagné plus de 950 étudiants dans leur parcours linguistique. Nous offrons des cours de français et d'anglais adaptés à tous les niveaux, ainsi qu'une préparation spécialisée aux examens TCF Québec et TCF Canada.</p>",
                'content_en' => "<p>Welcome to Académie Internationale Cultulangues, your trusted partner for language learning in Gatineau, Quebec.</p><p>Founded over 12 years ago, our academy has accompanied more than 950 students in their language journey. We offer French and English courses adapted to all levels, as well as specialized preparation for TCF Québec and TCF Canada exams.</p>",
                'template' => 'default',
            ],
            [
                'slug' => 'contact',
                'title_fr' => 'Contact',
                'title_en' => 'Contact',
                'content_fr' => "<p>Contactez-nous pour plus d'informations sur nos programmes de formation linguistique.</p><p>Adresse : 468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)</p><p>Email : admin@cultulangues.ca</p>",
                'content_en' => "<p>Contact us for more information about our language training programs.</p><p>Address: 468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)</p><p>Email: admin@cultulangues.ca</p>",
                'template' => 'contact',
            ],
            [
                'slug' => 'programs',
                'title_fr' => 'Nos programmes',
                'title_en' => 'Our programs',
                'content_fr' => "<p>Découvrez l'ensemble de nos programmes de formation linguistique.</p>",
                'content_en' => "<p>Discover all our language training programs.</p>",
                'template' => 'programs',
            ],
            [
                'slug' => 'privacy',
                'title_fr' => 'Politique de confidentialité',
                'title_en' => 'Privacy Policy',
                'content_fr' => "<p>Notre politique de confidentialité décrit comment nous collectons, utilisons et protégeons vos informations personnelles.</p>",
                'content_en' => "<p>Our privacy policy describes how we collect, use and protect your personal information.</p>",
                'template' => 'default',
            ],
            [
                'slug' => 'tcf-preparation',
                'title_fr' => 'Préparation TCF',
                'title_en' => 'TCF Preparation',
                'content_fr' => "<p>Préparez efficacement les examens TCF Québec et TCF Canada avec nos programmes spécialisés.</p>",
                'content_en' => "<p>Effectively prepare for TCF Québec and TCF Canada exams with our specialized programs.</p>",
                'template' => 'default',
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
