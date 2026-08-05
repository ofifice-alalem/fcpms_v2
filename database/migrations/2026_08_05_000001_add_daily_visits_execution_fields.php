<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('daily_records')) {
            Schema::table('daily_records', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_records', 'check_in_time')) {
                    $table->timestamp('check_in_time')->nullable()->after('work_date');
                }
                if (!Schema::hasColumn('daily_records', 'check_out_time')) {
                    $table->timestamp('check_out_time')->nullable()->after('check_in_time');
                }
                if (!Schema::hasColumn('daily_records', 'notes')) {
                    $table->text('notes')->nullable()->after('check_out_time');
                }
            });
        }

        if (Schema::hasTable('site_visits')) {
            Schema::table('site_visits', function (Blueprint $table) {
                if (!Schema::hasColumn('site_visits', 'status')) {
                    $table->enum('status', ['in_progress', 'completed'])->default('in_progress')->after('site_id');
                }
            });
        }

        if (Schema::hasTable('task_responses')) {
            Schema::table('task_responses', function (Blueprint $table) {
                if (!Schema::hasColumn('task_responses', 'status')) {
                    $table->enum('status', ['draft', 'submitted'])->default('draft')->after('task_definition_id');
                }
                if (!Schema::hasColumn('task_responses', 'submitted_at')) {
                    $table->timestamp('submitted_at')->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('daily_records')) {
            Schema::table('daily_records', function (Blueprint $table) {
                if (Schema::hasColumn('daily_records', 'check_in_time')) {
                    $table->dropColumn(['check_in_time', 'check_out_time', 'notes']);
                }
            });
        }

        if (Schema::hasTable('site_visits')) {
            Schema::table('site_visits', function (Blueprint $table) {
                if (Schema::hasColumn('site_visits', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        if (Schema::hasTable('task_responses')) {
            Schema::table('task_responses', function (Blueprint $table) {
                if (Schema::hasColumn('task_responses', 'status')) {
                    $table->dropColumn(['status', 'submitted_at']);
                }
            });
        }
    }
};
