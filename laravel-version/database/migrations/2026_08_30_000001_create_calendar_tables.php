<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Calendar programs — the public catalogue of programs shown on the calendar.
        Schema::create('calendar_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_fr');
            $table->string('name_en')->nullable();
            $table->string('category')->index();          // program family, e.g. 'Français Express', 'TCF Québec', 'Conversation'
            $table->string('language')->default('fr')->index(); // fr | en
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Calendar sessions — a cohort/block of a program (e.g. "Session 1") with its date range,
        // weekdays and schedule. Individual dated class meetings are generated from these.
        Schema::create('calendar_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_program_id')->constrained()->cascadeOnDelete();
            $table->integer('session_number')->nullable();
            $table->string('title')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('days_text')->nullable();      // e.g. "Lun → Ven", "Mardi & jeudi", "Mercredi", "Samedi"
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('start_time_2')->nullable();     // second block of the day (e.g. Samedi après-midi)
            $table->time('end_time_2')->nullable();
            $table->string('duration_text')->nullable();  // e.g. "4 semaines", "10 semaines"
            $table->integer('duration_weeks')->nullable();
            $table->string('notes')->nullable();          // e.g. "5 semaines (fin d’année)", "(40 h)"
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Calendar meetings — one row per actual dated class / workshop occurrence.
        Schema::create('calendar_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('calendar_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->date('event_date')->index();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('slot')->nullable();           // 'matin' | 'apres-midi' for two-block days, else null
            $table->string('event_type')->default('class'); // class | workshop | break
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['event_date', 'calendar_program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_meetings');
        Schema::dropIfExists('calendar_sessions');
        Schema::dropIfExists('calendar_programs');
    }
};
