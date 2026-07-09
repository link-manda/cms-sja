# Project Gallery Hardening Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Harden project image gallery so upload, delete, public rendering, and admin verification stay safe and test-covered.

**Architecture:** Keep existing Laravel/Blade structure. Put gallery storage/delete consistency in `ProjectService`, keep request validation in FormRequest, scope delete routes through the owning project, and add feature tests before behavior changes. No package, no media-library abstraction, no thumbnail generator yet.

**Tech Stack:** Laravel 13, PHP 8.3, Blade, Eloquent, Laravel Storage fake, PHPUnit 12.

---

## File Map

- Create: `tests/Feature/ProjectGalleryTest.php` — gallery behavior tests.
- Modify: `app/Http/Requests/UpdateProjectRequest.php` — enforce total gallery max 10 per project.
- Modify: `app/Services/ProjectService.php` — centralize gallery upload/delete cleanup.
- Modify: `app/Http/Controllers/ProjectController.php` — scoped gallery delete action, audit log, eager load show relations.
- Modify: `routes/web.php` — nested delete route: project + gallery image ID.
- Modify: `resources/views/projects/edit.blade.php` — update delete URL to scoped route.
- Modify: `resources/views/projects/show.blade.php` — add read-only gallery and Edit button.
- Modify: `resources/views/public/projects/show.blade.php` — add `loading`/`decoding` to gallery images.
- Modify: `app/Models/ProjectImage.php` — use Laravel 13 `#[Fillable]` attribute convention.

## Guardrails

- Total gallery cap: max 10 gallery images per project, excluding main image.
- Soft delete project: keep main + gallery files for restore.
- Force delete project: remove main + gallery files + gallery DB records.
- Single gallery delete: image must belong to project in route.
- Storage delete failure: do not delete DB record when file still exists but cannot be deleted.

---

### Task 1: Add failing gallery behavior tests

**Files:**
- Create: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Create gallery feature test file**

