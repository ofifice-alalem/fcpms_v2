<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'group')) {
                    $table->string('group')->default('general')->after('setting_value');
                }
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('activity_logs', 'model_type')) {
                    $table->string('model_type')->nullable()->after('entity_type');
                }
                if (!Schema::hasColumn('activity_logs', 'model_id')) {
                    $table->unsignedBigInteger('model_id')->nullable()->after('entity_id');
                }
                if (!Schema::hasColumn('activity_logs', 'old_values')) {
                    $table->json('old_values')->nullable()->after('description');
                }
                if (!Schema::hasColumn('activity_logs', 'new_values')) {
                    $table->json('new_values')->nullable()->after('old_values');
                }
                if (!Schema::hasColumn('activity_logs', 'user_agent')) {
                    $table->string('user_agent', 1023)->nullable()->after('ip_address');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (Schema::hasColumn('settings', 'group')) {
                    $table->dropColumn('group');
                }
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $cols = array_filter(['model_type', 'model_id', 'old_values', 'new_values', 'user_agent'], fn($c) => Schema::hasColumn('activity_logs', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
