<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('projects:prune-temp-gallery {--hours=24 : Age in hours to prune}', function () {
    $hours = (int) $this->option('hours');
    $files = Storage::disk('public')->files('projects/temp-gallery');
    $expiryTime = now()->subHours($hours)->timestamp;
    $count = 0;

    foreach ($files as $file) {
        if (Storage::disk('public')->lastModified($file) <= $expiryTime) {
            Storage::disk('public')->delete($file);
            $count++;
        }
    }

    $this->info("Pruned {$count} temporary gallery image(s) older than {$hours} hour(s).");
})->purpose('Prune abandoned temporary gallery upload files');

Artisan::command('projects:test-r2', function () {
    $this->info('Testing Cloudflare R2 configuration...');

    $bucket = config('filesystems.disks.r2.bucket');
    $endpoint = config('filesystems.disks.r2.endpoint');
    $key = config('filesystems.disks.r2.key');
    $url = config('filesystems.disks.r2.url');

    if (empty($bucket) || empty($key) || empty($endpoint)) {
        $this->error('Cloudflare R2 belum dikonfigurasi lengkap di berkas .env Anda.');
        $this->line('Pastikan variabel berikut sudah terisi:');
        $this->line('- R2_ACCESS_KEY_ID');
        $this->line('- R2_SECRET_ACCESS_KEY');
        $this->line('- R2_BUCKET');
        $this->line('- R2_ENDPOINT');
        $this->line('- R2_URL');

        return 1;
    }

    $this->line("Bucket     : {$bucket}");
    $this->line("Endpoint   : {$endpoint}");
    $this->line("Public URL : {$url}");

    try {
        $testFile = 'projects/videos/_test_probe_'.time().'.txt';
        $this->info("1. Menulis berkas uji coba ke R2: {$testFile}...");
        Storage::disk('r2')->put($testFile, 'Cloudflare R2 connection test: '.now());
        $this->info('   BERHASIL: Berkas berhasil diunggah ke R2.');

        $this->info('2. Memeriksa keberadaan berkas...');
        if (! Storage::disk('r2')->exists($testFile)) {
            throw new Exception('Berkas dilaporkan berhasil ditulis tetapi pemeriksaan exists() gagal.');
        }
        $this->info('   BERHASIL: Berkas terkonfirmasi ada di dalam bucket R2.');

        $this->info('3. Menguji pembuatan presigned temporary upload URL...');
        $presigned = Storage::disk('r2')->temporaryUploadUrl($testFile, now()->addMinutes(10));
        $presignedUrl = is_array($presigned) ? ($presigned['url'] ?? '') : (string) $presigned;
        $this->info('   BERHASIL: Presigned URL berhasil dibuat: '.substr($presignedUrl, 0, 60).'...');

        $this->info('4. Membersihkan berkas uji coba...');
        Storage::disk('r2')->delete($testFile);
        $this->info('   BERHASIL: Berkas uji coba berhasil dihapus dari R2.');

        $this->newLine();
        $this->info('Selamat! Semua pengujian koneksi Cloudflare R2 berhasil 100%.');

        return 0;
    } catch (Throwable $e) {
        $this->error('Pengujian R2 Gagal: '.$e->getMessage());

        return 1;
    }
})->purpose('Menguji koneksi, upload, presigned URL, dan penghapusan storage Cloudflare R2');

Schedule::command('projects:prune-temp-gallery')->daily();
Schedule::command('db:backup-r2')->monthlyOn(1, '02:00')->withoutOverlapping();
