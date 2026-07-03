# Minimal SEO Patch Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bring public SEO basics to normal business-site standard with smallest safe diff.

**Architecture:** Keep SEO in Blade and static files. Reuse one public SEO partial for canonical/social tags, keep admin `title-meta` unchanged, and update static sitemap with current public routes. No package, no dynamic sitemap route yet.

**Tech Stack:** Laravel 13, Blade, PHPUnit feature tests, static `public/robots.txt`, static `public/sitemap.xml`.

---

## File Map

- Modify: `public/robots.txt` — correct crawl blocks and sitemap URL.
- Modify: `public/sitemap.xml` — correct domain and add public case-study URL placeholder pattern via seeded test fixture later if dynamic not chosen.
- Create: `resources/views/partials/public-seo.blade.php` — reusable canonical/Open Graph/Twitter meta tags for public pages.
- Modify: `resources/views/welcome.blade.php` — replace duplicated base meta with public SEO partial and add Organization JSON-LD.
- Modify: `resources/views/public/projects/index.blade.php` — use public SEO partial.
- Modify: `resources/views/public/projects/show.blade.php` — use public SEO partial with project-specific image/title/description.
- Create: `tests/Feature/PublicSeoTest.php` — small runtime checks for public SEO tags.

## Constants

Use this production base URL everywhere this patch touches:

```text
https://www.sistemjayaabadi.biz.id
```

---

### Task 1: Fix static crawl files

**Files:**
- Modify: `public/robots.txt`
- Modify: `public/sitemap.xml`

- [ ] **Step 1: Update `public/robots.txt`**

Replace file content with:

```txt
User-agent: *
Disallow: /admin/
Disallow: /manage/
Disallow: /dashboard
Disallow: /categories
Disallow: /settings
Disallow: /profile
Disallow: /login
Disallow: /register
Disallow: /forgot-password
Disallow: /reset-password

Sitemap: https://www.sistemjayaabadi.biz.id/sitemap.xml
```

- [ ] **Step 2: Update `public/sitemap.xml`**

Replace current `.com` URLs with `.biz.id` and add known public case-study URLs only if existing production slugs are known. If slugs are not known during implementation, keep only stable public index pages:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://www.sistemjayaabadi.biz.id/</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>https://www.sistemjayaabadi.biz.id/projects</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
</urlset>
```

Do not invent project slugs. Wrong URLs are worse than missing URLs.

- [ ] **Step 3: Run syntax sanity check**

Run:

```bash
php -r 'simplexml_load_file("public/sitemap.xml") !== false || exit(1); echo "sitemap ok\n";'
```

Expected:

```text
sitemap ok
```

- [ ] **Step 4: Commit**

```bash
git add public/robots.txt public/sitemap.xml
git commit -m "fix(seo): correct robots and sitemap domain"
```

---

### Task 2: Add reusable public SEO partial

**Files:**
- Create: `resources/views/partials/public-seo.blade.php`

- [ ] **Step 1: Create partial**

Create `resources/views/partials/public-seo.blade.php`:

```blade
@php
    $seoTitle = $title ?? setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor');
    $seoDescription = $description ?? setting('site_description', 'Professional contractors for premium, on-time construction projects.');
    $seoUrl = $url ?? url()->current();
    $seoImage = $image ?? asset('assets/logo.png');
@endphp

<meta name="description" content="{{ $seoDescription }}">
<link rel="canonical" href="{{ $seoUrl }}">
<meta property="og:type" content="{{ $type ?? 'website' }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/partials/public-seo.blade.php
git commit -m "feat(seo): add public meta partial"
```

---

### Task 3: Use SEO partial on public pages

**Files:**
- Modify: `resources/views/welcome.blade.php:4-10`
- Modify: `resources/views/public/projects/index.blade.php:4-10`
- Modify: `resources/views/public/projects/show.blade.php:3-8`

- [ ] **Step 1: Update homepage head meta**

In `resources/views/welcome.blade.php`, replace current description/title/favicon block:

```blade
<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta name="description"
    content="{{ setting('site_description', 'Professional contractors for premium, on-time construction projects.') }}">
