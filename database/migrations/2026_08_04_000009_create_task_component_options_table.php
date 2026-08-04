<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_component_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_component_id')->constrained('task_components')->cascadeOnDelete();
            $table->string('option_label');
            $table->string('option_value');
            $table->integer('display_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_component_options');
    }
};
