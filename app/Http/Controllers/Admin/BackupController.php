<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

use Illuminate\Support\Facades\Gate;

class BackupController extends Controller
{
    private string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups/database');
    }

    public function index(): Response
    {
        Gate::authorize('manage-backups');

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        $files = glob($this->backupDir . '/*.zip') ?: [];

        $backups = collect($files)->map(function (string $path) {
            $filename = basename($path);
            $note     = null;

            $zip = new ZipArchive();
            if ($zip->open($path) === true) {
                $idx = $zip->locateName('note.txt');
                if ($idx !== false) {
                    $note = $zip->getFromIndex($idx);
                }
                $zip->close();
            }

            $date = null;
            if (preg_match('/backup_(\d{4}-\d{2}-\d{2})_(\d{2})-(\d{2})-(\d{2})_database\.zip/', $filename, $m)) {
                $date = "{$m[1]} {$m[2]}:{$m[3]}:{$m[4]}";
            }

            return [
                'filename' => $filename,
                'size'     => $this->formatBytes(filesize($path)),
                'date'     => $date,
                'note'     => $note,
            ];
        })->sortByDesc('date')->values();

        return Inertia::render('Admin/Backups/Index', [
            'backups' => $backups,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        Gate::authorize('create-backups');
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        session_write_close();

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        $timestamp  = now()->format('Y-m-d_H-i-s');
        $filename   = "backup_{$timestamp}_database.zip";
        $zipPath    = $this->backupDir . '/' . $filename;
        $sqlTmpPath = storage_path("app/backups/tmp_{$timestamp}.sql");

        $pdo = DB::connection()->getPdo();

        $handle = fopen($sqlTmpPath, 'w');
        if (!$handle) {
            return back()->with('error', 'تعذّر إنشاء ملف التخزين المؤقت.');
        }

        fwrite($handle, "-- Backup: {$filename}\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createSql = array_values($createRow)[1];
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createSql . ";\n\n");

            $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            if ($count > 0) {
                $offset = 0;
                $chunk  = 500;
                while ($offset < $count) {
                    $rows = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunk} OFFSET {$offset}")->fetchAll(\PDO::FETCH_ASSOC);
                    if (empty($rows)) break;

                    $cols   = '`' . implode('`, `', array_keys($rows[0])) . '`';
                    $values = [];
                    foreach ($rows as $row) {
                        $vals     = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), array_values($row));
                        $values[] = '(' . implode(', ', $vals) . ')';
                    }
                    fwrite($handle, "INSERT INTO `{$table}` ({$cols}) VALUES\n" . implode(",\n", $values) . ";\n");
                    $offset += $chunk;
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($sqlTmpPath, 'database.sql');
            $note = trim($request->input('note', ''));
            if ($note !== '') {
                $zip->addFromString('note.txt', $note);
            }
            $zip->close();
        }

        if (file_exists($sqlTmpPath)) {
            unlink($sqlTmpPath);
        }

        \App\Helpers\ActivityLogger::log(
            'create_backup',
            'Backup',
            null,
            "تم إنشاء نسخة احتياطية جديدة بقاعدة البيانات: {$filename}",
            null,
            ['filename' => $filename, 'note' => $request->input('note')]
        );

        return back()->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح.');
    }

    public function restore(string $filename): RedirectResponse
    {
        set_time_limit(0);
        ini_set('memory_limit', '1G');
        session_write_close();

        $zipPath = $this->backupDir . '/' . basename($filename);

        if (!file_exists($zipPath)) {
            return back()->with('error', 'الملف غير موجود.');
        }

        $tmpDir = storage_path('app/restore_temp_' . time());
        mkdir($tmpDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            rmdir($tmpDir);
            return back()->with('error', 'تعذّر فتح ملف النسخة الاحتياطية.');
        }
        $zip->extractTo($tmpDir);
        $zip->close();

        $sqlFile = $tmpDir . '/database.sql';
        if (file_exists($sqlFile)) {
            try {
                $this->restoreLargeSQL($sqlFile);
            } catch (\Throwable $e) {
                $this->deleteDir($tmpDir);
                return back()->with('error', 'فشل استعادة قاعدة البيانات: ' . $e->getMessage());
            }
        }

        $this->deleteDir($tmpDir);

        \App\Helpers\ActivityLogger::log(
            'restore_backup',
            'Backup',
            null,
            "تم استعادة قاعدة البيانات من النسخة الاحتياطية: {$filename}",
            null,
            ['filename' => $filename]
        );

        return back()->with('success', 'تمت استعادة النسخة الاحتياطية بنجاح.');
    }

    public function download(string $filename)
    {
        $zipPath = $this->backupDir . '/' . basename($filename);

        if (!file_exists($zipPath)) {
            abort(404);
        }

        return response()->download($zipPath, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function delete(string $filename): RedirectResponse
    {
        $zipPath = $this->backupDir . '/' . basename($filename);

        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        \App\Helpers\ActivityLogger::log(
            'delete_backup',
            'Backup',
            null,
            "تم حذف النسخة الاحتياطية: {$filename}",
            ['filename' => $filename],
            null
        );

        return back()->with('success', 'تم حذف النسخة الاحتياطية.');
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:zip', 'max:524288'],
        ]);

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        $file     = $request->file('file');
        $original = $file->getClientOriginalName();

        if (!preg_match('/^backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_database\.zip$/', $original)) {
            $original = 'backup_' . now()->format('Y-m-d_H-i-s') . '_database.zip';
        }

        $file->move($this->backupDir, $original);

        return back()->with('success', 'تم رفع النسخة الاحتياطية بنجاح.');
    }

    public function openFolder(): RedirectResponse
    {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        if (PHP_OS_FAMILY === 'Linux') {
            exec("xdg-open " . escapeshellarg($this->backupDir) . " > /dev/null 2>&1 &");
        } elseif (PHP_OS_FAMILY === 'Windows') {
            exec("explorer " . escapeshellarg(str_replace('/', '\\', $this->backupDir)));
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            exec("open " . escapeshellarg($this->backupDir));
        }

        return back()->with('success', 'تم فتح مجلد النسخ الاحتياطية المحلي بنجاح.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2)    . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2)       . ' KB';
        return $bytes . ' B';
    }

    private function restoreLargeSQL(string $sqlFile): void
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        $content  = file_get_contents($sqlFile);
        $current  = '';
        $inString = false;
        $strChar  = '';
        $len      = strlen($content);
        $stmts    = [];

        for ($i = 0; $i < $len; $i++) {
            $char = $content[$i];
            if ($inString) {
                $current .= $char;
                if ($char === '\\') {
                    $current .= $content[++$i];
                } elseif ($char === $strChar) {
                    $inString = false;
                }
            } elseif ($char === "'" || $char === '"') {
                $inString = true;
                $strChar  = $char;
                $current .= $char;
            } elseif ($char === ';') {
                $stmt = trim($current);
                if ($stmt !== '') {
                    $stmts[] = $stmt;
                }
                $current = '';
            } else {
                $current .= $char;
            }
        }

        foreach ($stmts as $stmt) {
            if (str_starts_with(ltrim($stmt), '--')) continue;
            $pdo->exec($stmt);
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
