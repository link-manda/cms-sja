# Plan: Implementasi Fitur Kalkulasi Harga Bangunan

Dokumen ini adalah cetak biru langkah demi langkah untuk membangun fitur **Kalkulasi Harga Bangunan** sesuai permintaan dari Pak Monyo (berdasarkan `note_request_fitur_kalkulasi.txt`).

## 1. Tahap Database & Model
Kita akan membuat struktur tabel baru agar terpisah dari modul *Projects*.

**Tabel `calculator_options`**
- `id` (Primary Key)
- `name` (String) - Label untuk opsi, misal "Paket Standar" atau "Bangunan Tipe 36".
- `price_range` (String) - Rentang harga untuk ditampilkan di *dropdown*.
- `description` (Text) - Teks penjelasan detail saat opsi dipilih.
- `timestamps`, `softDeletes`

> **Keputusan SEO (single-page):** Kolom `meta_title`/`meta_description` per-opsi **dihapus** dari rencana awal. Halaman kalkulator bersifat *single-page tanpa reload* (Vanilla JS mengganti DOM), sehingga meta tag per-opsi tidak pernah dibaca crawler Google — server hanya me-render satu `<title>`/`<meta>` untuk `/pricing-calculator`. Menyimpan meta per-opsi = kolom mubazir (YAGNI). SEO cukup di level halaman (lihat Tahap 4). Jika suatu saat klien butuh tiap opsi terindeks terpisah, barulah pindah ke arsitektur per-URL `/pricing-calculator/{slug}` (butuh kolom `slug`, dan konsekuensinya bukan lagi "tanpa reload").

**Tabel `calculator_images`**
- `id` (Primary Key)
- `calculator_option_id` (Foreign Key) - Set `onDelete('cascade')` agar record ikut terhapus saat opsi di-*force delete*.
- `image_path` (String) - Lokasi file gambar di sistem.
- `type` (String, Nullable) - Klasifikasi gambar: `2d`, `3d`, atau `proses`. Wajib terisi agar output publik bisa mengelompokkan galeri sesuai permintaan note (Desain 2D / 3D / Foto Proses).
- `timestamps`

**Model**
- `CalculatorOption` (Relasi: `hasMany(CalculatorImage::class)`)
- `CalculatorImage` (Relasi: `belongsTo(CalculatorOption::class)`)

## 2. Tahap Back-End (Admin Dashboard)
Modul untuk memungkinkan Admin mengatur (CRUD) opsi kalkulator secara dinamis.

**Controller & Service**
- Membuat `CalculatorOptionController` untuk rute web admin.
- Membuat `CalculatorOptionService` untuk menangani logika bisnis. Ikuti pola `ProjectService` yang sudah ada (`createProject`/`updateProject`/`storeGalleryImages`/`forceDeleteProject`) — reuse struktur transaksi + rollback file on-failure yang sama.

**Penanganan `type` gambar (2D / 3D / Proses)**
`ProjectService::storeGalleryImages` existing hanya menyimpan `image_path`, belum ada `type`. `CalculatorOptionService` perlu menyimpan `type` per gambar. Dua opsi UI:
- **Opsi A (direkomendasikan):** 3 zona upload terpisah di form — "Desain 2D", "Desain 3D", "Foto Proses" — masing-masing kirim array `images_2d[]`, `images_3d[]`, `images_proses[]`. Service menandai `type` per zona. Paling sederhana, tak perlu JS mapping per-file.
- **Opsi B:** 1 zona drag-n-drop, tiap file punya dropdown `type` sendiri. Lebih fleksibel tapi butuh JS untuk memasangkan file↔type saat submit.
- Total 10 gambar adalah batas gabungan seluruh zona; validasi `max:10` dihitung atas total.

**Validasi Unggahan (Upload Constraints)**
Aturan validasi akan diterapkan pada proses *Store* dan *Update* (contoh untuk Opsi A, jumlahkan lintas zona untuk batas total 10):
```php
'images_2d'   => 'nullable|array',
'images_3d'   => 'nullable|array',
'images_proses' => 'nullable|array',
'images_2d.*'   => 'image|mimes:jpeg,png,jpg,webp|max:4096', // Maksimal 4 MB per gambar
'images_3d.*'   => 'image|mimes:jpeg,png,jpg,webp|max:4096',
'images_proses.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
// Batas total 10 divalidasi via closure/after-hook: count(2d)+count(3d)+count(proses) <= 10
```
> Jika memilih Opsi B (satu zona), tetap pakai `'images' => 'nullable|array|max:10'` + `'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096'` seperti rencana awal, ditambah array `types.*` paralel.

