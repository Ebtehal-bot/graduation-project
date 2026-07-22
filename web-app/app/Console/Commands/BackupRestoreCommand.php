<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRestoreCommand extends Command
{
    protected $signature = 'backup:restore {file : اسم ملف النسخة الاحتياطية}';

    protected $description = 'استعادة نسخة احتياطية';

    public function handle(BackupService $backupService): int
    {
        $file = $this->argument('file');

        if (!$this->confirm("سيتم استبدال قاعدة البيانات الحالية بالكامل. هل أنت متأكد؟", false)) {
            $this->info('تم إلغاء عملية الاستعادة.');
            return Command::SUCCESS;
        }

        $this->info('جاري استعادة النسخة الاحتياطية...');

        $result = $backupService->restore($file);

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            return Command::SUCCESS;
        }

        $this->error('❌ ' . $result['message']);
        return Command::FAILURE;
    }
}
