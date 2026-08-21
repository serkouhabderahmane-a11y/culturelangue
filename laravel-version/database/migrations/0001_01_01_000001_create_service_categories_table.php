<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_fr');
            $table->string('name_en');
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name_fr');
            $table->string('name_en');
            $table->text('short_description_fr')->nullable();
            $table->text('short_description_en')->nullable();
            $table->longText('description_fr')->nullable();
            $table->longText('description_en')->nullable();
            $table->json('benefits_fr')->nullable();
            $table->json('benefits_en')->nullable();
            $table->json('prerequisites_fr')->nullable();
            $table->json('prerequisites_en')->nullable();
            $table->json('learning_objectives_fr')->nullable();
            $table->json('learning_objectives_en')->nullable();
            $table->string('duration')->nullable();
            $table->string('price')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('icon')->nullable();
            $table->json('pricing_options')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->string('name_en')->nullable();
            $table->string('role_fr')->nullable();
            $table->string('role_en')->nullable();
            $table->text('content_fr');
            $table->text('content_en')->nullable();
            $table->string('image')->nullable();
            $table->integer('rating')->default(5);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question_fr');
            $table->string('question_en')->nullable();
            $table->text('answer_fr');
            $table->text('answer_en')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('label_fr');
            $table->string('label_en')->nullable();
            $table->string('value');
            $table->string('suffix_fr')->nullable();
            $table->string('suffix_en')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistics');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
