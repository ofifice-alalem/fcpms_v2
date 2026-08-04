<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_response_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_response_id')->constrained('task_responses')->cascadeOnDelete();
            $table->foreignId('task_component_id')->constrained('task_components')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_response_values');
    }
};