Create `tests/Feature/ProjectGalleryTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectGalleryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_store_project_with_gallery_images(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $galleryImages = [
            UploadedFile::fake()->image('gallery-one.jpg'),
            UploadedFile::fake()->image('gallery-two.webp'),
        ];

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Gallery Project',
            'slug' => 'gallery-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Project with gallery images.',
            'image' => UploadedFile::fake()->image('main.jpg'),
            'status' => 'Ongoing',
            'gallery_images' => $galleryImages,
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('slug', 'gallery-project')->firstOrFail();
        $this->assertCount(2, $project->images);

        foreach ($project->images as $image) {
            $this->assertStringStartsWith('projects/gallery/', $image->image_path);
            Storage::disk('public')->assertExists($image->image_path);
        }
    }

    public function test_authenticated_user_can_append_gallery_images_when_updating_project(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image' => 'main.jpg',
        ]);

        $response = $this->actingAs($this->user)->put(route('projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'category_id' => $category->id,
            'location' => $project->location,
            'description' => $project->description,
            'status' => $project->status,
            'gallery_images' => [UploadedFile::fake()->image('new-gallery.jpg')],
        ]);

        $response->assertRedirect(route('projects.index'));

        $project->refresh();
        $this->assertCount(1, $project->images);
        Storage::disk('public')->assertExists($project->images->first()->image_path);
    }

    public function test_gallery_upload_rejects_more_than_ten_total_images_on_update(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'category_id' => $category->id,
            'image' => 'main.jpg',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $path = "projects/gallery/existing-{$i}.jpg";
            Storage::disk('public')->put($path, 'fake image');
            ProjectImage::create([
                'project_id' => $project->id,
                'image_path' => $path,
            ]);
        }

        $response = $this->actingAs($this->user)->from(route('projects.edit', $project))->put(route('projects.update', $project), [
            'title' => $project->title,
            'slug' => $project->slug,
            'category_id' => $category->id,
            'location' => $project->location,
            'description' => $project->description,
            'status' => $project->status,
            'gallery_images' => [UploadedFile::fake()->image('too-many.jpg')],
        ]);

        $response
            ->assertRedirect(route('projects.edit', $project))
            ->assertSessionHasErrors('gallery_images');

        $this->assertCount(10, $project->refresh()->images);
    }

    public function test_public_project_case_study_shows_gallery_carousel_images(): void
    {
        $project = Project::factory()->create([
            'title' => 'Public Gallery Project',
            'slug' => 'public-gallery-project',
            'image' => 'main.jpg',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/public-one.jpg',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('id="project-carousel"', false);
        $response->assertSee('storage/projects/gallery/public-one.jpg', false);
        $response->assertSee('carousel-thumb', false);
        $response->assertSee('gallery-lightbox', false);
    }

    public function test_authenticated_user_can_delete_scoped_gallery_image(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create();
        Storage::disk('public')->put('projects/gallery/delete-me.jpg', 'fake image');
        $image = ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/delete-me.jpg',
        ]);

        $response = $this->actingAs($this->user)->delete(route('projects.gallery.delete', [$project, $image->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Gallery image deleted successfully!');
        $this->assertDatabaseMissing('project_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('projects/gallery/delete-me.jpg');
    }

    public function test_force_delete_project_removes_gallery_files(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create([
            'image' => 'main.jpg',
        ]);
        Storage::disk('public')->put('projects/main.jpg', 'fake main image');
        Storage::disk('public')->put('projects/gallery/remove-me.jpg', 'fake gallery image');
        ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/remove-me.jpg',
        ]);

        $this->actingAs($this->user)->delete(route('projects.destroy', $project));
        $response = $this->actingAs($this->user)->delete(route('projects.force-delete', $project->id));

        $response->assertRedirect(route('projects.archive'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        $this->assertDatabaseMissing('project_images', ['project_id' => $project->id]);
        Storage::disk('public')->assertMissing('projects/main.jpg');
        Storage::disk('public')->assertMissing('projects/gallery/remove-me.jpg');
    }
}
```

- [ ] **Step 2: Run gallery tests and confirm failures**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php
```

Expected before implementation:

```text
FAILED
```

At least these failures should appear:

- route signature mismatch for `projects.gallery.delete`
- total gallery > 10 not rejected
- public/admin gallery behavior not fully covered yet

- [ ] **Step 3: Commit failing tests**

```bash
git add tests/Feature/ProjectGalleryTest.php
git commit -m "test: cover project gallery behavior"
```

---

### Task 2: Enforce total gallery max 10 per project

**Files:**
- Modify: `app/Http/Requests/UpdateProjectRequest.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Add Validator import**

In `app/Http/Requests/UpdateProjectRequest.php`, add:

```php
use Illuminate\Validation\Validator;
```

near existing imports.

- [ ] **Step 2: Add total gallery cap validator**

Add this method inside `UpdateProjectRequest` after `rules()`:

```php
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $project = $this->route('project');
            $uploadedImages = $this->file('gallery_images', []);

            if (! is_object($project) || empty($uploadedImages)) {
                return;
            }

            if ($project->images()->count() + count($uploadedImages) > 10) {
                $validator->errors()->add('gallery_images', 'Gallery may not contain more than 10 photos.');
            }
        });
    }
```

- [ ] **Step 3: Run max limit test**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter=gallery_upload_rejects_more_than_ten_total_images_on_update
```

Expected:

```text
PASS
```

- [ ] **Step 4: Commit validation guard**

```bash
git add app/Http/Requests/UpdateProjectRequest.php tests/Feature/ProjectGalleryTest.php
git commit -m "fix(gallery): enforce total image limit"
```

---

### Task 3: Centralize gallery upload and delete consistency in service

**Files:**
- Modify: `app/Services/ProjectService.php`
- Modify: `app/Http/Controllers/ProjectController.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Add imports to ProjectService**

In `app/Services/ProjectService.php`, add:

