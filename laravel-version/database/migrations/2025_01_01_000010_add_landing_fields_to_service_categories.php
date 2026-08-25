<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->string('hero_title_html')->nullable()->after('banner_image');
            $table->text('hero_subtitle')->nullable()->after('hero_title_html');
            $table->json('hero_chips')->nullable()->after('hero_subtitle');
            $table->text('programs_intro')->nullable()->after('hero_chips');
            $table->longText('description_html')->nullable()->after('programs_intro');
            $table->json('benefits')->nullable()->after('description_html');
            $table->string('benefits_title')->nullable()->after('benefits');
            $table->text('benefits_intro')->nullable()->after('benefits_title');
            $table->json('audience')->nullable()->after('benefits_intro');
            $table->text('audience_intro')->nullable()->after('audience');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title_html', 'hero_subtitle', 'hero_chips', 'programs_intro',
                'description_html', 'benefits', 'benefits_title', 'benefits_intro',
                'audience', 'audience_intro',
            ]);
        });
    }
};
