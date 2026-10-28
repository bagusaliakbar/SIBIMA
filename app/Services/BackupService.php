<?php

namespace App\Services;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;

class BackupService
{
    /**
     * Get the directory where backup files are stored.
     */
    public function getBackupDirectory(): string
    {
        $dir = storage_path('app/private/backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        return $dir;
    }

    /**
     * Format bytes into a human-readable string.
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get list of all backup files sorted by creation date descending.
     */
    public function getBackupsList(): array
    {
        $dir = $this->getBackupDirectory();
        $files = File::files($dir);

        // Also check legacy storage/app/backups if it exists
        $legacyDir = storage_path('app/backups');
        if (File::exists($legacyDir) && $legacyDir !== $dir) {
            $files = array_merge($files, File::files($legacyDir));
        }

        $backups = [];
        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            $filename = $file->getFilename();

            if (!in_array($extension, ['sql', 'gz'])) {
                continue;
            }

            $size = $file->getSize();
            $mtime = $file->getMTime();
            $carbon = Carbon::createFromTimestamp($mtime);

            $backups[] = [
                'filename' => $filename,
                'path' => $file->getPathname(),
                'size_bytes' => $size,
                'size_formatted' => self::formatBytes($size),
                'timestamp' => $mtime,
                'created_at' => $carbon,
                'created_at_formatted' => $carbon->locale('id')->translatedFormat('d M Y, H:i:s'),
                'created_at_human' => $carbon->diffForHumans(),
                'is_compressed' => $extension === 'gz' || str_ends_with($filename, '.sql.gz'),
            ];
        }

        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Get general stats regarding database and backups.
     */
    public function getStats(): array
    {
        $backups = $this->getBackupsList();
        $totalBackups = count($backups);
        $totalBytes = array_sum(array_column($backups, 'size_bytes'));
        $latestBackup = $backups[0] ?? null;

        $dbName = config('database.connections.' . config('database.default') . '.database', 'Database');
        $driver = DB::connection()->getDriverName();
        $tablesCount = 0;
        $dbSizeBytes = 0;

        try {
            if ($driver === 'mysql') {
                $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
                $tablesCount = count($tables);

                $sizeResult = DB::select('
                    SELECT SUM(data_length + index_length) AS size 
                    FROM information_schema.TABLES 
                    WHERE table_schema = ?
                ', [$dbName]);

                $dbSizeBytes = (int) ($sizeResult[0]->size ?? 0);
            } elseif ($driver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                $tablesCount = count($tables);
                $dbPath = config('database.connections.sqlite.database');
                if (file_exists($dbPath)) {
                    $dbSizeBytes = filesize($dbPath);
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully if schema queries are restricted
        }

        return [
            'total_backups' => $totalBackups,
            'total_size_bytes' => $totalBytes,
            'total_size_formatted' => self::formatBytes($totalBytes),
            'latest_backup' => $latestBackup,
            'database_name' => $dbName,
            'database_driver' => strtoupper($driver),
            'tables_count' => $tablesCount,
            'database_size_formatted' => $dbSizeBytes > 0 ? self::formatBytes($dbSizeBytes) : '-',
        ];
    }

    /**
     * Create a full database backup in pure PHP.
     */
    public function createBackup(?string $customName = null): array
    {
        // Increase time and memory limits for backup generation
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $dir = $this->getBackupDirectory();
        $timestamp = date('Y-m-d_H-i-s');
        
        if ($customName) {
            $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $customName);
            $filename = 'backup_' . $safeName . '_' . $timestamp . '.sql';
        } else {
            $filename = 'sibima_backup_' . $timestamp . '.sql';
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $filename;
        $handle = fopen($filePath, 'w');

        if (!$handle) {
            throw new \RuntimeException("Gagal membuat file backup pada path: {$filePath}");
        }

        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        $driver = DB::connection()->getDriverName();
        $dateFormatted = date('Y-m-d H:i:s');

        // Header SQL
        fwrite($handle, "-- ============================================================\n");
        fwrite($handle, "-- SIBIMA DATABASE BACKUP\n");
        fwrite($handle, "-- Aplikasi : " . config('app.name', 'SIBIMA') . "\n");
        fwrite($handle, "-- Tanggal  : {$dateFormatted}\n");
        fwrite($handle, "-- Database : {$dbName}\n");
        fwrite($handle, "-- Driver   : {$driver}\n");
        fwrite($handle, "-- ============================================================\n\n");

        fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
        fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
        fwrite($handle, "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
        fwrite($handle, "/*!40101 SET NAMES utf8mb4 */;\n");
        fwrite($handle, "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n");
        fwrite($handle, "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n");
        fwrite($handle, "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n");
        fwrite($handle, "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n");
        fwrite($handle, "SET time_zone = '+00:00';\n\n");

        $totalTables = 0;
        $totalRows = 0;

        if ($driver === 'mysql') {
            $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            $tableKey = 'Tables_in_' . $dbName;

            foreach ($tables as $tableObj) {
                $tableName = $tableObj->$tableKey ?? reset($tableObj);
                if (!$tableName) {
                    continue;
                }

                $totalTables++;

                // Table structure
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "-- Struktur Tabel `{$tableName}`\n");
                fwrite($handle, "-- ------------------------------------------------------------\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");

                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createSql = $createTable[0]->{'Create Table'} ?? null;

                if ($createSql) {
                    fwrite($handle, $createSql . ";\n\n");
                }

                // Table data
                $rowCount = DB::table($tableName)->count();
                if ($rowCount > 0) {
                    $totalRows += $rowCount;

                    fwrite($handle, "-- Data untuk Tabel `{$tableName}` ({$rowCount} baris)\n");
                    fwrite($handle, "LOCK TABLES `{$tableName}` WRITE;\n");
                    fwrite($handle, "/*!40000 ALTER TABLE `{$tableName}` DISABLE KEYS */;\n");

                    // Fetch columns
                    $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
                    $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                    $columnsList = implode(', ', $escapedColumns);

                    // Stream records in chunks of 250 rows to keep memory usage low
                    DB::table($tableName)->orderBy(DB::raw('1'))->chunk(250, function ($rows) use ($handle, $tableName, $columnsList, $columns, $pdo) {
                        $valuesSql = [];

                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $escapedValues = [];

                            foreach ($columns as $column) {
                                $val = $rowArray[$column] ?? null;

                                if (is_null($val)) {
                                    $escapedValues[] = 'NULL';
                                } elseif (is_int($val) || is_float($val)) {
                                    $escapedValues[] = $val;
                                } elseif (is_bool($val)) {
                                    $escapedValues[] = $val ? 1 : 0;
                                } else {
                                    $escapedValues[] = $pdo->quote((string) $val);
                                }
                            }

                            $valuesSql[] = '(' . implode(', ', $escapedValues) . ')';
                        }

                        if (!empty($valuesSql)) {
                            fwrite($handle, "INSERT INTO `{$tableName}` ({$columnsList}) VALUES\n" . implode(",\n", $valuesSql) . ";\n");
                        }
                    });

                    fwrite($handle, "/*!40000 ALTER TABLE `{$tableName}` ENABLE KEYS */;\n");
                    fwrite($handle, "UNLOCK TABLES;\n\n");
                }
            }
        } elseif ($driver === 'sqlite') {
            $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tables as $table) {
                $tableName = $table->name;
                $totalTables++;

                fwrite($handle, "-- Table {$tableName}\n");
                fwrite($handle, "DROP TABLE IF EXISTS \"{$tableName}\";\n");
                fwrite($handle, $table->sql . ";\n\n");

                $rowCount = DB::table($tableName)->count();
                if ($rowCount > 0) {
                    $totalRows += $rowCount;
                    DB::table($tableName)->orderBy(DB::raw('1'))->chunk(250, function ($rows) use ($handle, $tableName, $pdo) {
                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $cols = array_keys($rowArray);
                            $escapedCols = array_map(fn($c) => "\"{$c}\"", $cols);

                            $vals = [];
                            foreach ($rowArray as $v) {
                                if (is_null($v)) {
                                    $vals[] = 'NULL';
                                } elseif (is_int($v) || is_float($v)) {
                                    $vals[] = $v;
                                } else {
                                    $vals[] = $pdo->quote((string) $v);
                                }
                            }

                            fwrite($handle, "INSERT INTO \"{$tableName}\" (" . implode(', ', $escapedCols) . ") VALUES (" . implode(', ', $vals) . ");\n");
                        }
                    });
                    fwrite($handle, "\n");
                }
            }
        }

        // Footer SQL
        fwrite($handle, "-- ------------------------------------------------------------\n");
        fwrite($handle, "-- Restorasi Environment Variables\n");
        fwrite($handle, "-- ------------------------------------------------------------\n");
        fwrite($handle, "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n");
        fwrite($handle, "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n");
        fwrite($handle, "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n");
        fwrite($handle, "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n");
        fwrite($handle, "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n");
        fwrite($handle, "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n");
        fwrite($handle, "-- SIBIMA Backup Selesai pada: " . date('Y-m-d H:i:s') . "\n");

        fclose($handle);

        $fileSize = filesize($filePath);

        // Record activity log
        try {
            ActivityLog::log(
                activity: 'Buat Backup Database',
                description: "Berhasil membuat file backup: {$filename} (" . self::formatBytes($fileSize) . ") dengan {$totalTables} tabel dan {$totalRows} baris data.",
                module: 'Backup'
            );
        } catch (\Throwable $e) {}

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $filePath,
            'size_bytes' => $fileSize,
            'size_formatted' => self::formatBytes($fileSize),
            'tables_count' => $totalTables,
            'rows_count' => $totalRows,
            'created_at' => Carbon::now(),
        ];
    }