```php
use Illuminate\Support\Facades\DB;
use Throwable;
```

near existing imports.

- [ ] **Step 2: Replace createProject with transaction and helper call**

Replace `createProject()` with:

```php
    public function createProject(array $data): Project
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        }

        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        return DB::transaction(function () use ($data, $galleryImages) {
            $project = Project::create($data);
            $this->storeGalleryImages($project, $galleryImages);

            return $project;
        });
    }
```

- [ ] **Step 3: Replace updateProject with transaction and helper call**

Replace `updateProject()` with:

```php
    public function updateProject(Project $project, array $data): bool
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($project->image) {
                Storage::disk('public')->delete('projects/'.$project->image);
            }

            $filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
            $data['image']->storeAs('projects', $filename, 'public');
            $data['image'] = $filename;
        } else {
            unset($data['image']);
        }

        $galleryImages = $data['gallery_images'] ?? [];
        unset($data['gallery_images']);

        return DB::transaction(function () use ($project, $data, $galleryImages) {
            $updated = $project->update($data);
            $this->storeGalleryImages($project, $galleryImages);

            return $updated;
        });
    }
```

- [ ] **Step 4: Add gallery helper and scoped delete method**

Add these private/public methods before `deleteProject()`:

```php
    private function storeGalleryImages(Project $project, array $images): void
    {
        $storedPaths = [];

        try {
            foreach ($images as $image) {
                if (! $image instanceof UploadedFile) {
                    continue;
                }

                $filename = Str::random(40).'.'.$image->getClientOriginalExtension();
                $path = 'projects/gallery/'.$filename;

                $image->storeAs('projects/gallery', $filename, 'public');
                $storedPaths[] = $path;

                $project->images()->create(['image_path' => $path]);
            }
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }
    }

    public function deleteGalleryImage(Project $project, int $imageId): bool
    {
        $image = $project->images()->findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image_path) && ! Storage::disk('public')->delete($image->image_path)) {
            return false;
        }

        return (bool) $image->delete();
    }
```

- [ ] **Step 5: Update controller to use service delete**

In `app/Http/Controllers/ProjectController.php`, replace `deleteGalleryImage()` with:

```php
    public function deleteGalleryImage(Project $project, int $image): RedirectResponse
    {
        if (! $this->projectService->deleteGalleryImage($project, $image)) {
            return back()->with('error', 'Failed to delete gallery image file.');
        }

        Log::channel('audit')->info('Gallery image deleted', [
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'project_image_id' => $image,
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Gallery image deleted successfully!');
    }
```

- [ ] **Step 6: Run upload/delete/force-delete tests**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter='store_project_with_gallery|append_gallery|delete_scoped_gallery|force_delete_project_removes_gallery'
```

Expected: route delete test may still fail until Task 4; upload and force-delete should pass or improve.

- [ ] **Step 7: Commit service hardening**

```bash
git add app/Services/ProjectService.php app/Http/Controllers/ProjectController.php tests/Feature/ProjectGalleryTest.php
git commit -m "fix(gallery): centralize storage cleanup"
```

---

### Task 4: Scope gallery delete route to owning project

**Files:**
- Modify: `routes/web.php`
- Modify: `resources/views/projects/edit.blade.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Update route signature**

In `routes/web.php`, replace:

```php
    Route::delete('manage/projects/gallery/{id}', [ProjectController::class, 'deleteGalleryImage'])
        ->middleware('throttle:30,1')
        ->name('projects.gallery.delete');
```

with:

```php
    Route::delete('manage/projects/{project}/gallery/{image}', [ProjectController::class, 'deleteGalleryImage'])
        ->middleware('throttle:30,1')
        ->name('projects.gallery.delete');
```

- [ ] **Step 2: Update edit page delete action URL**

In `resources/views/projects/edit.blade.php`, replace:

```blade
data-action-url="{{ route('projects.gallery.delete', $image->id) }}"
```

with:

