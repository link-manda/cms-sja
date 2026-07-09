# Flowchart Sistem SEO CMS SJA

Dokumen ini menjelaskan alur SEO publik di CMS SJA memakai Mermaid chart. Fokus: crawler control, sitemap, metadata publik, project SEO, JSON-LD, test, dan deploy.

## File utama

| Area | File | Fungsi |
|---|---|---|
| Robots | `public/robots.txt` | Mengizinkan crawler publik, memblokir admin/auth/private path, menunjuk sitemap. |
| Sitemap | `public/sitemap.xml` | Menyediakan URL publik stabil: `/` dan `/projects`. |
| SEO partial | `resources/views/partials/public-seo.blade.php` | Render meta description, canonical, Open Graph, Twitter Card. |
| Homepage | `resources/views/welcome.blade.php` | Render SEO default dari settings + `Organization` JSON-LD. |
| Projects index | `resources/views/public/projects/index.blade.php` | Render SEO portfolio/projects list. |
| Project detail | `resources/views/public/projects/show.blade.php` | Render SEO detail project pakai `meta_title`, `meta_description`, image. |
| Routes publik | `routes/web.php` | Menentukan halaman publik yang bisa dicrawl. |
| Project model | `app/Models/Project.php` | Menyimpan field `meta_title` dan `meta_description`. |
| Test SEO | `tests/Feature/PublicSeoTest.php` | Memastikan canonical, OG, Twitter Card, dan JSON-LD muncul. |

---

## 1. Arsitektur SEO publik

```mermaid
flowchart TD
    A["Pengunjung / Search Engine Bot"] --> B{"Request URL"}

    B -->|"/robots.txt"| C["public/robots.txt"]
    C --> D["Disallow admin, auth, profile, settings"]
    C --> E["Sitemap URL: /sitemap.xml"]

    B -->|"/sitemap.xml"| F["public/sitemap.xml"]
    F --> G["URL: /"]
    F --> H["URL: /projects"]

    B -->|"/"| I["routes/web.php homepage route"]
    I --> J["welcome.blade.php"]
    J --> K["partials.public-seo"]
    J --> L["Organization JSON-LD"]

    B -->|"/projects"| M["PublicProjectController@index"]
    M --> N["public/projects/index.blade.php"]
    N --> K

    B -->|"/case-study/{slug}"| O["PublicProjectController@show"]
    O --> P["Project lookup by slug"]
    P --> Q["public/projects/show.blade.php"]
    Q --> K
```

Ringkas: semua halaman publik pakai metadata HTML langsung dari Blade. Tidak ada package SEO tambahan.

---

## 2. Alur crawler dan indexing

```mermaid
flowchart TD
    A["Search Engine Bot"] --> B["Fetch /robots.txt"]
    B --> C{"Path masuk Disallow?"}

    C -->|"Ya: /dashboard, /manage, /login, dll"| D["Bot tidak crawl path private"]
    C -->|"Tidak: path publik"| E["Bot boleh crawl"]

    E --> F["Fetch /sitemap.xml"]
    F --> G["Baca URL prioritas"]
    G --> H["Crawl homepage /"]
    G --> I["Crawl projects index /projects"]

    I --> J["Temukan link internal project"]
    J --> K["Crawl /case-study/{slug}"]
    K --> L["Index halaman project jika kualitas konten cukup"]
```

Catatan penting: detail project belum masuk sitemap statis. Saat ini project detail ditemukan lewat internal link dari halaman projects/homepage.

---

## 3. Alur render metadata SEO reusable

```mermaid
flowchart TD
    A["Blade public page"] --> B{"Kirim parameter ke partial?"}

    B -->|"Tidak"| C["Gunakan default"]
    C --> C1["title dari setting('site_title')"]
    C --> C2["description dari setting('site_description')"]
    C --> C3["url dari url()->current()"]
    C --> C4["image dari asset('assets/logo.png')"]

    B -->|"Ya"| D["Gunakan parameter halaman"]
    D --> D1["title"]
    D --> D2["description"]
    D --> D3["url"]
    D --> D4["image"]
    D --> D5["type website/article"]

    C1 --> E["Render meta tags"]
    C2 --> E
    C3 --> E
    C4 --> E
    D1 --> E
    D2 --> E
    D3 --> E
    D4 --> E
    D5 --> E

    E --> F["meta name=description"]
    E --> G["link rel=canonical"]
    E --> H["Open Graph tags"]
    E --> I["Twitter Card tags"]
```

