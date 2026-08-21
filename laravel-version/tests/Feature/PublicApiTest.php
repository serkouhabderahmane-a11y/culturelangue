<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;

class PublicApiTest extends BaseApiTestCase
{
    public function test_home_returns_all_content(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'parcours-linguistique',
            'name_fr' => 'Parcours linguistique',
            'name_en' => 'Linguistic Pathway',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'slug' => 'francais-express',
            'name_fr' => 'Français Express',
            'name_en' => 'Français Express',
            'duration' => '4 semaines',
            'price' => '600 $',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/home')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['services', 'categories', 'testimonials', 'faqs', 'statistics', 'settings'],
            ]);
    }

    public function test_services_lists_active_services(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'ateliers',
            'name_fr' => 'Ateliers',
            'name_en' => 'Workshops',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'slug' => 'atelier-conversation',
            'name_fr' => 'Atelier Conversation',
            'name_en' => 'Conversation Workshop',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'slug' => 'atelier-cache',
            'name_fr' => 'Hidden Atelier',
            'name_en' => 'Hidden Workshop',
            'order' => 2,
            'is_active' => false,
        ]);

        $this->getJson('/api/v1/services')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_service_by_slug(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'formation-en-solo',
            'name_fr' => 'Formation en solo',
            'name_en' => 'Private Lessons',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'slug' => 'solo-5h',
            'name_fr' => 'Solo 5h',
            'name_en' => 'Solo 5h',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/services/solo-5h')
            ->assertStatus(200)
            ->assertJsonPath('data.slug', 'solo-5h');
    }

    public function test_service_by_slug_not_found(): void
    {
        $this->getJson('/api/v1/services/non-existent')
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_categories_include_active_services(): void
    {
        $category = ServiceCategory::create([
            'slug' => 'tcf-quebec',
            'name_fr' => 'TCF Québec',
            'name_en' => 'TCF Québec',
            'order' => 1,
            'is_active' => true,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'slug' => 'tcf-partiel',
            'name_fr' => 'TCF Partiel',
            'name_en' => 'TCF Part-time',
            'order' => 1,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/categories')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_contact_form_creates_message(): void
    {
        $this->postJson('/api/v1/contact', [
            'first_name' => 'Alice',
            'last_name' => 'Martin',
            'email' => 'alice@example.com',
            'message' => 'Hello, I want to know more about your courses.',
            'consent' => true,
        ])->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('contact_messages', ['email' => 'alice@example.com']);
    }

    public function test_contact_form_validation(): void
    {
        $this->postJson('/api/v1/contact', [
            'first_name' => '',
            'email' => 'not-an-email',
        ])->assertStatus(422);
    }

    public function test_settings_endpoint(): void
    {
        $this->getJson('/api/v1/settings')
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