```blade
data-action-url="{{ route('projects.gallery.delete', [$project, $image->id]) }}"
```

- [ ] **Step 3: Run scoped delete test**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter=authenticated_user_can_delete_scoped_gallery_image
```

Expected:

```text
PASS
```

- [ ] **Step 4: Commit scoped route**

```bash
git add routes/web.php resources/views/projects/edit.blade.php tests/Feature/ProjectGalleryTest.php
git commit -m "fix(gallery): scope delete route to project"
```

---

### Task 5: Add admin detail read-only gallery

**Files:**
- Modify: `app/Http/Controllers/ProjectController.php`
- Modify: `resources/views/projects/show.blade.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Eager load images and category on show**

In `app/Http/Controllers/ProjectController.php`, replace `show()` with:

```php
    public function show(Project $project)
    {
        $project->load(['category', 'images']);

        return view('projects.show', compact('project'));
    }
```

- [ ] **Step 2: Add Edit Project button in detail header**

In `resources/views/projects/show.blade.php`, replace header action block:

```blade
            <a href="{{ route('projects.index') }}" class="btn btn-sm border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">
                 Back
             </a>
```

with:

```blade
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm bg-primary text-white cursor-pointer">
                    Edit Project
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-sm border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">
                    Back
                </a>
            </div>
```

- [ ] **Step 3: Add read-only gallery card after detail card**

In `resources/views/projects/show.blade.php`, insert this block after the closing `</div>` for the first `.card` and before `@endsection`:

```blade
    <div class="card mt-6">
        <div class="card-header flex justify-between items-center">
            <div>
                <h6 class="card-title text-base font-semibold text-default-800">Project Gallery</h6>
                <p class="text-sm text-default-500 mt-1">Read-only preview of photos shown on the public case study page.</p>
            </div>
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">
                Manage Gallery
            </a>
        </div>
        <div class="card-body">
            @if ($project->images->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($project->images as $image)
                        <div class="rounded-lg overflow-hidden border border-default-200 shadow-sm">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-32 object-cover" alt="{{ $project->title }} gallery image" loading="lazy" decoding="async">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-dashed border-default-300 bg-default-50 dark:bg-zinc-900 p-8 text-center text-sm text-default-500">
                    No gallery photos uploaded yet.
                </div>
            @endif
        </div>
    </div>
```

- [ ] **Step 4: Add admin show assertion to gallery test file**

Append this test method to `tests/Feature/ProjectGalleryTest.php` before final `}`:

```php
    public function test_admin_project_detail_shows_gallery_images(): void
    {
        $project = Project::factory()->create([
            'title' => 'Admin Gallery Project',
        ]);

        ProjectImage::create([
            'project_id' => $project->id,
            'image_path' => 'projects/gallery/admin-one.jpg',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.show', $project));

        $response->assertOk();
        $response->assertSee('Project Gallery');
        $response->assertSee('storage/projects/gallery/admin-one.jpg', false);
        $response->assertSee('Manage Gallery');
    }
```

- [ ] **Step 5: Run admin detail gallery test**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter=admin_project_detail_shows_gallery_images
```

Expected:

```text
PASS
```

- [ ] **Step 6: Commit admin detail gallery**

```bash
git add app/Http/Controllers/ProjectController.php resources/views/projects/show.blade.php tests/Feature/ProjectGalleryTest.php
git commit -m "feat(gallery): show gallery on admin detail"
```

---

### Task 6: Add public gallery image loading hints

**Files:**
- Modify: `resources/views/public/projects/show.blade.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Add loading hints to main carousel image**

In `resources/views/public/projects/show.blade.php`, replace:

```blade
<img id="main-carousel-img" src="{{ $allImages[0] }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-opacity duration-300 cursor-pointer" onclick="openLightbox(this.src)">
```

with:

```blade
<img id="main-carousel-img" src="{{ $allImages[0] }}" alt="{{ $project->title }}" class="w-full h-full object-cover transition-opacity duration-300 cursor-pointer" onclick="openLightbox(this.src)" decoding="async">
```

