<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DatabaseBackupController extends Controller
{
    /**
     * Return JSON list of existing database backups.
     */
    public function index(DatabaseBackupService $backupService): JsonResponse
    {
        $backups = $backupService->listBackups();

        return response()->json([
            'status' => 'success',
            'backups' => $backups,
        ]);
    }

    /**
     * Re-read the R2 bucket and refresh the cached backup list.
     */
    public function sync(DatabaseBackupService $backupService): JsonResponse
    {
        try {
            $count = count($backupService->syncFromStorage());

            return response()->json([
                'status' => 'success',
                'message' => $count === 1
                    ? 'Sync complete. 1 backup found in Cloudflare R2.'
                    : "Sync complete. {$count} backups found in Cloudflare R2.",
                'count' => $count,
            ]);
        } catch (Throwable $e) {
            Log::warning('Database backup sync failed: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed. Cloudflare R2 could not be reached. Your previous backup list is unchanged.',
            ], 500);
        }
    }

    /**
     * Trigger an on-demand manual database backup.
     */
    public function store(DatabaseBackupService $backupService): JsonResponse
    {
        try {
            $result = $backupService->createBackup('manual');

            return response()->json([
                'status' => 'success',
                'message' => 'Database backup created and encrypted to Cloudflare R2 successfully.',
                'backup' => $result,
            ]);
        } catch (Throwable $e) {
            Log::error('Manual database backup failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            $message = app()->hasDebugModeEnabled()
                ? 'Failed to create database backup: '.$e->getMessage()
                : 'Failed to create database backup. Please check the server logs.';

            return response()->json([
                'status' => 'error',
                'message' => $message,
            ], 500);
        }
    }

    /**
     * Download and stream decrypted backup archive (.sql.gz) on the fly.
     */
    public function download(string $filename, DatabaseBackupService $backupService): StreamedResponse
    {
        if (! preg_match('/^backup-sja-(scheduled|manual)-(\d{4}-\d{2}-\d{2}-\d{6})\.sql\.gz\.enc$/', $filename)) {
            abort(404, 'Invalid backup file.');
        }

        $r2Path = DatabaseBackupService::BACKUP_DIRECTORY.'/'.$filename;
        $downloadFilename = substr($filename, 0, -4); // Strip .enc extension

        try {
            $decryptedContent = $backupService->decryptFileStream($r2Path);

            return response()->streamDownload(
                function () use ($decryptedContent) {
                    echo $decryptedContent;
                },
                $downloadFilename,
                [
                    'Content-Type' => 'application/gzip',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                ]
            );
        } catch (Throwable $e) {
            Log::error('Failed to stream decrypted database backup: '.$e->getMessage(), [
                'filename' => $filename,
                'exception' => $e,
            ]);

            abort(404, 'Backup file not found or failed to decrypt.');
        }
    }
}