Partial utama: `resources/views/partials/public-seo.blade.php`.

---

## 4. Alur homepage SEO

```mermaid
flowchart TD
    A["GET /"] --> B["routes/web.php"]
    B --> C["Query latest ongoing projects"]
    C --> D["Render welcome.blade.php"]

    D --> E["Title dari setting site_title"]
    D --> F["Include partials.public-seo tanpa parameter"]
    F --> G["Default SEO dari settings helper"]

    D --> H["Build Organization schema array"]
    H --> I["json_encode JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE"]
    I --> J["Render script application/ld+json"]

    G --> K["HTML head siap untuk crawler/social crawler"]
    J --> K
```

Output homepage:
- `<title>` dari `site_title`
- meta description dari `site_description`
- canonical current URL
- Open Graph default
- Twitter Card default
- `Organization` JSON-LD

---

## 5. Alur projects index SEO

```mermaid
flowchart TD
    A["GET /projects"] --> B["PublicProjectController@index"]
    B --> C["Build Project query"]

    C --> D{"Filter request ada?"}
    D -->|"category"| E["where category_id"]
    D -->|"status"| F["where status"]
    D -->|"province"| G["where location like province"]
    D -->|"Tidak ada"| H["latest projects"]

    E --> I["paginate 9 with query string"]
    F --> I
    G --> I
    H --> I

    I --> J["Render public/projects/index.blade.php"]
    J --> K["Include public-seo dengan title projects"]
    K --> L["Canonical /projects"]
    K --> M["OG title Our Projects"]
    K --> N["Twitter Card summary_large_image"]
```

Catatan: canonical index selalu `route('public.projects.index')`, bukan URL dengan query filter. Ini mencegah variasi filter menjadi canonical duplicate.

---

## 6. Alur project detail SEO

```mermaid
flowchart TD
    A["GET /case-study/{slug}"] --> B["PublicProjectController@show"]
    B --> C["Project::where('slug', slug)->firstOrFail()"]

    C -->|"Tidak ditemukan"| D["404"]
    C -->|"Ditemukan"| E["Fetch related projects"]
    E --> F["Render public/projects/show.blade.php"]

    F --> G{"meta_title tersedia?"}
    G -->|"Ya"| H["Pakai project.meta_title"]
    G -->|"Tidak"| I["Fallback: project.title + Case Study"]

    F --> J{"meta_description tersedia?"}
    J -->|"Ya"| K["Pakai project.meta_description"]
    J -->|"Tidak"| L["Fallback: Str::limit(project.description, 150)"]

    F --> M{"image berupa URL penuh?"}
    M -->|"Ya"| N["Pakai image langsung"]
    M -->|"Tidak, file ada di public/assets"| O["asset('assets/' + image)"]
    M -->|"Tidak"| P["asset('storage/projects/' + image)"]

    H --> Q["Include public-seo"]
    I --> Q
    K --> Q
    L --> Q
    N --> Q
    O --> Q
    P --> Q

    Q --> R["canonical route public.projects.show"]
    Q --> S["og:type article"]
    Q --> T["og:image project image"]
```

Project detail adalah halaman SEO paling penting karena berisi case study dan keyword spesifik project.

---

## 7. Alur data SEO project dari admin ke publik

```mermaid
flowchart TD
    A["Admin login"] --> B["Manage Projects"]
    B --> C["Create / Edit Project"]

    C --> D["Input title, slug, description, image"]
    C --> E["Input meta_title optional"]
    C --> F["Input meta_description optional"]

    D --> G["StoreProjectRequest / UpdateProjectRequest"]
    E --> G
    F --> G

    G --> H["ProjectService create/update"]
    H --> I["projects table"]
    I --> J["Project model fillable"]

    J --> K["PublicProjectController@show"]
    K --> L["public/projects/show.blade.php"]
    L --> M{"Field SEO ada?"}

    M -->|"Ada"| N["Pakai meta_title dan meta_description"]
    M -->|"Kosong"| O["Fallback ke title dan description"]

    N --> P["Render SEO tags"]
    O --> P
```

Fitur SEO project sudah aman karena field optional. Kalau admin lupa isi meta, halaman tetap punya fallback.

---

## 8. Alur social sharing preview

