<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Log a user activity centrally into the activity_logs table.
     */
    public static function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null
    ): ActivityLog {
        try {
            // Auto-ensure columns exist in MySQL database
            if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'model_type')) {
                try {
                    \Illuminate\Support\Facades\Schema::table('activity_logs', function (\Illuminate\Database\Schema\Blueprint $table) {
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'model_type')) {
                            $table->string('model_type')->nullable();
                        }
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'model_id')) {
                            $table->unsignedBigInteger('model_id')->nullable();
                        }
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'old_values')) {
                            $table->json('old_values')->nullable();
                        }
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'new_values')) {
                            $table->json('new_values')->nullable();
                        }
                        if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'user_agent')) {
                            $table->string('user_agent', 1023)->nullable();
                        }
                    });
                } catch (\Throwable $schemaErr) {
                    // Ignore schema alter errors if concurrently applied
                }
            }

            $payload = [
                'user_id' => $userId ?? auth()->id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => request()->ip(),
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'model_type')) {
                $payload['model_type'] = $entityType;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'model_id')) {
                $payload['model_id'] = $entityId;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'old_values')) {
                $payload['old_values'] = $oldValues;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'new_values')) {
                $payload['new_values'] = $newValues;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('activity_logs', 'user_agent')) {
                $payload['user_agent'] = request()->userAgent();
            }

            return ActivityLog::create($payload);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ActivityLogger failed: ' . $e->getMessage());
            return new ActivityLog();
        }
    }
}
