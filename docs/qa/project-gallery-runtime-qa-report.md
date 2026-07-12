# QA Runtime Report — Modul Project Image Gallery

Tanggal: 2026-07-12  
Verdict: **PASS**  
Scope: create upload, update append, batas total max 10, public carousel, smooth transition HTML/JS, admin detail gallery, scoped delete route, force delete cleanup, dan SEO public detail.

## Ringkasan

Modul gallery lulus QA runtime untuk fungsi inti:

- Create project dengan gallery images: **PASS**
- Update project menambah gallery image: **PASS**
- Batas total 10 gallery images per project: **PASS**
- Public case study carousel: **PASS**
- Smooth transition guard HTML/JS: **PASS**
- Admin detail read-only gallery: **PASS**
- Scoped delete route / anti-IDOR: **PASS**
- Force delete cleanup DB: **PASS**
- SEO public project detail tidak terdampak: **PASS**

Tidak ada bug blocking ditemukan.

## Metode QA

Runtime app lokal:

- Host: `127.0.0.1:8012`
- Database: SQLite temp
- User: QA admin verified
- Akses: HTTP asli memakai cookie/session/CSRF
- Test suite: tidak dipakai untuk verdict runtime

QA dilakukan lewat surface aplikasi, bukan import function atau unit test.

## Hasil Observasi

### 1. Login admin dan akses form create

Status: **PASS**

Observasi:

- Login berhasil.
- `/manage/projects/create` bisa diakses setelah user verified.

Catatan runtime:

- Awal sempat redirect ke `/verify-email`.
- Penyebab: user seed pertama belum verified karena `email_verified_at` tidak mass-assign.
- Setelah update DB temp, flow admin normal.

Dampak:

- Bukan bug gallery.
- Production perlu pastikan akun admin punya `email_verified_at`.

---

### 2. Create project dengan gallery images

Status: **PASS**

Action:

- Create project via HTTP form.
- Upload 1 main image.
- Upload 2 gallery images.

Response:

```text
HTTP/1.1 302 Found
Location: http://127.0.0.1:8012/manage/projects
```

DB result:

```text
project_id=1 images=2
1:projects/gallery/Q3TJrnbcY4WVzqm3mhaAXQVxec1LjCQthOnCPxjH.png
2:projects/gallery/PVvZMz1aXGFcaMSNVFshQLDgcOMlvL70H7DYt8tU.png
```

Kesimpulan:

- Gallery tersimpan.
- Record `project_images` dibuat.
- Path storage benar: `projects/gallery/...`.

---

### 3. Public case study carousel

Status: **PASS**

URL:

```text
/case-study/runtime-gallery-project
```

Observed:

```text
200 page PASS
carousel PASS
gallery path PASS
thumb PASS
transition guard PASS
lazy PASS
seo canonical PASS
```

Evidence HTML/JS:

- `id="project-carousel"` ada.
- `storage/projects/gallery/` ada.
- `carousel-thumb` ada.
- `isTransitioning` ada.
- `loading="lazy"` ada.
- `rel="canonical"` tetap ada.

Kesimpulan:

- Public gallery tampil.
- Carousel HTML render.
- Guard smooth transition render.
- SEO metadata tidak hilang.

---

### 4. Admin detail read-only gallery

Status: **PASS**

URL:

```text
/manage/projects/1
```

Observed:

```text
project details PASS
gallery card PASS
manage gallery PASS
gallery image PASS
```

Kesimpulan:

- Admin bisa melihat gallery di halaman detail project.
- Halaman detail punya akses lanjut untuk manage gallery.

---

### 5. Update project tambah gallery image

Status: **PASS**

Action:

- Edit project via HTTP form.
- Upload 1 gallery image tambahan.

Observed:

```text
HTTP/1.1 302 Found
Location: http://127.0.0.1:8012/manage/projects
3
```

Kesimpulan:

- Gallery bertambah dari 2 menjadi 3.
- Existing gallery tidak terhapus saat update.

---

### 6. Batas total max 10 gallery images

Status: **PASS**

Setup:

- Isi project sampai total 10 gallery images.
- Coba upload 1 lagi via edit form.

Observed:

```text
HTTP/1.1 302 Found
Location: http://127.0.0.1:8012/manage/projects/1/edit
limit_message PASS
10
```

Kesimpulan:

- Upload ke-11 ditolak validasi.
- Jumlah gallery tetap 10.
- Batas total per project bekerja, bukan cuma batas per request.

---

### 7. Delete gallery image scoped ke project

