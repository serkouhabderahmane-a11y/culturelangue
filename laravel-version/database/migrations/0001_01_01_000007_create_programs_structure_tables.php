<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->decimal('rate_per_hour', 8, 2)->nullable()->after('price');
            $table->string('badge')->nullable()->after('hours_count');
            $table->string('badge_color')->nullable()->after('badge');
            $table->boolean('is_popular')->default(false)->after('badge_color');
        });

        Schema::create('course_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->integer('hours')->nullable();
            $table->integer('sessions')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('rate_per_hour', 8, 2)->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_color')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('group_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->integer('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('room')->nullable();
            $table->integer('max_students')->default(15);
            $table->integer('current_students')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('program_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('date_range')->nullable();
            $table->string('schedule_text')->nullable();
            $table->string('duration_text')->nullable();
            $table->string('state')->default('available');
            $table->string('availability_text')->nullable();
            $table->string('cta_primary')->default('Reserver');
            $table->string('cta_secondary')->default('Voir les dates');
            $table->text('pause_message')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('group_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->string('lesson_type')->default('class');
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['service_id', 'date']);
            $table->index('teacher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('benefits');
        Schema::dropIfExists('program_sessions');
        Schema::dropIfExists('group_schedules');
        Schema::dropIfExists('course_packages');
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['rate_per_hour', 'badge', 'badge_color', 'is_popular']);
        });
    }
};
