<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PublicProjectController;
use App\Http\Controllers\RoutingController;
use App\Http\Controllers\SettingController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $projects = Project::where('status', 'Ongoing')->latest()->take(4)->get();

    return view('welcome', compact('projects'));
});

Route::get('/projects', [PublicProjectController::class, 'index'])->name('public.projects.index');
Route::get('/case-study/{slug}', [PublicProjectController::class, 'show'])->name('public.projects.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [RoutingController::class, 'index'])->name('dashboard');

    // Project Archive Routes
    Route::get('manage/projects/archive', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::patch('manage/projects/{id}/restore', [ProjectController::class, 'restore'])
        ->middleware('throttle:10,1')
        ->name('projects.restore');
    Route::delete('manage/projects/{id}/force-delete', [ProjectController::class, 'forceDelete'])
        ->middleware('throttle:5,1')
        ->name('projects.force-delete');
    
    // Rute untuk menghapus 1 foto spesifik dari galeri
    Route::delete('manage/projects/{project}/gallery/{image}', [ProjectController::class, 'deleteGalleryImage'])
        ->middleware('throttle:30,1')
        ->name('projects.gallery.delete');

    Route::resource('manage/projects', ProjectController::class)
        ->names('projects')
        ->parameters(['projects' => 'project'])
        ->middleware(['throttle:30,1']);
    Route::resource('categories', CategoryController::class)
        ->middleware(['throttle:30,1']);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('settings.update');
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