    /**
     * Restore database from an existing backup file.
     */
    public function restoreBackup(string $filename): array
    {
        @set_time_limit(900);
        @ini_set('memory_limit', '512M');

        $safeFilename = basename($filename);
        $filePath = $this->getBackupFilePath($safeFilename);

        if (!$filePath || !File::exists($filePath)) {
            throw new \InvalidArgumentException("File backup tidak ditemukan: {$safeFilename}");
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['sql', 'gz'])) {
            throw new \InvalidArgumentException("Format file tidak didukung: {$extension}");
        }

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Temporarily disable foreign keys for seamless table dropping and insertion
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');

        $isGz = $extension === 'gz' || str_ends_with($filePath, '.sql.gz');
        $handle = $isGz ? gzopen($filePath, 'rb') : fopen($filePath, 'r');

        if (!$handle) {
            throw new \RuntimeException("Gagal membuka file backup: {$filePath}");
        }

        $queryBuffer = '';
        $executedCount = 0;

        // Snapshot existing admin accounts if restore is executed by a non-admin (e.g. kaprodi)
        $isCallerNonAdmin = auth()->check() && auth()->user()->role !== 'admin';
        $adminSnapshot = $isCallerNonAdmin
            ? DB::table('users')->where('role', 'admin')->get()->keyBy('id')
            : collect();

