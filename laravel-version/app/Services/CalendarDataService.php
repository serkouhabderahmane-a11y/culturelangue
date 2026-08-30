<?php

namespace App\Services;

use App\Models\CalendarMeeting;
use App\Models\CalendarProgram;
use App\Models\CalendarSession;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Encodes the exact data from the DOCX "Calendrier à insérer dans la plateforme.docx"
 * (the single source of truth) and imports it into the database.
 *
 * - Programs map 1:1 to the platform's existing `services`.
 * - Sessions carry the date range, weekdays, times and duration from the document.
 * - Individual dated class/workshop meetings are generated from each session.
 */
class CalendarDataService
{
    /**
     * Full programme catalogue keyed by a stable internal id.
     */
    public static function data(): array
    {
        return [
            // ─────────────────────────── FRANÇAIS ───────────────────────────
            'francais-express' => [
                'name_fr' => 'Français Express',
                'name_en' => 'Français Express',
                'category' => 'Français Express',
                'language' => 'fr',
                'service' => 'francais-express',
                'color' => '#2f7d63',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-05', '2026-10-31', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(2, '2026-11-03', '2026-11-28', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(3, '2026-12-01', '2026-12-19', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::pause(), // Pause des fêtes 20 déc → 4 jan
                    self::hit(4, '2027-01-05', '2027-01-30', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(5, '2027-02-02', '2027-02-27', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(6, '2027-03-02', '2027-03-27', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(7, '2027-03-30', '2027-04-24', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(8, '2027-04-27', '2027-05-22', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                    self::hit(9, '2027-05-25', '2027-06-20', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 semaines'),
                ],
            ],
            'soiree-linguo' => [
                'name_fr' => 'Soirée Linguo',
                'name_en' => 'Soirée Linguo',
                'category' => 'Soirée Linguo',
                'language' => 'fr',
                'service' => 'soiree-linguo',
                'color' => '#5b8fb9',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-05', '2026-12-10', ['wed'], '17:00', '20:00', '10 semaines'),
                    self::pause(),
                    self::hit(2, '2027-01-07', '2027-03-11', ['wed'], '17:00', '20:00', '10 semaines'),
                    self::hit(3, '2027-03-18', '2027-05-20', ['wed'], '17:00', '20:00', '10 semaines'),
                ],
            ],
            'samedis-en-francais' => [
                'name_fr' => 'Samedis en français',
                'name_en' => 'Samedis en français',
                'category' => 'Samedis en français',
                'language' => 'fr',
                'service' => 'samedis-en-francais',
                'color' => '#b98a2f',
                'kind' => 'two-blocks',
                'sessions' => [
                    self::hit(1, '2026-10-11', '2026-11-08', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                    self::hit(2, '2026-11-15', '2026-12-13', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                    self::pause(),
                    self::hit(3, '2027-01-10', '2027-02-07', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                    self::hit(4, '2027-02-14', '2027-03-14', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                    self::hit(5, '2027-03-21', '2027-04-18', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                    self::hit(6, '2027-04-25', '2027-05-23', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                    self::hit(7, '2027-05-30', '2027-06-27', ['sat'], '09:00', '12:00', '5 semaines', '13:00', '16:00'),
                ],
            ],
            'oral-b-partiel' => [
                'name_fr' => 'Cap sur l’oral B – Temps partiel',
                'name_en' => 'Cap sur l’oral B – Part-time',
                'category' => 'Cap sur l’oral B',
                'language' => 'fr',
                'service' => 'oral-b-partiel',
                'color' => '#3e7d9e',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-07', '2026-12-11', ['tue','thu'], '18:00', '20:00', '10 semaines'),
                    self::pause(),
                    self::hit(2, '2027-01-06', '2027-03-13', ['tue','thu'], '18:00', '20:00', '10 semaines'),
                    self::hit(3, '2027-03-18', '2027-05-22', ['tue','thu'], '18:00', '20:00', '10 semaines', null, null, '10 semaines (40 h)'),
                    self::hit(4, '2027-05-27', '2027-06-30', ['tue','thu'], '18:00', '20:00', '5 semaines', null, null, '5 semaines (fin d’année)'),
                ],
            ],
            'oral-b-intensif' => [
                'name_fr' => 'Cap sur l’oral B – Intensif',
                'name_en' => 'Cap sur l’oral B – Intensive',
                'category' => 'Cap sur l’oral B',
                'language' => 'fr',
                'service' => 'oral-b-intensif',
                'color' => '#2c6e8c',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-05', '2026-10-31', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(2, '2026-11-03', '2026-12-19', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::pause(),
                    self::hit(3, '2027-01-05', '2027-01-30', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(4, '2027-02-02', '2027-02-27', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(5, '2027-03-02', '2027-03-27', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(6, '2027-03-30', '2027-04-24', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(7, '2027-04-27', '2027-05-22', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(8, '2027-05-25', '2027-06-20', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                ],
            ],
            'oral-c-partiel' => [
                'name_fr' => 'Cap sur l’oral C – Temps partiel',
                'name_en' => 'Cap sur l’oral C – Part-time',
                'category' => 'Cap sur l’oral C',
                'language' => 'fr',
                'service' => 'oral-c-partiel',
                'color' => '#7d3e9e',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-06', '2026-12-10', ['mon','wed'], '18:00', '20:00', '10 semaines'),
                    self::pause(),
                    self::hit(2, '2027-01-05', '2027-03-11', ['mon','wed'], '18:00', '20:00', '10 semaines'),
                    self::hit(3, '2027-03-16', '2027-05-20', ['mon','wed'], '18:00', '20:00', '10 semaines'),
                ],
            ],
            'oral-c-intensif' => [
                'name_fr' => 'Cap sur l’oral C – Intensif',
                'name_en' => 'Cap sur l’oral C – Intensive',
                'category' => 'Cap sur l’oral C',
                'language' => 'fr',
                'service' => 'oral-c-intensif',
                'color' => '#7d2e8c',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-05', '2026-10-31', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(2, '2026-11-03', '2026-12-19', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::pause(),
                    self::hit(3, '2027-01-05', '2027-01-30', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(4, '2027-02-02', '2027-02-27', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(5, '2027-03-02', '2027-03-27', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(6, '2027-03-30', '2027-04-24', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(7, '2027-04-27', '2027-05-22', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                    self::hit(8, '2027-05-25', '2027-06-20', ['mon','tue','wed','thu','fri'], '18:00', '20:00', '4 semaines'),
                ],
            ],
            'tcf-quebec-partiel' => [
                'name_fr' => 'TCF Québec – Temps partiel',
                'name_en' => 'TCF Québec – Part-time',
                'category' => 'TCF Québec',
                'language' => 'fr',
                'service' => 'tcf-quebec-partiel',
                'color' => '#c0392b',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-06', '2026-12-17', ['mon','wed'], '16:30', '18:00', '14 semaines'),
                    self::pause(),
                    self::hit(2, '2027-01-05', '2027-04-09', ['mon','wed'], '16:30', '18:00', '14 semaines'),
                    self::hit(3, '2027-04-13', '2027-07-16', ['mon','wed'], '16:30', '18:00', '14 semaines'),
                ],
            ],
            'tcf-quebec-intensif' => [
                'name_fr' => 'TCF Québec – Intensif',
                'name_en' => 'TCF Québec – Intensive',
                'category' => 'TCF Québec',
                'language' => 'fr',
                'service' => 'tcf-quebec-intensif',
                'color' => '#8e2b1a',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-07', '2026-11-21', ['tue','wed','thu','fri'], '16:30', '18:00', '7 semaines'),
                    self::pause(),
                    self::hit(2, '2027-01-06', '2027-02-20', ['tue','wed','thu','fri'], '16:30', '18:00', '7 semaines'),
                    self::hit(3, '2027-02-25', '2027-04-11', ['tue','wed','thu','fri'], '16:30', '18:00', '7 semaines'),
                    self::hit(4, '2027-04-15', '2027-05-30', ['tue','wed','thu','fri'], '16:30', '18:00', '7 semaines'),
                ],
            ],
            'tcf-canada-partiel' => [
                'name_fr' => 'TCF Canada – Temps partiel',
                'name_en' => 'TCF Canada – Part-time',
                'category' => 'TCF Canada',
                'language' => 'fr',
                'service' => 'tcf-canada-partiel',
                'color' => '#d35400',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2027-01-06', '2027-04-09', ['tue','thu'], '18:00', '19:30', '14 semaines'),
                    self::hit(2, '2027-04-14', '2027-07-14', ['tue','thu'], '18:00', '19:30', '14 semaines'),
                ],
            ],
            'tcf-canada-intensif' => [
                'name_fr' => 'TCF Canada – Intensif',
                'name_en' => 'TCF Canada – Intensive',
                'category' => 'TCF Canada',
                'language' => 'fr',
                'service' => 'tcf-canada-intensif',
                'color' => '#a04000',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-06', '2026-11-21', ['mon','tue','wed','thu'], '18:00', '19:30', '7 semaines'),
                    self::pause(),
                    self::hit(2, '2027-01-05', '2027-02-20', ['mon','tue','wed','thu'], '18:00', '19:30', '7 semaines'),
                    self::hit(3, '2027-02-24', '2027-04-10', ['mon','tue','wed','thu'], '18:00', '19:30', '7 semaines'),
                    self::hit(4, '2027-04-14', '2027-05-30', ['mon','tue','wed','thu'], '18:00', '19:30', '7 semaines'),
                ],
            ],

            // ─────────────────────────── ENGLISH ───────────────────────────
            'english-express' => [
                'name_fr' => 'English Express',
                'name_en' => 'English Express',
                'category' => 'English Express',
                'language' => 'en',
                'service' => 'english-express',
                'color' => '#1e88a5',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-05', '2026-10-31', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::hit(2, '2026-11-03', '2026-12-19', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::pause(),
                    self::hit(3, '2027-01-05', '2027-01-30', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::hit(4, '2027-02-02', '2027-02-27', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::hit(5, '2027-03-02', '2027-03-27', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::hit(6, '2027-03-30', '2027-04-24', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::hit(7, '2027-04-27', '2027-05-22', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                    self::hit(8, '2027-05-25', '2027-06-20', ['mon','tue','wed','thu','fri'], '17:00', '20:00', '4 weeks'),
                ],
            ],
            'evening-lingo' => [
                'name_fr' => 'Evening Lingo',
                'name_en' => 'Evening Lingo',
                'category' => 'Evening Lingo',
                'language' => 'en',
                'service' => 'evening-lingo',
                'color' => '#8e5bb9',
                'kind' => 'recurring',
                'sessions' => [
                    self::hit(1, '2026-10-08', '2026-12-10', ['wed'], '17:00', '20:00', '10 weeks'),
                    self::pause(),
                    self::hit(2, '2027-01-07', '2027-03-11', ['wed'], '17:00', '20:00', '10 weeks'),
                    self::hit(3, '2027-03-18', '2027-05-20', ['wed'], '17:00', '20:00', '10 weeks'),
                ],
            ],
            'saturdays-in-english' => [
                'name_fr' => 'Saturdays in English',
                'name_en' => 'Saturdays in English',
                'category' => 'Saturdays in English',
                'language' => 'en',
                'service' => 'saturdays-in-english',
                'color' => '#b96b2f',
                'kind' => 'two-blocks',
                'sessions' => [
                    self::hit(1, '2026-10-11', '2026-11-08', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                    self::hit(2, '2026-11-15', '2026-12-13', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                    self::pause(),
                    self::hit(3, '2027-01-10', '2027-02-07', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                    self::hit(4, '2027-02-14', '2027-03-14', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                    self::hit(5, '2027-03-21', '2027-04-18', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                    self::hit(6, '2027-04-25', '2027-05-23', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                    self::hit(7, '2027-05-30', '2027-06-27', ['sat'], '09:00', '12:00', '5 weeks', '13:00', '16:00'),
                ],
            ],

            // ─────────────────────────── WORKSHOPS (FR) ───────────────────────────
            'conversation-mardi' => [
                'name_fr' => 'Atelier de conversation – Mardi',
                'name_en' => 'Conversation Workshop – Tuesday',
                'category' => 'Conversation',
                'language' => 'fr',
                'service' => 'atelier-conversation',
                'color' => '#27ae60',
                'kind' => 'workshop',
                'sessions' => [
                    self::wm(1, ['2026-10-01','2026-10-08','2026-10-15','2026-10-22','2026-10-29'], '12:00', '13:00'),
                    self::wm(2, ['2026-11-05','2026-11-12','2026-11-19','2026-11-26','2026-12-03'], '12:00', '13:00'),
                    self::pause(),
                    self::wm(3, ['2027-01-07','2027-01-14','2027-01-21','2027-01-28','2027-02-04'], '12:00', '13:00'),
                    self::wm(4, ['2027-02-11','2027-02-18','2027-02-25','2027-03-04','2027-03-11'], '12:00', '13:00'),
                    self::wm(5, ['2027-03-18','2027-03-25','2027-04-01','2027-04-08','2027-04-15'], '12:00', '13:00'),
                    self::wm(6, ['2027-04-22','2027-04-29','2027-05-06','2027-05-13','2027-05-20'], '12:00', '13:00'),
                    self::wm(7, ['2027-05-27','2027-06-03','2027-06-10','2027-06-17','2027-06-24'], '12:00', '13:00'),
                ],
            ],
            'conversation-jeudi' => [
                'name_fr' => 'Atelier de conversation – Jeudi',
                'name_en' => 'Conversation Workshop – Thursday',
                'category' => 'Conversation',
                'language' => 'fr',
                'service' => 'atelier-conversation',
                'color' => '#229954',
                'kind' => 'workshop',
                'sessions' => [
                    self::wm(1, ['2026-10-03','2026-10-10','2026-10-17','2026-10-24','2026-10-31'], '17:00', '18:00'),
                    self::wm(2, ['2026-11-07','2026-11-14','2026-11-21','2026-11-28','2026-12-05'], '17:00', '18:00'),
                    self::pause(),
                    self::wm(3, ['2027-01-09','2027-01-16','2027-01-23','2027-01-30','2027-02-06'], '17:00', '18:00'),
                    self::wm(4, ['2027-02-13','2027-02-20','2027-02-27','2027-03-06','2027-03-13'], '17:00', '18:00'),
                    self::wm(5, ['2027-03-20','2027-03-27','2027-04-03','2027-04-10','2027-04-17'], '17:00', '18:00'),
                    self::wm(6, ['2027-04-24','2027-05-01','2027-05-08','2027-05-15','2027-05-22'], '17:00', '18:00'),
                    self::wm(7, ['2027-05-29','2027-06-05','2027-06-12','2027-06-19','2027-06-26'], '17:00', '18:00'),
                ],
            ],
            'culture-mercredi' => [
                'name_fr' => 'Atelier Culture du Canada – Mercredi',
                'name_en' => 'Culture of Canada Workshop – Wednesday',
                'category' => 'Culture du Canada',
                'language' => 'fr',
                'service' => 'atelier-culture-canada',
                'color' => '#8e44ad',
                'kind' => 'workshop',
                'sessions' => [
                    self::wm(1, ['2026-10-02','2026-10-09','2026-10-16','2026-10-23','2026-10-30','2026-11-06','2026-11-13','2026-11-20','2026-11-27','2026-12-04'], '17:00', '18:00'),
                    self::pause(),
                    self::wm(2, ['2027-01-08','2027-01-15','2027-01-22','2027-01-29','2027-02-05','2027-02-12','2027-02-19','2027-02-26','2027-03-05','2027-03-12'], '17:00', '18:00'),
                    self::wm(3, ['2027-03-19','2027-03-26','2027-04-02','2027-04-09','2027-04-16','2027-04-23','2027-04-30','2027-05-07','2027-05-14','2027-05-21'], '17:00', '18:00'),
                ],
            ],
            'culture-vendredi' => [
                'name_fr' => 'Atelier Culture du Canada – Vendredi',
                'name_en' => 'Culture of Canada Workshop – Friday',
                'category' => 'Culture du Canada',
                'language' => 'fr',
                'service' => 'atelier-culture-canada',
                'color' => '#7d3c98',
                'kind' => 'workshop',
                'sessions' => [
                    self::wm(1, ['2026-10-04','2026-10-11','2026-10-18','2026-10-25','2026-11-01','2026-11-08','2026-11-15','2026-11-22','2026-11-29','2026-12-06'], '12:00', '13:00'),
                    self::pause(),
                    self::wm(2, ['2027-01-10','2027-01-17','2027-01-24','2027-01-31','2027-02-07','2027-02-14','2027-02-21','2027-02-28','2027-03-07','2027-03-14'], '12:00', '13:00'),
                    self::wm(3, ['2027-03-21','2027-03-28','2027-04-04','2027-04-11','2027-04-18','2027-04-25','2027-05-02','2027-05-09','2027-05-16','2027-05-23'], '12:00', '13:00'),
                ],
            ],
            'maintien-mardi' => [
                'name_fr' => 'Atelier maintien & renforcement – Mardi',
                'name_en' => 'Maintenance & Reinforcement Workshop – Tuesday',
                'category' => 'Maintien & Renforcement',
                'language' => 'fr',
                'service' => 'atelier-maintien',
                'color' => '#c9883c',
                'kind' => 'workshop',
                'sessions' => [
                    self::wm(1, ['2026-10-01','2026-10-08','2026-10-15','2026-10-22','2026-10-29'], '17:00', '18:00'),
                    self::wm(2, ['2026-11-05','2026-11-12','2026-11-19','2026-11-26','2026-12-03'], '17:00', '18:00'),
                    self::pause(),
                    self::wm(3, ['2027-01-07','2027-01-14','2027-01-21','2027-01-28','2027-02-04'], '17:00', '18:00'),
                    self::wm(4, ['2027-02-11','2027-02-18','2027-02-25','2027-03-04','2027-03-11'], '17:00', '18:00'),
                    self::wm(5, ['2027-03-18','2027-03-25','2027-04-01','2027-04-08','2027-04-15'], '17:00', '18:00'),
                    self::wm(6, ['2027-04-22','2027-04-29','2027-05-06','2027-05-13','2027-05-20'], '17:00', '18:00'),
                    self::wm(7, ['2027-05-27','2027-06-03','2027-06-10','2027-06-17','2027-06-24'], '17:00', '18:00'),
                ],
            ],
            'maintien-jeudi' => [
                'name_fr' => 'Atelier maintien & renforcement – Jeudi',
                'name_en' => 'Maintenance & Reinforcement Workshop – Thursday',
                'category' => 'Maintien & Renforcement',
                'language' => 'fr',
                'service' => 'atelier-maintien',
                'color' => '#b9772f',
                'kind' => 'workshop',
                'sessions' => [
                    self::wm(1, ['2026-10-03','2026-10-10','2026-10-17','2026-10-24','2026-10-31'], '12:00', '13:00'),
                    self::wm(2, ['2026-11-07','2026-11-14','2026-11-21','2026-11-28','2026-12-05'], '12:00', '13:00'),
                    self::pause(),
                    self::wm(3, ['2027-01-09','2027-01-16','2027-01-23','2027-01-30','2027-02-06'], '12:00', '13:00'),
                    self::wm(4, ['2027-02-13','2027-02-20','2027-02-27','2027-03-06','2027-03-13'], '12:00', '13:00'),
                    self::wm(5, ['2027-03-20','2027-03-27','2027-04-03','2027-04-10','2027-04-17'], '12:00', '13:00'),
                    self::wm(6, ['2027-04-24','2027-05-01','2027-05-08','2027-05-15','2027-05-22'], '12:00', '13:00'),
                    self::wm(7, ['2027-05-29','2027-06-05','2027-06-12','2027-06-19','2027-06-26'], '12:00', '13:00'),
                ],
            ],
        ];
    }

    /** A recurring session entry (start/end/days/times/duration; optional second block). */
    protected static function hit(
        int $n,
        string $start,
        string $end,
        array $days,
        ?string $startTime,
        ?string $endTime,
        string $duration,
        ?string $startTime2 = null,
        ?string $endTime2 = null,
        ?string $notes = null
    ): array {
        return [
            'session' => $n,
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'start_time_2' => $startTime2,
            'end_time_2' => $endTime2,
            'duration' => $duration,
            'notes' => $notes,
        ];
    }

    /** A workshop session with explicit meeting dates. */
    protected static function wm(int $n, array $dates, string $startTime, string $endTime): array
    {
        return [
            'session' => $n,
            'dates' => $dates,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }

    /** A winter/holiday break marker. */
    protected static function pause(): array
    {
        return ['pause' => true];
    }

    /**
     * Builds a human-friendly weekday label from the day keys of a recurring session.
     */
    public static function daysLabel(array $days): string
    {
        $labels = [
            'mon' => 'Lun', 'tue' => 'Mar', 'wed' => 'Mer', 'thu' => 'Jeu',
            'fri' => 'Ven', 'sat' => 'Sam', 'sun' => 'Dim',
        ];
        if (count($days) === 5 && !in_array('sat', $days) && !in_array('sun', $days)) {
            return 'Lun → Ven';
        }
        if ($days === ['tue', 'thu']) {
            return 'Mardi & jeudi';
        }
        if ($days === ['mon', 'wed']) {
            return 'Lundi & mercredi';
        }
        if ($days === ['mon', 'tue', 'wed', 'thu']) {
            return 'Lun → Jeu';
        }
        if ($days === ['tue', 'wed', 'thu', 'fri']) {
            return 'Mar → Ven';
        }
        return collect($days)->map(fn ($d) => $labels[$d] ?? $d)->implode(' & ');
    }

    /**
     * Import (or refresh) all calendar data from the encoded DOCX source.
     */
    public static function import(bool $reset = true): array
    {
        if ($reset) {
            CalendarMeeting::query()->delete();
            CalendarSession::query()->delete();
            CalendarProgram::query()->delete();
        }

        $counts = ['programs' => 0, 'sessions' => 0, 'meetings' => 0, 'pauses' => 0];
        $order = 0;

        foreach (static::data() as $key => $program) {
            $service = Service::where('slug', $program['service'])->first();
            $cp = CalendarProgram::create([
                'name_fr' => $program['name_fr'],
                'name_en' => $program['name_en'] ?? $program['name_fr'],
                'category' => $program['category'],
                'language' => $program['language'],
                'service_id' => $service?->id,
                'color' => $program['color'] ?? null,
                'sort_order' => $order++,
                'is_active' => true,
            ]);
            $counts['programs']++;

            foreach ($program['sessions'] as $idx => $session) {
                if (isset($session['pause'])) {
                    $counts['pauses']++;
                    continue;
                }
                $counts['sessions']++;
                $cs = CalendarSession::create([
                    'calendar_program_id' => $cp->id,
                    'session_number' => $session['session'] ?? null,
                    'title' => 'Session ' . ($session['session'] ?? ''),
                    'start_date' => $session['start'] ?? ($session['dates'][0] ?? null),
                    'end_date' => $session['end'] ?? ($session['dates'][count($session['dates']) - 1] ?? null),
                    'days_text' => isset($session['days']) ? static::daysLabel($session['days']) : null,
                    'start_time' => $session['start_time'] ?? null,
                    'end_time' => $session['end_time'] ?? null,
                    'start_time_2' => $session['start_time_2'] ?? null,
                    'end_time_2' => $session['end_time_2'] ?? null,
                    'duration_text' => $session['duration'] ?? null,
                    'notes' => $session['notes'] ?? null,
                    'sort_order' => $idx,
                    'is_active' => true,
                ]);

                foreach (static::meetingsFor($cp, $cs, $session) as $date => $blocks) {
                    foreach ($blocks as $block) {
                        CalendarMeeting::create([
                            'calendar_program_id' => $cp->id,
                            'calendar_session_id' => $cs->id,
                            'title' => $cp->name_fr,
                            'event_date' => $date,
                            'day_of_week' => Carbon::parse($date)->dayOfWeek,
                            'start_time' => $block['start_time'],
                            'end_time' => $block['end_time'],
                            'slot' => $block['slot'] ?? null,
                            'event_type' => array_key_exists('dates', $session) ? 'workshop' : 'class',
                            'is_active' => true,
                        ]);
                        $counts['meetings']++;
                    }
                }
            }
        }

        return $counts;
    }

    /**
     * Returns the dated occurrences for a session.
     *
     * @return array<string, array<int, array{start_time:?string,end_time:?string,slot:?string}>>
     */
    protected static function meetingsFor(CalendarProgram $program, CalendarSession $session, array $data): array
    {
        // Workshop-type: dates are explicit.
        if (isset($data['dates'])) {
            $out = [];
            foreach ($data['dates'] as $d) {
                $out[$d] = [['start_time' => $data['start_time'], 'end_time' => $data['end_time'], 'slot' => null]];
            }
            return $out;
        }

        // Recurring: expand weekday occurrences between start and end.
        $daysMap = [
            'mon' => CarbonInterface::MONDAY,
            'tue' => CarbonInterface::TUESDAY,
            'wed' => CarbonInterface::WEDNESDAY,
            'thu' => CarbonInterface::THURSDAY,
            'fri' => CarbonInterface::FRIDAY,
            'sat' => CarbonInterface::SATURDAY,
            'sun' => CarbonInterface::SUNDAY,
        ];
        $targetDays = collect($data['days'])->map(fn ($d) => $daysMap[$d])->all();
        $start = Carbon::parse($data['start']);
        $end = Carbon::parse($data['end']);
        $out = [];

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if (!in_array($d->dayOfWeek, $targetDays)) {
                continue;
            }
            $dateKey = $d->format('Y-m-d');
            $blocks = [['start_time' => $data['start_time'], 'end_time' => $data['end_time'], 'slot' => null]];
            if (!empty($data['start_time_2']) && !empty($data['end_time_2'])) {
                $blocks[] = ['start_time' => $data['start_time_2'], 'end_time' => $data['end_time_2'], 'slot' => 'apres-midi'];
            }
            $out[$dateKey] = $blocks;
        }

        return $out;
    }

    /**
     * Returns the weekday (day of week 1-7, Carbon Monday=1) set in effect for a session.
     * Prefers existing meeting rows, falling back to Monday–Friday.
     *
     * @return int[]
     */
    protected static function sessionWeekdays(CalendarSession $session): array
    {
        $days = $session->meetings()
            ->whereNotNull('day_of_week')
            ->distinct()
            ->orderBy('day_of_week')
            ->pluck('day_of_week')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (count($days) === 0) {
            // Default: Monday–Friday (Carbon dayOfWeek 1..5)
            return [1, 2, 3, 4, 5];
        }

        return $days;
    }

    /**
     * Regenerate the dated meetings of a single session from dates/times on the
     * session record, preserving the weekday pattern discovered from its meetings.
     *
     * Workshop-type sessions keep their explicit individual dates and are skipped.
     */
    public static function regenerateSession(CalendarSession $session): int
    {
        if ($session->meetings()->where('event_type', 'workshop')->exists()) {
            return $session->meetings()->count();
        }

        CalendarMeeting::where('calendar_session_id', $session->id)->delete();

        if (! $session->start_date || ! $session->end_date) {
            return 0;
        }

        $weekdays = static::sessionWeekdays($session);
        $start = Carbon::parse($session->start_date);
        $end = Carbon::parse($session->end_date);
        $count = 0;

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if (! in_array($d->dayOfWeek, $weekdays, true)) {
                continue;
            }
            $blocks = [[$session->start_time, $session->end_time, null]];
            if ($session->start_time_2 && $session->end_time_2) {
                $blocks[] = [$session->start_time_2, $session->end_time_2, 'apres-midi'];
            }
            foreach ($blocks as [$st, $et, $slot]) {
                CalendarMeeting::create([
                    'calendar_program_id' => $session->calendar_program_id,
                    'calendar_session_id' => $session->id,
                    'title' => $session->program->name_fr,
                    'event_date' => $d->format('Y-m-d'),
                    'day_of_week' => $d->dayOfWeek,
                    'start_time' => $st,
                    'end_time' => $et,
                    'slot' => $slot,
                    'event_type' => 'class',
                    'is_active' => true,
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Regenerate the meetings of every session belonging to a programme.
     */
    public static function regenerateProgram(CalendarProgram $program): int
    {
        $total = 0;
        foreach ($program->sessions as $session) {
            $total += static::regenerateSession($session);
        }
        return $total;
    }
}
