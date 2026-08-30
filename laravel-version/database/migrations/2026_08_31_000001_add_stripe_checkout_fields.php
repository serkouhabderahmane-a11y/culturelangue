<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id', 255)->nullable()->after('transaction_id');
            $table->index('stripe_checkout_session_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_program_id')->nullable()->after('program_id');
            $table->unsignedBigInteger('calendar_session_id')->nullable()->after('calendar_program_id');
            $table->string('session_label', 255)->nullable()->after('calendar_session_id');
            $table->date('session_date')->nullable()->after('session_label');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['stripe_checkout_session_id']);
            $table->dropColumn('stripe_checkout_session_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['calendar_program_id', 'calendar_session_id', 'session_label', 'session_date']);
        });
    }
};
