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

Schedule::command('projects:prune-temp-gallery')->daily();