        try {
            while (!($isGz ? gzeof($handle) : feof($handle))) {
                $line = $isGz ? gzgets($handle, 40960) : fgets($handle, 40960);
                if ($line === false) {
                    break;
                }

                $trimmed = trim($line);

                // Skip empty lines and full line comments
                if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*')) {
                    // Check if comment is not closed on the same line
                    if (str_starts_with($trimmed, '/*') && !str_contains($trimmed, '*/')) {
                        while (!($isGz ? gzeof($handle) : feof($handle))) {
                            $cLine = $isGz ? gzgets($handle, 40960) : fgets($handle, 40960);
                            if ($cLine !== false && str_contains($cLine, '*/')) {
                                break;
                            }
                        }
                    }
                    continue;
                }

                $queryBuffer .= $line;

                // If statement ends with semicolon
                if (str_ends_with($trimmed, ';')) {
                    $sqlToExecute = trim($queryBuffer);
                    if ($sqlToExecute !== '') {
                        $this->assertSafeBackupStatement($sqlToExecute);
                        $pdo->exec($sqlToExecute);
                        $executedCount++;
                    }
                    $queryBuffer = '';
                }
            }

            // Execute remaining buffer if any
            if (trim($queryBuffer) !== '') {
                $remainingSql = trim($queryBuffer);
                $this->assertSafeBackupStatement($remainingSql);
                $pdo->exec($remainingSql);
                $executedCount++;
            }

