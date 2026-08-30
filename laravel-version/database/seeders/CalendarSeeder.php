<?php

namespace Database\Seeders;

use App\Services\CalendarDataService;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    /**
     * Import the programmes calendar exactly as described in the DOCX
     * "Calendrier à insérer dans la plateforme.docx".
     */
    public function run(): void
    {
        $counts = CalendarDataService::import(reset: true);

        $this->command?->info(sprintf(
            'Calendrier importé : %d programmes, %d sessions, %d cours/ateliers.',
            $counts['programs'],
            $counts['sessions'],
            $counts['meetings']
        ));
    }
}
