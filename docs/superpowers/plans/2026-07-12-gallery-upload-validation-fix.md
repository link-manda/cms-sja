# Gallery Upload Validation Fix Plan

> **Untuk eksekutor patch:** implementasi baru boleh dilakukan setelah plan ini direview. Steps pakai checkbox (`- [ ]`) untuk tracking. Scope sengaja kecil: validasi upload, pesan error, keamanan ekstensi file, dan UX drag & drop.

**Goal:** Perbaiki bug upload image gallery yang menolak file >4MB / <4MB tanpa pesan jelas, cegah format file tidak sesuai, dan pastikan file public tidak disimpan memakai ekstensi dari user filename.

**Architecture:** Tetap pakai Laravel FormRequest sebagai source of truth. UI drag & drop hanya preflight UX, bukan pengganti validasi server. Service layer tetap simpan file. Tidak tambah package, tidak bikin media library, tidak generate thumbnail.

**Tech Stack:** Laravel 13, PHP 8.3+, Blade, native JavaScript, PHPUnit 12.

---

## Root Cause

1. UI file input memakai `accept="image/*"`, lebih longgar dari server yang hanya menerima JPG/PNG/WEBP.
2. Drag & drop preview hanya cek `file.type.startsWith('image/')`, tidak cek ukuran, ekstensi, jumlah file, atau sisa slot gallery.
3. FormRequest belum punya pesan validasi custom, jadi error server kurang jelas untuk user.
4. FormRequest memakai `mimes`, tetapi belum `extensions`; Laravel docs menyatakan `mimes` cek MIME/konten, bukan user-assigned extension.
5. `ProjectService` menyimpan file memakai `getClientOriginalExtension()`, berarti ekstensi berasal dari nama file user.
6. `PostTooLargeException` render halaman 413, bukan balik ke form dengan field error.
7. Copy UI tidak menyebut batas resolusi `4096×4096`, sehingga file <4MB tetap bisa ditolak karena dimensi.

---

## File Map

- Modify: `app/Http/Requests/StoreProjectRequest.php` — rules + custom messages.
- Modify: `app/Http/Requests/UpdateProjectRequest.php` — rules + custom messages + existing total cap message.
- Modify: `app/Services/ProjectService.php` — safe detected extension for main/gallery image filenames.
- Modify: `resources/views/projects/create.blade.php` — exact accept list, UX copy, client preflight, visible error box.
- Modify: `resources/views/projects/edit.blade.php` — exact accept list, UX copy, client preflight with remaining slot count, visible error box.
- Modify: `bootstrap/app.php` — project upload 413 redirect back with clear error.
- Modify: `tests/Feature/ProjectGalleryTest.php` — upload validation regression tests.

---

## Guardrails

- Server validation remains source of truth.
- Client validation only improves UX; never rely on it for security.
- Allowed formats: JPG, JPEG, PNG, WEBP.
- Gallery file size: max 4096 KiB.
- Main image size: max 2048 KiB.
- Max resolution: 4096×4096 px.
- Gallery count: max 10 per project.
- Do not store user-controlled extension.
- No package, no thumbnail generation, no media-library abstraction.

---

## Task 1: Add upload validation regression tests

**Files:**
- Modify: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Add test for non-image disguised as `.jpg`**

Add method:

```php
public function test_gallery_upload_rejects_non_image_file_with_jpg_extension(): void
{
    Storage::fake('public');

    $category = Category::factory()->create();

    $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
        'title' => 'Invalid Gallery Project',
        'slug' => 'invalid-gallery-project',
        'category_id' => $category->id,
        'location' => 'Bali',
        'description' => 'Project with invalid gallery image.',
        'image' => UploadedFile::fake()->image('main.jpg'),
        'status' => 'Ongoing',
        'gallery_images' => [UploadedFile::fake()->create('bad.jpg', 10, 'text/plain')],
    ]);

    $response
        ->assertRedirect(route('projects.create'))
        ->assertSessionHasErrors('gallery_images.0');
}
```

- [ ] **Step 2: Add test for image with unsafe original extension**

Add method:

