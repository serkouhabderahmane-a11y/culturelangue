<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::role('teacher')->first()
            ?? User::where('email', 'teacher@cultulangues.ca')->first();

        $schedule = [
            ['service' => 'francais-express', 'title' => 'Français Express — Séance 1', 'offset' => 1, 'start' => '17:00', 'end' => '20:00', 'room' => 'A-101', 'type' => 'class'],
            ['service' => 'francais-express', 'title' => 'Français Express — Séance 2', 'offset' => 3, 'start' => '17:00', 'end' => '20:00', 'room' => 'A-101', 'type' => 'class'],
            ['service' => 'soiree-linguo', 'title' => 'Soirée Linguo — Cours hebdomadaire', 'offset' => 2, 'start' => '17:00', 'end' => '20:00', 'room' => 'B-204', 'type' => 'class'],
            ['service' => 'samedis-en-francais', 'title' => 'Samedis en français — Matinée', 'offset' => 6, 'start' => '09:00', 'end' => '12:00', 'room' => 'C-310', 'type' => 'class'],
            ['service' => 'oral-b-partiel', 'title' => "Cap sur l'oral B — Atelier de conversation", 'offset' => 4, 'start' => '18:30', 'end' => '20:30', 'room' => 'B-110', 'type' => 'practice'],
            ['service' => 'oral-c-partiel', 'title' => "Cap sur l'oral C — Débat et oral avancé", 'offset' => 5, 'start' => '18:30', 'end' => '20:30', 'room' => 'B-112', 'type' => 'practice'],
            ['service' => 'tcf-quebec-partiel', 'title' => 'TCF Québec — Préparation en groupe', 'offset' => 7, 'start' => '17:00', 'end' => '20:00', 'room' => 'D-401', 'type' => 'exam-prep'],
            ['service' => 'atelier-conversation', 'title' => 'Atelier de conversation — Thème de la semaine', 'offset' => 2, 'start' => '12:00', 'end' => '14:00', 'room' => 'Salle Atelier 1', 'type' => 'workshop'],
        ];

        foreach ($schedule as $item) {
            $service = Service::where('slug', $item['service'])->first();

            if (! $service) {
                continue;
            }

            $date = now()->addDays($item['offset'])->toDateString();

            Lesson::updateOrCreate(
                ['service_id' => $service->id, 'title' => $item['title'], 'date' => $date],
                [
                    'teacher_id' => $teacher?->id,
                    'start_time' => $item['start'],
                    'end_time' => $item['end'],
                    'room' => $item['room'],
                    'lesson_type' => $item['type'],
                    'status' => 'scheduled',
                    'notes' => 'Séance de groupe planifiée pour ' . $service->name_fr . '.',
                ],
            );
        }
    }
}