- [ ] **Step 2: Add loading hints to thumbnail images**

Replace:

```blade
<img src="{{ $img }}" class="w-full h-full object-cover">
```

with:

```blade
<img src="{{ $img }}" class="w-full h-full object-cover" alt="{{ $project->title }} gallery image {{ $index + 1 }}" loading="lazy" decoding="async">
```

- [ ] **Step 3: Add test assertion for lazy thumbnail**

In `test_public_project_case_study_shows_gallery_carousel_images()`, add:

```php
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('decoding="async"', false);
```

- [ ] **Step 4: Run public gallery test**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter=public_project_case_study_shows_gallery_carousel_images
```

Expected:

```text
PASS
```

- [ ] **Step 5: Commit public image loading hints**

```bash
git add resources/views/public/projects/show.blade.php tests/Feature/ProjectGalleryTest.php
git commit -m "perf(gallery): lazy load public thumbnails"
```

---

### Task 7: Align ProjectImage model convention

**Files:**
- Modify: `app/Models/ProjectImage.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Replace fillable property with attribute**

Replace `app/Models/ProjectImage.php` content with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'image_path'])]
class ProjectImage extends Model
{
    use HasFactory;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
```

- [ ] **Step 2: Run gallery tests**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php
```

Expected:

```text
PASS
```

- [ ] **Step 3: Commit model convention fix**

```bash
git add app/Models/ProjectImage.php tests/Feature/ProjectGalleryTest.php
git commit -m "refactor(gallery): align image model conventions"
```

---

### Task 8: Final verification

**Files:**
- No source edits unless checks fail.

- [ ] **Step 1: Run gallery tests**

Run:

```bash
php artisan test tests/Feature/ProjectGalleryTest.php
```

Expected:

```text
PASS
```

- [ ] **Step 2: Run project CRUD tests**

Run:

```bash
php artisan test tests/Feature/ProjectCrudTest.php
```

Expected:

```text
PASS
```

- [ ] **Step 3: Run full test suite**

Run:

```bash
composer test
```

Expected:

```text
Tests: 59 passed
```

Exact assertion count may vary.

- [ ] **Step 4: Build frontend**

Run:

```bash
npm run build
```

Expected:

```text
✓ built
```

Vite chunk-size warnings are acceptable unless new errors appear.

- [ ] **Step 5: Inspect changed files**

Run:

```bash
git diff --stat
```

Expected files only:

```text
app/Http/Controllers/ProjectController.php
app/Http/Requests/UpdateProjectRequest.php
app/Models/ProjectImage.php
app/Services/ProjectService.php
resources/views/projects/edit.blade.php
resources/views/projects/show.blade.php
resources/views/public/projects/show.blade.php
routes/web.php
tests/Feature/ProjectGalleryTest.php
```

- [ ] **Step 6: Final commit if verification required small fixes**

Only run if Step 1-5 required additional edits:

```bash
git add app routes resources tests
git commit -m "fix(gallery): finalize hardening"
```

---

## Deliberate skips

- Thumbnail generation skipped. Add only when real image payload hurts public performance.
- Role/policy system skipped. Current app treats verified admin area as trusted; scoped route reduces future IDOR risk cheaply.
- Lightbox next/prev skipped. UX nice-to-have, not security hardening.
- New package skipped. Laravel Storage + validation cover current need.
- Reworking create/edit preview JS skipped in this hardening pass except if time remains; server guard is source of truth.

## Self-review

- Spec coverage: gallery tests, total max 10, upload cleanup, scoped delete, admin detail read-only gallery, lazy/async public image hints, verification — covered.
- Placeholder scan: no `TBD`, no vague validation/error-handling steps without code.
- Type consistency: route uses `Project $project, int $image`; service uses `deleteGalleryImage(Project $project, int $imageId)`; tests call `route('projects.gallery.delete', [$project, $image->id])`.