```php
public function test_gallery_upload_does_not_store_user_supplied_extension(): void
{
    Storage::fake('public');

    $category = Category::factory()->create();

    $response = $this->actingAs($this->user)->post(route('projects.store'), [
        'title' => 'Safe Extension Project',
        'slug' => 'safe-extension-project',
        'category_id' => $category->id,
        'location' => 'Bali',
        'description' => 'Project with safe stored extension.',
        'image' => UploadedFile::fake()->image('main.jpg'),
        'status' => 'Ongoing',
        'gallery_images' => [UploadedFile::fake()->image('shell.php')],
    ]);

    $response->assertRedirect(route('projects.index'));

    $project = Project::where('slug', 'safe-extension-project')->firstOrFail();
    $storedPath = $project->images()->firstOrFail()->image_path;

    $this->assertStringStartsWith('projects/gallery/', $storedPath);
    $this->assertStringNotEndsWith('.php', $storedPath);
    Storage::disk('public')->assertExists($storedPath);
}
```

Note: if `extensions` rule rejects `shell.php`, adjust expected behavior to session error instead. Preferred security behavior: reject bad extension and also never use original extension for stored names.

- [ ] **Step 3: Add test for oversized gallery image error**

Use fake upload size over 4096 KiB:

```php
public function test_gallery_upload_rejects_files_larger_than_four_mb(): void
{
    Storage::fake('public');

    $category = Category::factory()->create();

    $response = $this->actingAs($this->user)->from(route('projects.create'))->post(route('projects.store'), [
        'title' => 'Large Gallery Project',
        'slug' => 'large-gallery-project',
        'category_id' => $category->id,
        'location' => 'Bali',
        'description' => 'Project with large gallery image.',
        'image' => UploadedFile::fake()->image('main.jpg'),
        'status' => 'Ongoing',
        'gallery_images' => [UploadedFile::fake()->image('large.jpg')->size(4097)],
    ]);

    $response
        ->assertRedirect(route('projects.create'))
        ->assertSessionHasErrors('gallery_images.0');
}
```

- [ ] **Step 4: Run tests and confirm current behavior**

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter='non_image_file|user_supplied_extension|larger_than_four_mb'
```

Expected before patch:

- non-image rejected may already pass
- unsafe extension likely exposes current filename extension bug
- oversized rejected may pass but message is still generic

---

## Task 2: Harden server validation rules and messages

**Files:**
- Modify: `app/Http/Requests/StoreProjectRequest.php`
- Modify: `app/Http/Requests/UpdateProjectRequest.php`

- [ ] **Step 1: Add `extensions` to main image rules**

Store:

```php
'image' => 'required|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:2048|dimensions:max_width=4096,max_height=4096',
```

Update:

```php
'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:2048|dimensions:max_width=4096,max_height=4096',
```

- [ ] **Step 2: Add `extensions` to gallery image rules**

Both Store and Update:

```php
'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:4096|dimensions:max_width=4096,max_height=4096',
```

- [ ] **Step 3: Add custom messages to StoreProjectRequest**

Add method:

```php
public function messages(): array
{
    return [
        'image.image' => 'Foto utama harus berupa gambar valid.',
        'image.mimes' => 'Format foto utama harus JPG, PNG, atau WEBP.',
        'image.extensions' => 'Ekstensi foto utama harus .jpg, .jpeg, .png, atau .webp.',
        'image.max' => 'Foto utama maksimal 2 MB.',
        'image.dimensions' => 'Resolusi foto utama maksimal 4096×4096 piksel.',
        'gallery_images.max' => 'Maksimal 10 foto galeri.',
        'gallery_images.*.image' => 'File galeri harus berupa gambar valid.',
        'gallery_images.*.mimes' => 'Format foto galeri harus JPG, PNG, atau WEBP.',
        'gallery_images.*.extensions' => 'Ekstensi foto galeri harus .jpg, .jpeg, .png, atau .webp.',
        'gallery_images.*.max' => 'Setiap foto galeri maksimal 4 MB.',
        'gallery_images.*.dimensions' => 'Resolusi foto galeri maksimal 4096×4096 piksel.',
    ];
}
```

- [ ] **Step 4: Add same custom messages to UpdateProjectRequest**

Same method as Store. Keep existing `withValidator()` total-count guard.

- [ ] **Step 5: Make total-count message Indonesian**

In `UpdateProjectRequest::withValidator()` replace:

```php
Gallery may not contain more than 10 photos.
```

with:

```php
Maksimal 10 foto galeri per project.
```

- [ ] **Step 6: Run validation tests**

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter='non_image_file|larger_than_four_mb|gallery_upload_rejects_more_than_ten_total_images_on_update'
```

