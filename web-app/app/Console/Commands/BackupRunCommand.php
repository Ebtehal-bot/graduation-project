<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use App\Models\Setting;
use Illuminate\Console\Command;

class BackupRunCommand extends Command
{
    protected $signature = 'backup:run';

    protected $description = 'إنشاء نسخة احتياطية كاملة للنظام';

    public function handle(BackupService $backupService): int
    {
        $this->info('جاري إنشاء النسخة الاحتياطية...');

        $result = $backupService->run();

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->line('   الملف: ' . $result['file']);
            $this->line('   الحجم: ' . $result['size']);
            $this->line('   التاريخ: ' . $result['created_at']);
            return Command::SUCCESS;
        }

        $this->error('❌ ' . $result['message']);
        return Command::FAILURE;
    }
}