**Cascade Delete (kebersihan file & record)**
Saat opsi di-*force delete*, tiru `ProjectService::forceDeleteProject`: hapus semua file `calculator_images` dari disk, lalu hapus record. FK `onDelete('cascade')` membersihkan record DB, tapi **file di disk tetap harus dihapus manual** karena cascade DB tidak menyentuh storage.

**View Admin Dashboard**
- `resources/views/calculator/index.blade.php` - Tabel daftar opsi.
- `resources/views/calculator/create.blade.php` - Form input dengan *Drag n Drop* untuk 10 gambar (3 zona bila pakai Opsi A).
- `resources/views/calculator/edit.blade.php` - Form edit dan pengelolaan galeri spesifik kalkulator.

**Security (Routing)**
Menempatkan *resource route* di `routes/web.php` tepat di dalam grup *middleware* `['auth', 'verified']` (ikuti pola `manage/projects` existing, dengan `throttle`):
```php
Route::resource('manage/calculator', CalculatorOptionController::class)
    ->middleware(['throttle:30,1']);
```

## 3. Tahap Front-End (Halaman Publik)
Halaman tempat *user* (pengunjung web) berinteraksi dengan kalkulator harga.

**Controller & Routing**
- Membuat `PublicCalculatorController@index`.
- Mendefinisikan *route* publik `/pricing-calculator`.

> **⚠️ KRITIS — Urutan Route:** `routes/web.php` memiliki grup *catch-all* di paling bawah:
> ```php
> Route::get('{any}', [RoutingController::class, 'root'])->where('any', '[^.]+');
> ```
> Segmen tunggal `/pricing-calculator` akan **tertangkap `{any}`** (yang butuh `auth`) → pengunjung publik ter-redirect ke login. Route publik kalkulator **WAJIB didefinisikan di atas grup catch-all**, sejajar dengan `public.projects.*` yang sudah ada (Laravel first-match-wins — itulah sebabnya route projects publik existing bisa jalan):
> ```php
> Route::get('/pricing-calculator', [PublicCalculatorController::class, 'index'])->name('public.calculator.index');
> ```

**UI Interaktif (Tanpa Reload)**
- File: `resources/views/public/calculator/index.blade.php`.
- **Data Bootstrap**: Controller mengirim seluruh opsi + gambar (dikelompokkan per `type`) ke view; JS membacanya dari elemen data (mis. `@json` di `data-*` atau `<script>` inline). Tak ada AJAX — semua data sudah ada saat load, sesuai "tanpa reload".
- **Dropdown Input**: Menampilkan seluruh data `price_range`.
- **Vanilla JS Logic**: Saat pengunjung memilih opsi *dropdown*, JS mendeteksi perubahan dan memperbarui DOM secara instan dari data yang sudah di-bootstrap.
- **Output Visual**: Merender array gambar (Desain 2D, 3D, Proses Pembangunan — dikelompokkan via kolom `type`) menjadi galeri *slider/carousel* yang rapi.
- **Output Teks**: Memperbarui paragraf deskripsi sesuai opsi yang dipilih.

## 4. Keamanan Lanjutan & Optimasi SEO
- **Isolasi Halaman Utama**: Sesuai dengan *"Catatan Penting"*, pengaturan SEO dan UI pada `welcome.blade.php` (Landing Page) sama sekali **tidak akan diubah/ditimpa**.
- **SEO Spesifik Kalkulator**: Halaman `/pricing-calculator` akan memiliki struktur tag `<title>` dan `<meta name="description">` nya sendiri agar terindeks sempurna oleh Google sebagai "Kalkulator Harga Bangunan Sistem Jaya Abadi". Meta ini **di-render server-side satu kali per halaman** (bukan per-opsi) — konsisten dengan arsitektur single-page pada Tahap 1. SEO per-opsi hanya relevan jika beralih ke arsitektur per-URL (di luar scope note "tanpa reload").