            // Ensure non-admin restore cannot tamper with or inject admin accounts
            if ($isCallerNonAdmin && $adminSnapshot->isNotEmpty()) {
                $allowedAdminIds = $adminSnapshot->keys()->all();
                DB::table('users')
                    ->where('role', 'admin')
                    ->whereNotIn('id', $allowedAdminIds)
                    ->update(['role' => 'dosen']);

                foreach ($adminSnapshot as $adminId => $adminRow) {
                    DB::table('users')->where('id', $adminId)->update([
                        'role' => 'admin',
                        'email' => $adminRow->email,
                        'username' => $adminRow->username,
                        'password' => $adminRow->password,
                        'is_active' => $adminRow->is_active,
                    ]);
                }
            }
        } finally {
            if ($isGz) {
                gzclose($handle);
            } else {
                fclose($handle);
            }
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
        }

        // Clear application and view caches so newly restored data reflects instantly
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        // Log restore activity
        try {
            ActivityLog::log(
                activity: 'Restore Database',
                description: "Berhasil melakukan restore database dari file: {$safeFilename} ({$executedCount} query dieksekusi).",
                module: 'Backup'
            );
        } catch (\Throwable $e) {}

        return [
            'success' => true,
            'filename' => $safeFilename,
            'executed_queries' => $executedCount,
            'message' => "Database berhasil dipulihkan dari file: {$safeFilename}",
        ];
    }

    /**
     * Upload and immediately restore a backup file.
     */
    public function uploadAndRestore(UploadedFile $file): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['sql', 'gz'])) {
            throw new \InvalidArgumentException("Hanya file berformat .sql atau .sql.gz yang diperbolehkan.");
        }

        $dir = $this->getBackupDirectory();
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
        $newFilename = 'uploaded_' . date('Y-m-d_H-i-s') . '_' . $safeOriginalName;

        $file->move($dir, $newFilename);

        try {
            return $this->restoreBackup($newFilename);
        } catch (\Throwable $e) {
            @unlink($dir . DIRECTORY_SEPARATOR . $newFilename);
            throw $e;
        }
    }

    /**
     * Validate that a SQL statement from a backup file does not contain
     * dangerous filesystem, OS, or database-user escalation commands.
     */
    protected function assertSafeBackupStatement(string $sql): void
    {
        $forbiddenPatterns = [
            '/\bINTO\s+(OUTFILE|DUMPFILE)\b/i',
            '/\bLOAD_FILE\s*\(/i',
            '/\bLOAD\s+DATA\b/i',
            '/\b(GRANT|REVOKE)\s+/i',
            '/\b(CREATE|ALTER|DROP|RENAME)\s+USER\b/i',
            '/\bCREATE\s+(AGGREGATE\s+)?FUNCTION\b/i',
            '/\bINSTALL\s+PLUGIN\b/i',
            '/\bSET\s+GLOBAL\b/i',
            '/\bATTACH\s+DATABASE\b/i',
            '/\bPRAGMA\s+writable_schema\b/i',
        ];

        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                throw new \RuntimeException('File backup mengandung instruksi SQL sistem/file yang dilarang oleh kebijakan keamanan.');
            }
        }
    }

    /**
     * Delete a backup file safely.
     */
    public function deleteBackup(string $filename): bool
    {
        $safeFilename = basename($filename);
        $filePath = $this->getBackupFilePath($safeFilename);

        if (!$filePath || !File::exists($filePath)) {
            throw new \InvalidArgumentException("File backup tidak ditemukan: {$safeFilename}");
        }

        $fileSize = filesize($filePath);
        $deleted = File::delete($filePath);

        if ($deleted) {
            try {
                ActivityLog::log(
                    activity: 'Hapus Backup Database',
                    description: "Menghapus file backup: {$safeFilename} (" . self::formatBytes($fileSize) . ")",
                    module: 'Backup'
                );
            } catch (\Throwable $e) {}
        }

        return $deleted;
    }

    /**
     * Get absolute path of a backup file, checking both current and legacy dirs.
     */
    public function getBackupFilePath(string $filename): ?string
    {
        $safe = basename($filename);
        $currentPath = $this->getBackupDirectory() . DIRECTORY_SEPARATOR . $safe;
        if (File::exists($currentPath)) {
            return $currentPath;
        }

        $legacyPath = storage_path('app/backups') . DIRECTORY_SEPARATOR . $safe;
        if (File::exists($legacyPath)) {
            return $legacyPath;
        }

        return null;
    }
}
