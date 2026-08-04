<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('consultants')->cascadeOnDelete();
            $table->date('work_date');
            $table->integer('required_daily_tasks')->default(0);
            $table->integer('completed_daily_tasks')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['consultant_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_records');
    }
};
