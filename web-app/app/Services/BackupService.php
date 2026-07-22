<?php

namespace App\Services;

use App\Models\Setting;

class BackupService
{
    protected string $backupDisk = 'backup';

    protected string $mysqldumpPath;

    protected string $mysqlPath;

    protected int $dumpTimeout;

    public function __construct()
    {
        $dumpConfig = config('database.connections.mysql.dump', []);
        $binaryPath = $dumpConfig['dump_binary_path'] ?? $this->detectBinaryPath();
        $this->dumpTimeout = $dumpConfig['timeout'] ?? 300;

        $this->mysqldumpPath = $binaryPath . DIRECTORY_SEPARATOR . 'mysqldump.exe';
        $this->mysqlPath = $binaryPath . DIRECTORY_SEPARATOR . 'mysql.exe';
    }

    public function verifyProvider(string $provider): array
    {
        return match ($provider) {
            'local' => $this->verifyLocal(),
            'external' => $this->verifyExternalDisk(),
            default => ['status' => 'ACTIVE', 'message' => 'جاهز'],
        };
    }

    protected function verifyLocal(): array
    {
        $backupDir = storage_path('app/backup');
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }
        if (is_writable($backupDir)) {
            return ['status' => 'ACTIVE', 'message' => 'التخزين المحلي جاهز'];
        }
        return ['status' => 'FAILED', 'message' => 'لا يمكن الكتابة في مسار التخزين المحلي'];
    }

    protected function verifyExternalDisk(): array
    {
        $paths = [
            'D:\\',
            'E:\\',
            'F:\\',
            'G:\\',
            'H:\\',
            'I:\\',
        ];

        $usbDrive = Setting::getValue('backup_external_disk_path', '');

        if (!empty($usbDrive) && is_dir($usbDrive)) {
            $testFile = $usbDrive . 'orphan_backup_test.tmp';
            if (@file_put_contents($testFile, 'test') !== false) {
                @unlink($testFile);
                $free = disk_free_space($usbDrive);
                return [
                    'status' => 'ACTIVE',
                    'message' => 'القرص الخارجي متصل - المساحة الحرة: ' . $this->formatSize((int)$free),
                ];
            }
            return ['status' => 'FAILED', 'message' => 'القرص الخارجي غير قابل للكتابة: ' . $usbDrive];
        }

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $testFile = $path . 'orphan_backup_test.tmp';
                if (@file_put_contents($testFile, 'test') !== false) {
                    @unlink($testFile);
                    $free = disk_free_space($path);
                    $label = 'القرص ' . rtrim($path, '\\');
                    return [
                        'status' => 'ACTIVE',
                        'message' => $label . ' متصل - المساحة الحرة: ' . $this->formatSize((int)$free),
                    ];
                }
            }
        }

        if (!empty($usbDrive)) {
            return ['status' => 'FAILED', 'message' => 'المسار غير موجود: ' . $usbDrive];
        }

        return ['status' => 'NOT_CONFIGURED', 'message' => 'لم يتم العثور على قرص خارجي متصل'];
    }

    protected function detectBinaryPath(): string
    {
        $candidates = [
            'C:\\xampp\\mysql\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.31-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.32-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.33-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.34-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.35-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.36-winx64\\bin',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.37-winx64\\bin',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . 'mysqldump.exe')) {
                return $path;
            }
        }

        return 'C:\\xampp\\mysql\\bin';
    }

    public function run(): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $dbName = config('database.connections.mysql.database');
        $sqlFile = "{$dbName}_{$timestamp}.sql";
        $zipFile = "{$dbName}_{$timestamp}.zip";
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'tmp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $sqlPath = $tempDir . DIRECTORY_SEPARATOR . $sqlFile;
        $backupDir = storage_path('app' . DIRECTORY_SEPARATOR . 'backup');
        $zipPath = $backupDir . DIRECTORY_SEPARATOR . $zipFile;

        try {
            $dumpResult = $this->createDatabaseDump($sqlPath);
            if (!$dumpResult['success']) {
                $this->updateSettingsAfterFailure($dumpResult['message']);
                return $dumpResult;
            }

            $sourceDirs = $this->getSourceFilePaths();

            $zipResult = $this->createZipArchive($sqlPath, $zipPath, $sqlFile, $sourceDirs);
            if (!$zipResult['success']) {
                $this->updateSettingsAfterFailure($zipResult['message']);
                return $zipResult;
            }

            $this->cleanOldBackups($zipPath);

            if (file_exists($sqlPath)) {
                unlink($sqlPath);
            }

            if (is_dir($tempDir)) {
                $this->removeDirectory($tempDir);
            }

            if (!file_exists($zipPath)) {
                return ['success' => false, 'message' => 'Backup file was unexpectedly removed'];
            }

            $fileSize = filesize($zipPath);
            $sizeFormatted = $this->formatSize($fileSize);

            $uploadResult = $this->uploadToExternal($zipPath, $zipFile);

            $statusMessage = 'تم إنشاء النسخة الاحتياطية بنجاح';
            if ($uploadResult['success'] && $uploadResult['destination'] !== 'local') {
                $statusMessage = $uploadResult['message'];
            }

            Setting::setValue('backup_last_date', now()->toDateTimeString(), 'backup', 'string');
            Setting::setValue('backup_last_size', $sizeFormatted, 'backup', 'string');
            Setting::setValue('backup_status', $statusMessage, 'backup', 'string');
            Setting::setValue('backup_next_date', $this->calculateNextBackupDate(), 'backup', 'string');

            $this->cleanOldBackups();

            if (is_dir($tempDir)) {
                $this->removeDirectory($tempDir);
            }

            return [
                'success' => true,
                'message' => $statusMessage,
                'file' => $zipFile,
                'path' => $zipPath,
                'size' => $sizeFormatted,
                'size_bytes' => $fileSize,
                'created_at' => now()->toDateTimeString(),
                'upload' => $uploadResult,
            ];
        } catch (\Exception $e) {
            $this->updateSettingsAfterFailure($e->getMessage());

            if (is_dir($tempDir)) {
                $this->removeDirectory($tempDir);
            }

            return [
                'success' => false,
                'message' => 'فشل إنشاء النسخة الاحتياطية: ' . $e->getMessage(),
            ];
        }
    }

    public function restore(string $backupFile): array
    {
        $backupPath = storage_path('app' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . $backupFile);

        if (!file_exists($backupPath)) {
            return [
                'success' => false,
                'message' => 'ملف النسخة الاحتياطية غير موجود: ' . $backupFile,
            ];
        }

        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'restore_tmp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($backupPath) !== true) {
                return ['success' => false, 'message' => 'فشل فتح الملف المضغوط'];
            }

            $zip->extractTo($tempDir);
            $zip->close();

            $files = array_diff(scandir($tempDir), ['.', '..']);
            $sqlFile = null;
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $sqlFile = $tempDir . DIRECTORY_SEPARATOR . $file;
                    break;
                }
            }

            if (!$sqlFile) {
                $this->removeDirectory($tempDir);
                return ['success' => false, 'message' => 'لم يتم العثور على ملف SQL داخل النسخة الاحتياطية'];
            }

            $importResult = $this->importDatabaseDump($sqlFile);

            $this->removeDirectory($tempDir);

            if (!$importResult['success']) {
                return $importResult;
            }

            Setting::setValue('backup_status', 'تمت استعادة النسخة الاحتياطية بنجاح', 'backup', 'string');

            return [
                'success' => true,
                'message' => 'تمت استعادة النسخة الاحتياطية بنجاح',
                'file' => $backupFile,
            ];
        } catch (\Exception $e) {
            if (is_dir($tempDir)) {
                $this->removeDirectory($tempDir);
            }

            return [
                'success' => false,
                'message' => 'فشل استعادة النسخة الاحتياطية: ' . $e->getMessage(),
            ];
        }
    }

    public function listBackups(): array
    {
        $backupDir = storage_path('app' . DIRECTORY_SEPARATOR . 'backup');
        $backups = [];

        if (!is_dir($backupDir)) {
            return $backups;
        }

        $files = array_diff(scandir($backupDir), ['.', '..', 'tmp', 'restore_tmp']);

        foreach ($files as $file) {
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'filename' => $file,
                    'size' => $this->formatSize(filesize($filePath)),
                    'size_bytes' => filesize($filePath),
                    'created_at' => date('Y-m-d H:i:s', filemtime($filePath)),
                ];
            }
        }

        rsort($backups);
        return $backups;
    }

    protected function getSourceFilePaths(): array
    {
        $paths = config('backup.backup.source.files.include', []);
        $excludes = config('backup.backup.source.files.exclude', []);
        $resolved = [];

        foreach ($paths as $path) {
            if (is_dir($path) && !in_array($path, $excludes)) {
                $resolved[] = $path;
            }
        }

        return $resolved;
    }

    protected function createDatabaseDump(string $outputPath): array
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        if (!file_exists($this->mysqldumpPath)) {
            return ['success' => false, 'message' => 'mysqldump not found at: ' . $this->mysqldumpPath];
        }

        $args = [
            $this->mysqldumpPath,
            '--host=' . $dbHost,
            '--port=' . $dbPort,
            '--user=' . $dbUser,
            '--single-transaction',
            '--routines',
            '--events',
            '--triggers',
            '--add-drop-table',
            '--extended-insert',
            '--force',
            '--no-tablespaces',
            '--default-character-set=utf8mb4',
        ];

        if (!empty($dbPass)) {
            $args[] = '--password=' . $dbPass;
        }

        $args[] = $dbName;

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($args, $descriptors, $pipes);

        if (!is_resource($process)) {
            return ['success' => false, 'message' => 'Failed to start mysqldump process'];
        }

        fclose($pipes[0]);

        $dumpContent = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        file_put_contents($outputPath, $dumpContent);

        if ($returnCode !== 0 || empty(trim($dumpContent))) {
            $errorMsg = !empty(trim($errorOutput))
                ? trim($errorOutput)
                : 'mysqldump returned code ' . $returnCode . ' with no output';
            return ['success' => false, 'message' => $errorMsg];
        }

        return ['success' => true, 'message' => 'Database dump created successfully'];
    }

    protected function importDatabaseDump(string $sqlFile): array
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $sqlContent = file_get_contents($sqlFile);

        if ($sqlContent === false || trim($sqlContent) === '') {
            return ['success' => false, 'message' => 'ملف SQL فارغ أو غير قابل للقراءة'];
        }

        if (!file_exists($this->mysqlPath)) {
            return ['success' => false, 'message' => 'mysql غير موجود في المسار: ' . $this->mysqlPath];
        }

        $args = [
            $this->mysqlPath,
            '--host=' . $dbHost,
            '--port=' . $dbPort,
            '--user=' . $dbUser,
        ];

        if (!empty($dbPass)) {
            $args[] = '--password=' . $dbPass;
        }

        $args[] = $dbName;

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($args, $descriptors, $pipes);

        if (!is_resource($process)) {
            return ['success' => false, 'message' => 'فشل تشغيل mysql'];
        }

        fwrite($pipes[0], $sqlContent);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        if ($returnCode !== 0) {
            $errorMsg = !empty(trim($errorOutput)) ? trim($errorOutput) : 'mysql returned code ' . $returnCode;
            return ['success' => false, 'message' => $errorMsg];
        }

        return ['success' => true, 'message' => 'تم استيراد قاعدة البيانات بنجاح'];
    }

    protected function createZipArchive(string $sqlPath, string $destinationZip, string $sqlFileName, array $sourceDirs): array
    {
        $zip = new \ZipArchive();

        if ($zip->open($destinationZip, \ZipArchive::CREATE) !== true) {
            return ['success' => false, 'message' => 'فشل إنشاء الملف المضغوط'];
        }

        if (file_exists($sqlPath)) {
            $zip->addFile($sqlPath, $sqlFileName);
        }

        foreach ($sourceDirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $dirName = basename($dir);

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $relativePath = $dirName . '/' . $file->getFilename();
                    $zip->addFile($file->getRealPath(), $relativePath);
                }
            }
        }

        $zip->close();

        if (!file_exists($destinationZip)) {
            return ['success' => false, 'message' => 'فشل إنشاء الملف المضغوط'];
        }

        return ['success' => true, 'message' => 'تم إنشاء الملف المضغوط بنجاح'];
    }

    public function uploadToExternal(string $zipPath, string $zipFile): array
    {
        $destination = Setting::getValue('backup_storage_destination', 'local');

        return match ($destination) {
            'local_external' => $this->uploadToExternalDisk($zipPath, $zipFile),
            default => ['success' => true, 'message' => 'النسخة محلية فقط', 'destination' => 'local'],
        };
    }

    protected function uploadToExternalDisk(string $zipPath, string $zipFile): array
    {
        try {
            $diskPath = Setting::getValue('backup_external_disk_path', '');

            $candidates = empty($diskPath) ? ['D:\\', 'E:\\', 'F:\\', 'G:\\'] : [$diskPath];
            $targetDir = null;

            foreach ($candidates as $path) {
                if (is_dir($path) && is_writable($path)) {
                    $targetDir = $path . 'orphan_backups\\';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0755, true);
                    }
                    break;
                }
            }

            if (!$targetDir) {
                return ['success' => false, 'message' => 'لا يوجد قرص خارجي متاح للكتابة'];
            }

            $destPath = $targetDir . $zipFile;
            if (copy($zipPath, $destPath)) {
                return [
                    'success' => true,
                    'message' => 'تم نسخ الملف إلى ' . $targetDir,
                    'destination' => $destPath,
                ];
            }

            return ['success' => false, 'message' => 'فشل نسخ الملف إلى القرص الخارجي'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'القرص الخارجي: ' . $e->getMessage()];
        }
    }

    protected function cleanOldBackups(string $keepPath = ''): void
    {
        $retentionCount = max(1, (int) Setting::getValue('backup_retention_count', 30));
        $backupDir = storage_path('app' . DIRECTORY_SEPARATOR . 'backup');

        if (!is_dir($backupDir)) {
            return;
        }

        $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.zip');
        if (count($files) <= $retentionCount) {
            return;
        }

        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        $toDelete = array_slice($files, 0, count($files) - $retentionCount);

        foreach ($toDelete as $file) {
            if ($file === $keepPath) {
                continue;
            }
            @unlink($file);
        }
    }

    protected function calculateNextBackupDate(): string
    {
        $frequency = Setting::getValue('backup_frequency', 'yearly');
        $now = now();

        return match ($frequency) {
            'daily' => $now->addDay()->toDateString(),
            'weekly' => $now->addWeek()->toDateString(),
            'monthly' => $now->addMonth()->toDateString(),
            'yearly' => $now->addYear()->toDateString(),
            default => $now->addYear()->toDateString(),
        };
    }

    protected function updateSettingsAfterFailure(string $error): void
    {
        Setting::setValue('backup_status', 'فشل إنشاء النسخة الاحتياطية: ' . $error, 'backup', 'string');
    }

    protected function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    protected function removeDirectory(string $dir): void
    {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
