<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_definition_id')->constrained('task_definitions')->cascadeOnDelete();
            $table->string('component_type');
            $table->string('label');
            $table->string('placeholder')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->foreignId('conditional_parent_id')->nullable()->constrained('task_components')->nullOnDelete();
            $table->string('conditional_value')->nullable();
            $table->unsignedBigInteger('visibility_component_id')->nullable();
            $table->unsignedBigInteger('visibility_option_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_components');
    }
};
