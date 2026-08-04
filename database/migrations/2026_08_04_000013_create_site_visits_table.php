<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_record_id')->constrained('daily_records')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->timestamp('visit_started_at')->nullable();
            $table->timestamp('visit_finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // UNIQUE constraint on (daily_record_id, site_id) — BR-023
            $table->unique(['daily_record_id', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
