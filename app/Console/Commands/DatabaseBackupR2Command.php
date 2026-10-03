<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Throwable;

class DatabaseBackupR2Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-r2 {--source=scheduled : Source of the backup (scheduled or manual)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create encrypted MySQL database dump and upload to Cloudflare R2 with retention pruning';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $source = (string) $this->option('source');
        if (! in_array($source, ['scheduled', 'manual'], true)) {
            $source = 'scheduled';
        }

        $this->info('Starting database backup to Cloudflare R2...');

        try {
            $result = $backupService->createBackup($source);

            $this->newLine();
            $this->info('Database backup completed successfully!');
            $this->table(
                ['Parameter', 'Value'],
                [
                    ['Filename', $result['filename']],
                    ['R2 Path', $result['r2_path']],
                    ['File Size', $result['size_formatted'].' ('.$result['size'].' bytes)'],
                    ['Source', $result['source']],
                    ['Duration', $result['duration'].'s'],
                    ['Pruned Archives', (string) $result['pruned_count']],
                ]
            );

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Database backup failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
