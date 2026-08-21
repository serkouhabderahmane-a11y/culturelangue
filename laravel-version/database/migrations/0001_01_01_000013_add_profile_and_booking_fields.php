<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('student_number')->nullable()->unique()->after('user_id');
            $table->string('native_language')->nullable()->after('country');
            $table->string('current_level')->nullable()->after('native_language');
            $table->string('target_level')->nullable()->after('current_level');
            $table->string('goal')->nullable()->after('target_level');
            $table->date('enrollment_date')->nullable()->after('goal');
            $table->string('preferred_language')->default('fr')->after('enrollment_date');
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->unique()->after('user_id');
            $table->string('department')->nullable()->after('specialization');
            $table->decimal('hourly_rate_solo', 8, 2)->default(55.00)->after('hourly_rate');
            $table->decimal('hourly_rate_group', 8, 2)->default(45.00)->after('hourly_rate_solo');
            $table->integer('contract_hours_month')->default(80)->after('hourly_rate_group');
            $table->date('hire_date')->nullable()->after('contract_hours_month');
            $table->decimal('rating', 2, 1)->default(0)->after('hire_date');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_ref')->nullable()->unique()->after('id');
            $table->string('contact_method')->nullable()->after('phone');
            $table->string('preferred_slot')->nullable()->after('preferred_date');
            $table->decimal('placement_score', 5, 2)->nullable()->after('payment_status');
            $table->string('placement_level')->nullable()->after('placement_score');
            $table->date('oral_test_date')->nullable()->after('placement_level');
            $table->string('oral_test_slot')->nullable()->after('oral_test_date');
            $table->string('oral_test_status')->nullable()->after('oral_test_slot');
            $table->string('currency')->default('CAD')->after('total_amount');
            $table->string('source')->default('website')->after('currency');
            $table->string('ip_address', 45)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_ref', 'contact_method', 'preferred_slot', 'placement_score', 'placement_level',
                'oral_test_date', 'oral_test_slot', 'oral_test_status', 'currency', 'source', 'ip_address',
            ]);
        });
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropColumn(['employee_number', 'department', 'hourly_rate_solo', 'hourly_rate_group', 'contract_hours_month', 'hire_date', 'rating']);
        });
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn(['student_number', 'native_language', 'current_level', 'target_level', 'goal', 'enrollment_date', 'preferred_language']);
        });
    }
};
