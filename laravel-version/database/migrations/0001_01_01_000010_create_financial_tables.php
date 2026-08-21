<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('CAD');
            $table->string('status')->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('teacher_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('hours', 4, 2);
            $table->string('hour_type')->default('group');
            $table->date('work_date');
            $table->timestamps();
        });

        Schema::create('teacher_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('solo_hours', 6, 2)->default(0);
            $table->decimal('group_hours', 6, 2)->default(0);
            $table->decimal('solo_rate', 8, 2)->nullable();
            $table->decimal('group_rate', 8, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('student_id')->constrained()->nullOnDelete();
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->string('refund_reason')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn(['paid_at', 'refund_reason']);
        });
        Schema::dropIfExists('teacher_payrolls');
        Schema::dropIfExists('teacher_hours');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
    }
};
