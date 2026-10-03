<?php

use App\Http\Controllers\CalculatorOptionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PublicCalculatorController;
use App\Http\Controllers\PublicProjectController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SettingController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $projects = Project::with('category')->where('status', 'Ongoing')->latest()->take(4)->get();

    return view('welcome', compact('projects'));
});

Route::get('/projects', [PublicProjectController::class, 'index'])->name('public.projects.index');
Route::get('/case-study/{slug}', [PublicProjectController::class, 'show'])->name('public.projects.show');

// Public pricing calculator: WAJIB di atas grup catch-all {any} agar tidak tertangkap auth middleware.
Route::get('/pricing-calculator', [PublicCalculatorController::class, 'index'])->name('public.calculator.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/admin', '/dashboard');
    Route::get('/dashboard', [RoutingController::class, 'index'])->name('dashboard');
    Route::post('manage/storage/sync-r2', [RoutingController::class, 'syncR2Storage'])
        ->middleware('throttle:10,1')
        ->name('manage.storage.sync-r2');

    // Project Archive Routes
    Route::get('manage/projects/archive', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::patch('manage/projects/{id}/restore', [ProjectController::class, 'restore'])
        ->middleware('throttle:30,1')
        ->name('projects.restore');
    Route::delete('manage/projects/{id}/force-delete', [ProjectController::class, 'forceDelete'])
        ->middleware('throttle:30,1')
        ->name('projects.force-delete');

    // Rute untuk menghapus 1 foto spesifik dari galeri
    Route::delete('manage/projects/{project}/gallery/{image}', [ProjectController::class, 'deleteGalleryImage'])
        ->middleware('throttle:30,1')
        ->name('projects.gallery.delete');

    // Rute upload gambar galeri sementara (asynchronous staged upload)
    Route::post('manage/projects/upload-temp-gallery', [ProjectController::class, 'uploadTempGallery'])
        ->middleware('throttle:60,1')
        ->name('projects.upload-temp-gallery');

    // Rute presigned upload video showcase ke Cloudflare R2
    Route::post('manage/projects/video/presign-upload', [ProjectController::class, 'presignVideoUpload'])
        ->middleware('throttle:30,1')
        ->name('projects.video.presign-upload');

    Route::resource('manage/projects', ProjectController::class)
        ->names('projects')
        ->parameters(['projects' => 'project'])
        ->middleware(['throttle:30,1']);
    Route::resource('categories', CategoryController::class)
        ->except(['show'])
        ->middleware(['throttle:30,1']);

    // Calculator: hapus 1 gambar spesifik dari opsi
    Route::delete('manage/calculator/{calculator}/image/{image}', [CalculatorOptionController::class, 'deleteImage'])
        ->middleware('throttle:30,1')
        ->name('calculator.image.delete');
    Route::resource('manage/calculator', CalculatorOptionController::class)
        ->except(['show'])
        ->names('calculator')
        ->parameters(['calculator' => 'calculator'])
        ->middleware(['throttle:30,1']);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('settings.update');

    // Database Backup Management (Cloudflare R2)
    Route::get('manage/backups', [DatabaseBackupController::class, 'index'])->name('backups.index');
    Route::post('manage/backups', [DatabaseBackupController::class, 'store'])
        ->middleware('throttle:5,60')
        ->name('backups.store');
    Route::get('manage/backups/{filename}/download', [DatabaseBackupController::class, 'download'])
        ->name('backups.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('throttle:3,1')
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Dynamic routing for Tailwick template pages (should be defined last to prevent conflicts with specific routes)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('{first}/{second}/{third}', [RoutingController::class, 'thirdLevel'])->name('third')->where(['first' => '[^.]+', 'second' => '[^.]+', 'third' => '[^.]+']);
    Route::get('{first}/{second}', [RoutingController::class, 'secondLevel'])->name('second')->where(['first' => '[^.]+', 'second' => '[^.]+']);
    Route::get('{any}', [RoutingController::class, 'root'])->name('any')->where('any', '[^.]+');
});