Expected:

```text
PASS
```

---

## Task 3: Stop storing user-controlled file extensions

**Files:**
- Modify: `app/Services/ProjectService.php`
- Test: `tests/Feature/ProjectGalleryTest.php`

- [ ] **Step 1: Replace main image create filename extension**

Replace:

```php
$filename = Str::random(40).'.'.$data['image']->getClientOriginalExtension();
```

with:

```php
$filename = Str::random(40).'.'.$data['image']->extension();
```

- [ ] **Step 2: Replace main image update filename extension**

Same replacement in `updateProject()`.

- [ ] **Step 3: Replace gallery filename extension**

Replace:

```php
$filename = Str::random(40).'.'.$image->getClientOriginalExtension();
```

with:

```php
$filename = Str::random(40).'.'.$image->extension();
```

- [ ] **Step 4: Run safe extension test**

```bash
php artisan test tests/Feature/ProjectGalleryTest.php --filter=user_supplied_extension
```

Expected:

```text
PASS
```

If `extensions` rule rejects `shell.php`, update test to assert session error instead and add separate test that valid `.jpg` stores detected extension.

---

## Task 4: Align file picker and visible server errors in Blade

**Files:**
- Modify: `resources/views/projects/create.blade.php`
- Modify: `resources/views/projects/edit.blade.php`

- [ ] **Step 1: Replace broad accept attributes**

Main image and gallery inputs in both files:

```blade
accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
```

- [ ] **Step 2: Update helper copy**

Create gallery copy:

```text
You can select multiple files at once. Max 10 photos, up to 4MB each, max resolution 4096×4096px. Format: JPG, PNG, WEBP.
```

Edit gallery copy:

```text
Upload additional photos to the gallery. Max 10 photos total, up to 4MB each, max resolution 4096×4096px. Format: JPG, PNG, WEBP.
```

- [ ] **Step 3: Add client-side error container**

Below preview area in both files:

```blade
<div id="gallery-upload-errors" class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-3 hidden"></div>
```

- [ ] **Step 4: Render parent gallery server error first**

Inside validation error block, render:

```blade
@if($errors->has('gallery_images'))
    <p class="text-sm flex items-center gap-1"><i class="size-4" data-lucide="alert-circle"></i> {{ $errors->first('gallery_images') }}</p>
@endif
```

before child wildcard errors.

---

## Task 5: Add drag & drop client preflight

**Files:**
- Modify: `resources/views/projects/create.blade.php`
- Modify: `resources/views/projects/edit.blade.php`

- [ ] **Step 1: Add validation constants in JS**

In both scripts near `dropzone/fileInput/previewArea`:

```js
const errorBox = document.getElementById('gallery-upload-errors');
const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
const maxFileSize = 4 * 1024 * 1024;
const maxFiles = 10;
```

For edit, use remaining slots:

```js
const existingGalleryCount = {{ $project->images->count() }};
const maxFiles = Math.max(0, 10 - existingGalleryCount);
```

- [ ] **Step 2: Add message helpers**

```js
const showGalleryErrors = (messages) => {
    if (!errorBox) return;
    errorBox.innerHTML = '';
    messages.forEach((message) => {
        const p = document.createElement('p');
        p.className = 'text-sm flex items-center gap-1';
        p.textContent = message;
        errorBox.appendChild(p);
    });
    errorBox.classList.remove('hidden');
};

const clearGalleryErrors = () => {
    if (!errorBox) return;
    errorBox.innerHTML = '';
    errorBox.classList.add('hidden');
};
```

- [ ] **Step 3: Add file validation helper**

```js
const validateFiles = (files) => {
    const errors = [];
    const selectedFiles = Array.from(files);

    if (selectedFiles.length > maxFiles) {
        errors.push(maxFiles > 0
            ? `Maksimal ${maxFiles} foto lagi bisa ditambahkan.`
            : 'Gallery sudah mencapai batas maksimal 10 foto.');
    }

    selectedFiles.forEach((file) => {
        const extension = file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(file.type) || !allowedExtensions.includes(extension)) {
            errors.push(`${file.name}: format harus JPG, PNG, atau WEBP.`);
        }

        if (file.size > maxFileSize) {
            errors.push(`${file.name}: ukuran maksimal 4 MB.`);
        }
    });

    return errors;
};
```

- [ ] **Step 4: Gate preview with validation**

