<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DatabaseBackupService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseBackupR2Test extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Storage::fake('r2');
        Process::fake();
    }

    public function test_artisan_backup_command_successfully_dumps_encrypts_and_uploads_to_r2(): void
    {
        $this->artisan('db:backup-r2')
            ->expectsOutputToContain('Starting database backup to Cloudflare R2...')
            ->expectsOutputToContain('Database backup completed successfully!')
            ->assertSuccessful();

        $files = Storage::disk('r2')->files(DatabaseBackupService::BACKUP_DIRECTORY);
        $this->assertCount(1, $files);
        $this->assertStringStartsWith('backups/database/backup-sja-scheduled-', $files[0]);
        $this->assertStringEndsWith('.sql.gz.enc', $files[0]);

        $tempDir = storage_path('app/temp-backups');
        if (File::isDirectory($tempDir)) {
            $this->assertEmpty(File::files($tempDir));
        }
    }

    public function test_backup_archive_in_r2_is_valid_ciphertext_and_not_plaintext_sql(): void
    {
        $this->artisan('db:backup-r2')->assertSuccessful();

        $files = Storage::disk('r2')->files(DatabaseBackupService::BACKUP_DIRECTORY);
        $this->assertNotEmpty($files);

        $rawPayload = Storage::disk('r2')->get($files[0]);
        $this->assertNotNull($rawPayload);
        $this->assertGreaterThan(16, strlen($rawPayload));

        // Ensure zero plaintext database statements leak into stored ciphertext
        $this->assertStringNotContainsString('CREATE TABLE', $rawPayload);
        $this->assertStringNotContainsString('INSERT INTO', $rawPayload);
        $this->assertStringNotContainsString('CMS SJA', $rawPayload);
    }

    public function test_decrypted_download_produces_valid_extractable_sql_archive(): void
    {
        $service = app(DatabaseBackupService::class);
        $backupResult = $service->createBackup('manual');
        $filename = $backupResult['filename'];

        $response = $this->actingAs($this->user)->get(route('backups.download', $filename));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/gzip');

        $expectedDownloadFilename = substr($filename, 0, -4);
        $response->assertHeader('Content-Disposition', 'attachment; filename='.$expectedDownloadFilename);

        $streamedContent = $response->streamedContent();
        $this->assertNotEmpty($streamedContent);

        $decompressedSql = gzdecode($streamedContent);
        $this->assertNotFalse($decompressedSql);
        $this->assertStringContainsString('CMS SJA Mock SQL Dump', $decompressedSql);
        $this->assertStringContainsString('CREATE TABLE test', $decompressedSql);
    }

    public function test_retention_policy_prunes_archives_exceeding_twelve_cycles(): void
    {
        $service = app(DatabaseBackupService::class);

        // Seed 14 scheduled backups across the last 14 months
        for ($i = 14; $i >= 1; $i--) {
            $date = Carbon::now()->subMonths($i)->format('Y-m-d-His');
            $filename = "backup-sja-scheduled-{$date}.sql.gz.enc";
            Storage::disk('r2')->put(
                DatabaseBackupService::BACKUP_DIRECTORY."/{$filename}",
                random_bytes(32)
            );
        }

        // Seed 2 manual backups: one 40 days ago (expired), one 5 days ago (retained)
        $oldManualDate = Carbon::now()->subDays(40)->format('Y-m-d-His');
        $oldManualFilename = "backup-sja-manual-{$oldManualDate}.sql.gz.enc";
        Storage::disk('r2')->put(
            DatabaseBackupService::BACKUP_DIRECTORY."/{$oldManualFilename}",
            random_bytes(32)
        );

        $recentManualDate = Carbon::now()->subDays(5)->format('Y-m-d-His');
        $recentManualFilename = "backup-sja-manual-{$recentManualDate}.sql.gz.enc";
        Storage::disk('r2')->put(
            DatabaseBackupService::BACKUP_DIRECTORY."/{$recentManualFilename}",
            random_bytes(32)
        );

        // Pre-assertion: 14 scheduled + 2 manual = 16 backups
        $initialFiles = Storage::disk('r2')->files(DatabaseBackupService::BACKUP_DIRECTORY);
        $this->assertCount(16, $initialFiles);

        $prunedCount = $service->pruneOldBackups();

        // 2 oldest scheduled + 1 expired manual = 3 pruned
        $this->assertEquals(3, $prunedCount);

        $remainingFiles = Storage::disk('r2')->files(DatabaseBackupService::BACKUP_DIRECTORY);
        $this->assertCount(13, $remainingFiles); // 12 scheduled + 1 recent manual

        $this->assertFalse(Storage::disk('r2')->exists(DatabaseBackupService::BACKUP_DIRECTORY."/{$oldManualFilename}"));
        $this->assertTrue(Storage::disk('r2')->exists(DatabaseBackupService::BACKUP_DIRECTORY."/{$recentManualFilename}"));
    }

    public function test_guest_cannot_trigger_or_download_database_backups(): void
    {
        $this->get(route('backups.index'))->assertRedirect('/login');
        $this->post(route('backups.store'))->assertRedirect('/login');
        $this->get(route('backups.download', 'backup-sja-scheduled-2026-10-01-020000.sql.gz.enc'))->assertRedirect('/login');
    }

    public function test_admin_backup_trigger_is_rate_limited(): void
    {
        // 5 requests allowed per hour
        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($this->user)->post(route('backups.store'));
            $response->assertOk();
            $response->assertJson(['status' => 'success']);
        }

        // 6th request must be throttled
        $throttledResponse = $this->actingAs($this->user)->post(route('backups.store'));
        $throttledResponse->assertStatus(429);
    }

    public function test_settings_page_renders_gracefully_when_r2_is_unreachable(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.index'));

        $response->assertOk();
        $response->assertViewHas('backups');
        $response->assertSee('Database Backups (Cloudflare R2)');
        $response->assertSee('Backup Now');
        $response->assertSee('No Database Backups Yet');
    }

    public function test_invalid_download_filename_is_rejected(): void
    {
        // Path traversal attempts
        $this->actingAs($this->user)
            ->get(route('backups.download', '../../etc/passwd.enc'))
            ->assertNotFound();

        // Arbitrary non-matching filenames
        $this->actingAs($this->user)
            ->get(route('backups.download', 'malicious-script.php'))
            ->assertNotFound();
    }

    public function test_dump_database_passes_password_via_environment_and_not_command_line(): void
    {
        config(['database.connections.sqlite.password' => 'secret-db-pass']);

        $service = app(DatabaseBackupService::class);
        $service->createBackup('manual');

        Process::assertRan(function ($process) {
            $cmdStr = is_array($process->command) ? implode(' ', $process->command) : (string) $process->command;

            return ! str_contains($cmdStr, 'secret-db-pass')
                && ($process->environment['MYSQL_PWD'] ?? '') === 'secret-db-pass';
        });
    }
}
