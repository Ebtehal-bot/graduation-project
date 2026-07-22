<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupListCommand extends Command
{
    protected $signature = 'backup:list';

    protected $description = 'عرض قائمة النسخ الاحتياطية';

    public function handle(BackupService $backupService): int
    {
        $backups = $backupService->listBackups();

        if (empty($backups)) {
            $this->warn('لا توجد نسخ احتياطية');
            return Command::SUCCESS;
        }

        $this->info('النسخ الاحتياطية المتاحة:');
        $this->line(str_repeat('-', 80));
        $this->line(sprintf('%-40s %-15s %s', 'اسم الملف', 'الحجم', 'التاريخ'));
        $this->line(str_repeat('-', 80));

        foreach ($backups as $backup) {
            $this->line(sprintf('%-40s %-15s %s',
                $backup['filename'],
                $backup['size'],
                $backup['created_at']
            ));
        }

        return Command::SUCCESS;
    }
}
