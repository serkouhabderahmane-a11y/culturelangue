<?php

namespace Database\Seeders;

use App\Models\ContactInfo;
use Illuminate\Database\Seeder;

class ContactInfoSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'type' => 'email',
                'label_fr' => 'Email',
                'label_en' => 'Email',
                'value' => 'admin@cultulangues.ca',
                'icon' => 'envelope',
                'order' => 1,
            ],
            [
                'type' => 'phone',
                'label_fr' => 'Téléphone',
                'label_en' => 'Phone',
                'value' => '+1 (819) 271-9783',
                'icon' => 'phone',
                'order' => 2,
            ],
            [
                'type' => 'address',
                'label_fr' => 'Adresse',
                'label_en' => 'Address',
                'value' => '468 rue Plouffe suite 3, Gatineau J8P 4B7 (QC)',
                'icon' => 'map-marker',
                'order' => 3,
            ],
        ];

        foreach ($contacts as $data) {
            ContactInfo::create($data);
        }
    }
}
