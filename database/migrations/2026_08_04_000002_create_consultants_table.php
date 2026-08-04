<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('employee_number')->unique();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('specialization')->nullable();
            $table->foreignId('work_schedule_template_id')->nullable()->constrained('work_schedule_templates')->nullOnDelete();
            $table->enum('employment_status', ['active', 'suspended', 'vacation'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};