At start of `handleFiles(files)`:

```js
const errors = validateFiles(files);
if (errors.length > 0) {
    showGalleryErrors(errors);
    previewArea.innerHTML = '';
    previewArea.classList.add('hidden');
    fileInput.value = '';
    return;
}

clearGalleryErrors();
```

- [ ] **Step 5: Avoid `innerHTML` for user filename**

Current template injects `${file.name}` into `innerHTML`. Keep image markup if desired, but set filename via `textContent` after element creation.

Minimal safe pattern:

```js
const label = document.createElement('span');
label.className = 'text-white text-xs font-semibold px-2 text-center line-clamp-2';
label.textContent = file.name;
```

Then append label instead of interpolating filename into template string.

- [ ] **Step 6: Ensure drag/drop assignment only happens after validation**

In `handleDrop(e)`, validate before assigning `fileInput.files`:

```js
const errors = validateFiles(files);
if (errors.length > 0) {
    showGalleryErrors(errors);
    fileInput.value = '';
    previewArea.innerHTML = '';
    previewArea.classList.add('hidden');
    return;
}
```

Then build `DataTransfer`.

---

## Task 6: Improve 413 behavior for project upload forms

**Files:**
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Redirect project uploads back with error**

Inside existing `PostTooLargeException` render callback, before `response()->view('errors.413', [], 413)`:

```php
if ($request->is('manage/projects*')) {
    return back()->withErrors([
        'gallery_images' => 'Total ukuran upload terlalu besar. Setiap foto galeri maksimal 4 MB, foto utama maksimal 2 MB, dan maksimal 10 foto galeri.',
    ]);
}
```

- [ ] **Step 2: Keep JSON/API response unchanged**

No change to:

```php
if ($request->expectsJson()) {
    return response()->json(['message' => 'Total file size is too large.'], 413);
}
```

- [ ] **Step 3: Keep generic 413 page for non-project routes**

No change to final fallback:

```php
return response()->view('errors.413', [], 413);
```

---

## Task 7: Verification

**Files:**
- No source edits unless checks fail.

- [ ] **Step 1: Run targeted gallery tests**

```bash
php artisan test tests/Feature/ProjectGalleryTest.php
```

Expected:

```text
PASS
```

- [ ] **Step 2: Run full test suite**

```bash
composer test
```

Expected:

```text
PASS
```

- [ ] **Step 3: Build frontend**

```bash
npm run build
```

Expected:

```text
✓ built
```

- [ ] **Step 4: Runtime manual check**

Drive admin form in browser/runtime:

1. Drop `.jpg` >4MB → visible client error.
2. Drop `.heic` / `.gif` / `.svg` → visible client error.
3. Drop PDF renamed `.jpg` → server rejects with clear message if client misses it.
4. Drop >10 files on create → visible client error.
5. Edit project with 9 existing gallery photos, drop 2 new photos → visible client error.
6. Upload valid JPG/PNG/WEBP under 4MB and 4096×4096 → preview + submit succeed.

- [ ] **Step 5: Inspect changed files**

```bash
git diff --stat
```

Expected files only:

```text
app/Http/Requests/StoreProjectRequest.php
app/Http/Requests/UpdateProjectRequest.php
app/Services/ProjectService.php
bootstrap/app.php
resources/views/projects/create.blade.php
resources/views/projects/edit.blade.php
tests/Feature/ProjectGalleryTest.php
```

---

## Deliberate Skips

- New upload middleware skipped. Existing `PostTooLargeException` handling covers oversized request behavior.
- New package skipped. Laravel validation + native JS enough.
- Media library abstraction skipped. Current domain only needs project image upload.
- Thumbnail generation skipped. Separate performance task.
- Chunked upload skipped. Needed only if business wants large original files.
- Image compression skipped. User can compress before upload; server resize/compression can be later feature.
- HEIC/AVIF support skipped. Current accepted formats stay JPG/PNG/WEBP.

---

## Acceptance Criteria

- User sees clear message before submit for invalid drag/drop files.
- Server returns clear validation message when invalid file reaches backend.
- Files with unsafe original extension do not get stored using that extension.
- Non-image file disguised as `.jpg` is rejected.
- Gallery limit remains max 10 total per project.
- Existing successful gallery upload flow still works.
- Public gallery, admin detail gallery, scoped delete, force delete cleanup remain green.