Status: **PASS**

Action:

- Delete image via route:

```text
/manage/projects/1/gallery/{imageId}
```

Observed:

```text
HTTP/1.1 302 Found
Location: http://127.0.0.1:8012/manage/projects/1/edit
9
```

Kesimpulan:

- Image terhapus.
- Jumlah turun dari 10 menjadi 9.
- Redirect kembali ke edit project.

---

### 8. Probe IDOR / wrong-project delete

Status: **PASS**

Action:

- Buat project lain.
- Coba hapus image milik project 1 lewat route project 2:

```text
/manage/projects/{project2}/gallery/{imageFromProject1}
```

Observed:

```text
404
```

Kesimpulan:

- Scoped route bekerja.
- Image tidak bisa dihapus lewat project salah.
- Risiko IDOR untuk delete gallery tertutup pada route ini.

---

### 9. Soft delete + force delete cleanup

Status: **PASS**

Action:

- Soft delete project via HTTP.
- Force delete project via HTTP.

Observed:

```text
HTTP/1.1 302 Found
Location: http://127.0.0.1:8012/manage/projects

HTTP/1.1 302 Found
Location: http://127.0.0.1:8012/manage/projects/archive

project=missing images=0
```

Probe public route setelah force delete:

```text
404
```

Kesimpulan:

- Project hilang permanen.
- Gallery DB records hilang.
- Public case study tidak bisa diakses setelah force delete.

Catatan batas verifikasi:

- Runtime evidence memastikan DB cleanup.
- Storage file cleanup sudah tercakup automated test `tests/Feature/ProjectGalleryTest.php`.

---

### 10. SEO public detail tidak terdampak

Status: **PASS**

Claim:

Gallery hardening tidak merusak SEO public project detail. `title`, `meta description`, `canonical`, `og:image`, dan Twitter Card tetap render. Gallery image masuk carousel tanpa mengganti `og:image`.

Evidence:

```html
<title>SEO Gallery Verification Title</title>
<meta name="description" content="SEO gallery verification description.">
<link rel="canonical" href="http://127.0.0.1:8011/case-study/seo-gallery-verification">
<meta property="og:type" content="article">
<meta property="og:title" content="SEO Gallery Verification Title">
```

`og:image` tetap memakai main project image:

```html
<meta property="og:image" content="http://127.0.0.1:8011/storage/projects/main-verification.jpg">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="SEO Gallery Verification Title">
```

Gallery image tetap masuk carousel data:

```html
data-images="[
  &quot;http:\/\/127.0.0.1:8011\/storage\/projects\/main-verification.jpg&quot;,
  &quot;http:\/\/127.0.0.1:8011\/storage\/projects\/gallery\/verify-one.jpg&quot;
]"
```

Kesimpulan:

- SEO public detail tidak terdampak.
- `og:image` stabil memakai main image.
- Gallery image tidak mengganggu canonical/meta/OG/Twitter.

## Temuan

### ⚠️ Finding 1 — Runtime PHP lokal sempat salah versi

Observed:

```text
Composer dependencies require PHP >= 8.3.0. You are running 7.2.34.
```

Penyebab:

- Shell awal memakai PHP 7.2.
- Setelah runtime switch ke PHP 8.5, QA berjalan normal.

Rekomendasi:

- Pastikan terminal default memakai PHP 8.3+.
- Jika sering terjadi, set `PATH` permanen ke PHP Homebrew/current.

---

### ⚠️ Finding 2 — Smooth transition belum diverifikasi lewat browser pixel/video

Yang sudah diverifikasi:

- Public carousel render.
- `isTransitioning` ada.
- `transition-all` ada.
- Thumbnail lazy/async render.

Yang belum diverifikasi:

- Pixel/browser screenshot.
- Video fade transition.
- Rapid-click behavior secara visual.

Rekomendasi manual check:

1. Buka public case study project dengan lebih dari 1 gallery image.
2. Klik thumbnail/next cepat beberapa kali.
3. Pastikan tidak flicker.
4. Pastikan fade terasa halus.

## Kesimpulan Akhir

Modul project image gallery **lulus QA runtime** untuk scope yang diminta.

Tidak ada bug blocking pada:

- upload gallery create
- upload gallery update
- batas total max 10
- public carousel
- admin detail read-only gallery
- scoped delete route
- force delete cleanup DB
- SEO public detail

Next paling aman:

1. Push commit ke `main`.
2. Deploy.
3. Setelah deploy, cek visual carousel di browser production.