<title>{{ setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor') }}</title>
<link rel="icon" type="image/png" href="/assets/logo.png" />
```

with:

```blade
<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<title>{{ setting('site_title', 'PT Sistem Jaya Abadi - Professional Contractor') }}</title>
@include('partials.public-seo')
<link rel="icon" type="image/png" href="/assets/logo.png" />
```

- [ ] **Step 2: Add Organization JSON-LD on homepage**

Immediately after favicon line in `resources/views/welcome.blade.php`, add:

```blade
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "PT Sistem Jaya Abadi",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('assets/logo.png') }}",
    "description": "{{ setting('site_description', 'Professional contractors for premium, on-time construction projects.') }}",
    "email": "{{ setting('contact_email', '') }}",
    "address": "{{ setting('company_address', '') }}"
}
</script>
```

- [ ] **Step 3: Update projects index head meta**

In `resources/views/public/projects/index.blade.php`, replace current description/title/favicon block:

```blade
<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta name="description"
    content="Explore the portfolio of PT Sistem Jaya Abadi. View our completed and ongoing construction projects across Indonesia.">
<title>Our Projects - PT Sistem Jaya Abadi</title>
<link rel="icon" type="image/png" href="/assets/logo.png" />
```

with:

```blade
<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<title>Our Projects - PT Sistem Jaya Abadi</title>
@include('partials.public-seo', [
    'title' => 'Our Projects - PT Sistem Jaya Abadi',
    'description' => 'Explore the portfolio of PT Sistem Jaya Abadi. View our completed and ongoing construction projects across Indonesia.',
    'url' => route('public.projects.index'),
])
<link rel="icon" type="image/png" href="/assets/logo.png" />
```

- [ ] **Step 4: Update project detail head meta**

In `resources/views/public/projects/show.blade.php`, replace current description/title/favicon block:

```blade
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="description" content="{{ $project->meta_description ?? Str::limit($project->description, 150) }}">
<title>{{ $project->meta_title ?? $project->title . ' - Case Study | PT Sistem Jaya Abadi' }}</title>
<link rel="icon" type="image/png" href="/assets/logo.png" />
```

with:

```blade
@php
    $seoTitle = $project->meta_title ?? $project->title . ' - Case Study | PT Sistem Jaya Abadi';
    $seoDescription = $project->meta_description ?? Str::limit($project->description, 150);
    $seoImage = str_starts_with($project->image, 'http')
        ? $project->image
        : (file_exists(public_path('assets/' . $project->image))
            ? asset('assets/' . $project->image)
            : asset('storage/projects/' . $project->image));