```mermaid
flowchart TD
    A["User share URL ke WhatsApp / Facebook / LinkedIn"] --> B["Social crawler fetch URL"]
    B --> C["Laravel render Blade"]
    C --> D["partials.public-seo output OG tags"]

    D --> E["og:title"]
    D --> F["og:description"]
    D --> G["og:url"]
    D --> H["og:image"]

    E --> I["Preview title"]
    F --> J["Preview description"]
    G --> K["Preview canonical link"]
    H --> L["Preview image"]
```

Social preview sekarang tidak bergantung pada crawler menebak konten halaman.

---

## 9. Alur testing SEO

```mermaid
flowchart TD
    A["php artisan test tests/Feature/PublicSeoTest.php"] --> B["Homepage test"]
    A --> C["Projects index test"]
    A --> D["Project detail test"]

    B --> B1["GET /"]
    B1 --> B2["Assert canonical"]
    B1 --> B3["Assert og:title"]
    B1 --> B4["Assert twitter:card"]
    B1 --> B5["Assert Organization JSON-LD"]

    C --> C1["GET /projects"]
    C1 --> C2["Assert canonical /projects"]
    C1 --> C3["Assert og:title projects"]
    C1 --> C4["Assert og:url projects"]

    D --> D1["Create project factory"]
    D1 --> D2["GET /case-study/{slug}"]
    D2 --> D3["Assert project title"]
    D2 --> D4["Assert project meta description"]
    D2 --> D5["Assert canonical detail"]
    D2 --> D6["Assert og:image"]
    D2 --> D7["Assert og:type article"]
```

Test ini menjaga SEO dasar tidak hilang saat Blade diubah.

---

## 10. Alur deploy SEO ke hosting

```mermaid
flowchart TD
    A["Developer push ke main"] --> B["GitHub Actions main-deploy.yml"]
    B --> C["Checkout code"]
    B --> D["Composer install no-dev"]
    B --> E["npm install dan npm run build"]

    C --> F["Archive Laravel core"]
    D --> F
    E --> G["Archive public assets"]

    F --> H["Upload core.tar.gz ke server"]
    G --> I["Upload public.tar.gz ke server"]

    H --> J["Extract ke /home/sistemja/core-cms"]
    I --> K["Extract ke /home/sistemja/public_html"]

    K --> L["public/robots.txt live"]
    K --> M["public/sitemap.xml live"]
    J --> N["Blade SEO partial live"]
    J --> O["Views public live"]

    L --> P["Crawler membaca aturan baru"]
    M --> P
    N --> Q["Halaman publik render meta baru"]
    O --> Q
```

SEO berubah live setelah workflow deploy sukses.

---

## 11. Batasan sistem SEO sekarang

```mermaid
flowchart TD
    A["Sistem SEO sekarang"] --> B["Sudah ada"]
    A --> C["Belum ada"]

    B --> B1["robots.txt"]
    B --> B2["static sitemap.xml"]
    B --> B3["canonical"]
    B --> B4["meta description"]
    B --> B5["Open Graph"]
    B --> B6["Twitter Card"]
    B --> B7["Organization JSON-LD"]
    B --> B8["Project meta_title/meta_description"]
    B --> B9["Feature tests"]

    C --> C1["Dynamic sitemap dari database"]
    C --> C2["Category public SEO"]
    C --> C3["LocalBusiness schema detail"]
    C --> C4["Breadcrumb schema"]
    C --> C5["Per-project sitemap otomatis"]
```

Upgrade berikutnya paling masuk akal: dynamic sitemap jika jumlah project sering berubah.

---

## 12. Ringkasan flow end-to-end

```mermaid
flowchart LR
    A["Admin isi project SEO"] --> B["Project tersimpan"] --> C["Public detail page"]
    D["Settings site_title/site_description"] --> E["Homepage SEO default"]
    F["robots.txt"] --> G["Crawler rules"]
    H["sitemap.xml"] --> I["Crawler discovery"]
    C --> J["public-seo partial"]
    E --> J
    J --> K["Canonical + OG + Twitter"]
    E --> L["Organization JSON-LD"]
    G --> M["Indexing aman"]
    I --> M
    K --> M
    L --> M
```

Satu sumber metadata publik: `resources/views/partials/public-seo.blade.php`. Halaman publik hanya memberi parameter sesuai konteks.
