<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class BackupStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.backup-status';

    protected static ?int $sort = 6;

    protected function getViewData(): array
    {
        $backupDir = storage_path('app' . DIRECTORY_SEPARATOR . 'backup');
        $latestFile = null;
        $latestTime = 0;

        if (is_dir($backupDir)) {
            $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.zip');
            foreach ($files as $file) {
                $mtime = filemtime($file);
                if ($mtime > $latestTime) {
                    $latestTime = $mtime;
                    $latestFile = $file;
                }
            }
        }

        if ($latestFile && file_exists($latestFile)) {
            $sizeBytes = filesize($latestFile);
            $sizeFormatted = $this->formatBytes($sizeBytes);
            $lastBackup = Carbon::createFromTimestamp(filemtime($latestFile))->toDateTimeString();

            if ($sizeBytes < 15360) {
                $status = 'Warning: Backup completed but database dump may have failed (size too small)';
            } else {
                $status = 'Healthy: Backup completed successfully';
            }
        } else {
            $status = 'Warning: No backup files found';
            $lastBackup = 'Never';
            $sizeFormatted = 'N/A';
        }

        return [
            'status' => $status,
            'lastBackup' => $lastBackup,
            'nextBackup' => $this->getNextBackupDate(),
            'storage' => $this->getStorageDestination(),
            'size' => $sizeFormatted,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function getNextBackupDate(): string
    {
        return Setting::getValue('backup_next_date', 'Not scheduled');
    }

    private function getStorageDestination(): string
    {
        $destination = Setting::getValue('backup_storage_destination', 'local');
        $labels = [
            'local' => 'Local Storage',
            'local_google' => 'Local + Google Drive',
            'local_dropbox' => 'Local + Dropbox',
            'local_aws' => 'Local + Amazon S3',
            'local_do' => 'Local + DigitalOcean Spaces',
            'local_onedrive' => 'Local + OneDrive',
            'local_ftp' => 'Local + FTP',
        ];
        return $labels[$destination] ?? $destination;
    }
}