@endphp
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>{{ $seoTitle }}</title>
@include('partials.public-seo', [
    'title' => $seoTitle,
    'description' => $seoDescription,
    'url' => route('public.projects.show', $project->slug),
    'image' => $seoImage,
    'type' => 'article',
])
<link rel="icon" type="image/png" href="/assets/logo.png" />
```

- [ ] **Step 5: Run Blade smoke check through feature test command**

Run:

```bash
php artisan test --filter=ExampleTest
```

Expected:

```text
PASS  Tests\Feature\ExampleTest
```

- [ ] **Step 6: Commit**

```bash
git add resources/views/welcome.blade.php resources/views/public/projects/index.blade.php resources/views/public/projects/show.blade.php
git commit -m "feat(seo): add public canonical and social tags"
```

---

### Task 4: Add runtime SEO tests

**Files:**
- Create: `tests/Feature/PublicSeoTest.php`

- [ ] **Step 1: Create feature test**

Create `tests/Feature/PublicSeoTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_public_seo_tags(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="http://localhost">', false);
        $response->assertSee('<meta property="og:title" content="PT Sistem Jaya Abadi - Professional Contractor">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@type": "Organization"', false);
    }

    public function test_projects_index_has_public_seo_tags(): void
    {
        $response = $this->get('/projects');

        $response->assertOk();
        $response->assertSee('<link rel="canonical" href="http://localhost/projects">', false);
        $response->assertSee('<meta property="og:title" content="Our Projects - PT Sistem Jaya Abadi">', false);
        $response->assertSee('<meta property="og:url" content="http://localhost/projects">', false);
    }

    public function test_project_detail_uses_project_seo_tags(): void
    {
        $project = Project::factory()->create([
            'title' => 'Factory Warehouse Build',
            'slug' => 'factory-warehouse-build',
            'description' => 'Fallback project description for SEO metadata.',
            'image' => 'factory.jpg',
            'meta_title' => 'Factory Warehouse SEO Title',
            'meta_description' => 'Factory warehouse SEO description.',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertOk();
        $response->assertSee('<title>Factory Warehouse SEO Title</title>', false);
        $response->assertSee('<meta name="description" content="Factory warehouse SEO description.">', false);
        $response->assertSee('<link rel="canonical" href="http://localhost/case-study/factory-warehouse-build">', false);
        $response->assertSee('<meta property="og:image" content="http://localhost/storage/projects/factory.jpg">', false);
        $response->assertSee('<meta property="og:type" content="article">', false);
    }
}
```

- [ ] **Step 2: Run new test**

Run:

```bash
php artisan test tests/Feature/PublicSeoTest.php
```

Expected:

```text
PASS  Tests\Feature\PublicSeoTest
```

- [ ] **Step 3: Run all tests**

Run:

```bash
composer test
```

Expected:

```text
Tests: ... passed
```

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/PublicSeoTest.php
git commit -m "test(seo): cover public metadata"
```

---

### Task 5: Final verification before deploy

**Files:**
- No source edits unless verification fails.

- [ ] **Step 1: Verify robots and sitemap content locally**

Run:

```bash
grep -n "sistemjayaabadi.com\|sistemjayaabadi.biz.id\|Disallow" public/robots.txt public/sitemap.xml
```

Expected:

```text
public/robots.txt:2:Disallow: /admin/
public/robots.txt:3:Disallow: /manage/
public/robots.txt:4:Disallow: /dashboard
public/robots.txt:5:Disallow: /categories
public/robots.txt:6:Disallow: /settings
public/robots.txt:7:Disallow: /profile
public/robots.txt:8:Disallow: /login
public/robots.txt:9:Disallow: /register
public/robots.txt:10:Disallow: /forgot-password
public/robots.txt:11:Disallow: /reset-password
public/robots.txt:13:Sitemap: https://www.sistemjayaabadi.biz.id/sitemap.xml
public/sitemap.xml:4:        <loc>https://www.sistemjayaabadi.biz.id/</loc>
public/sitemap.xml:9:        <loc>https://www.sistemjayaabadi.biz.id/projects</loc>
```

- [ ] **Step 2: Verify no old domain remains in public SEO files**

Run:

```bash
grep -R "sistemjayaabadi.com" -n public resources/views || true
```

Expected: no output.

- [ ] **Step 3: Build assets**

Run:

```bash
composer run build
```

Expected: Vite build succeeds.

- [ ] **Step 4: Final commit if verification caused edits**

Only run if Step 1-3 required fixes:

```bash
git add public resources/views tests
git commit -m "fix(seo): finalize public metadata"
```

---

## Deliberate skips

- Dynamic sitemap route skipped. Add when project changes are frequent enough that static sitemap becomes stale.
- Category SEO skipped. Add when category pages become public.
- SEO package skipped. Blade covers current need.
- LocalBusiness schema skipped. Organization is safer without verified business hours/geo/price range.

## Self-review

- Spec coverage: domain fix, robots hardening, public SEO partial, canonical/social tags, Organization JSON-LD, no package — covered.
- Placeholder scan: no `TBD`, no vague implementation-only steps.
- Type consistency: Blade variables use local `$seoTitle`, `$seoDescription`, `$seoImage`; tests match routes in `routes/web.php`.
