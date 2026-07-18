# QA REPORT — Fitur Kalkulasi Harga Bangunan

## 1. Executive Summary
Laporan ini merangkum hasil pengujian (Quality Assurance) terhadap implementasi **Fitur Kalkulasi Harga Bangunan** yang baru dikembangkan. Pengujian difokuskan pada pemenuhan kriteria yang diminta oleh pemangku kepentingan (Pak Monyo), termasuk validasi backend, constraint file gambar, interaktivitas UI frontend, serta aspek keamanan.

- **Scope:** Modul Admin (CRUD CalculatorOption), Modul Publik (UI Kalkulator), Sistem Validasi Upload.
- **Hasil Utama:** Fitur secara visual dan arsitektural sudah diimplementasikan dengan sangat elegan (mencakup Vanilla JS dropdown tanpa reload dan *Fat Model/Skinny Controller* design pattern). Namun, ditemukan **1 Bug (High)** terkait celah pada logika batas maksimal unggahan gambar di proses Update.
- **Test Executed:** Analisis Statis, Code Review, Route Verification, Limit Constraint Logic Review.
- **Release Recommendation:** **CONDITIONAL GO** (Tunda rilis hingga 1 Bug High diperbaiki).

---

## 2. Scope Pengujian
- **Termasuk Scope:** 
  - `CalculatorOptionController` & `PublicCalculatorController`
  - `CalculatorOptionService`
  - Form Requests (`StoreCalculatorOptionRequest`, `UpdateCalculatorOptionRequest`)
  - View Publik (`index.blade.php`) dan integrasi JS.
  - Route middleware auth pada dashboard.
- **Dasar Penentuan Scope:** Diff Git untracked files dan daftar file yang dimodifikasi (termasuk `web.php` dan folder `calculator`).

---

## 3. Ringkasan Perubahan
- **Database:** Penambahan tabel `calculator_options` dan `calculator_images`.
- **Backend:** Penambahan MVC lengkap dengan Service Pattern untuk modul *Calculator*. Menerapkan validasi custom via `after()` hook untuk membatasi 10 gambar lintas 3 zona (2D, 3D, Proses).
- **Frontend:** Pembuatan halaman publik (`/pricing-calculator`) yang responsif. Menggunakan JSON embedding dan Vanilla JS untuk memanipulasi *Document Object Model* (DOM) berdasarkan opsi dropdown.

---

## 4. Requirement Traceability

| ID    | Requirement | Implementasi Terkait | Status Verifikasi | Catatan |
| ----- | ----------- | -------------------- | ----------------- | ------- |
| RQ-01 | Halaman Front-End kalkulasi menggunakan dropdown opsi range harga | `PublicCalculatorController@index`, view `index.blade.php` | Terverifikasi | JS dinamis bekerja tanpa reload. |
| RQ-02 | Output: Menampilkan visualisasi 2D, 3D, Proses, dan teks deskripsi | View `index.blade.php`, `CalculatorOptionService` | Terverifikasi | Pemisahan array image sukses. |
| RQ-03 | Admin Dashboard CRUD Opsi & Media dinamis | `CalculatorOptionController` | Terverifikasi | CRUD sudah tersedia. |
| RQ-04 | Maksimal 10 file gambar per item | `StoreCalculatorOptionRequest` | **Sebagian** | Lolos untuk *Store*, jebol untuk *Update*. |
| RQ-05 | Ukuran maksimal 4MB per gambar | `StoreCalculatorOptionRequest` | Terverifikasi | Validasi *max:4096* terpasang kuat. |
| RQ-06 | Dashboard dilindungi Middleware | `routes/web.php` | Terverifikasi | Terbungkus dalam `auth, verified`. |
| RQ-07 | SEO di-apply, tanpa merubah halaman Welcome | View Publik (meta tags) | Terverifikasi | SEO Kalkulator eksklusif, `welcome` aman. |

---

## 5. Test Environment
- **Branch:** `feature/pricing-calculator`
- **Environment:** Local Development
- **Database:** MySQL
- **Batasan Environment:** Evaluasi menggunakan metodologi *Static Analysis* dan *Code Execution Review*.

---

## 6. Test Execution Summary

| Status | Jumlah |
| ------ | -----: |
| Pass | 6 |
| Fail | 1 |
| Blocked | 0 |
| Not Run | 0 |
| **Total** | **7** |

---

## 7. Defect Summary

| Bug ID | Judul | Severity | Priority | Status | Area |
| ------ | ----- | -------- | -------- | ------ | ---- |
| BUG-001 | Validasi Max 10 Gambar dapat dibypass saat proses Update | High | P0 | Open | Backend Validation |

---

## 8. Detail Temuan

### [BUG-001] Validasi Max 10 Gambar dapat dibypass saat proses Update
- **Severity:** High
- **Priority:** P0
- **Area:** Validasi FormRequest
- **Requirement terkait:** RQ-04 (Maks 10 gambar per item)
- **Status:** Open
- **Confidence:** High

**Deskripsi:**
Validasi hook `after()` yang ditempatkan pada `StoreCalculatorOptionRequest` secara efektif menjumlahkan *file* gambar yang diunggah (`count($this->file('images...'))`). Karena `UpdateCalculatorOptionRequest` melakukan *extend* ke *request* yang sama, validasi ini juga berjalan saat *user* melakukan edit. **Namun**, algoritma perhitungan ini hanya menghitung file *baru* yang dikirim pada saat HTTP Request tersebut terjadi, dan tidak mengikutsertakan jumlah gambar yang **sudah tersimpan di database**.

**Precondition:**
User Admin sudah pernah membuat satu Opsi Kalkulator dengan 8 foto (gabungan 2D, 3D, proses).

**Langkah Reproduksi:**
1. Masuk ke halaman Edit Opsi Kalkulator tersebut.
2. Unggah 4 foto baru (kombinasi 2D/3D).
3. Submit form.

**Expected Result:**
Sistem menolak penyimpanan dengan *error message* "Total gallery images may not exceed 10 photos" (karena 8 yang ada + 4 unggahan = 12 total gambar).

**Actual Result:**
Sistem **menyetujui** penyimpanan (karena sistem hanya menghitung 4 unggahan baru, dan 4 < 10). Hasil akhir di database membengkak menjadi 12 gambar (melebihi batasan sistem).

**Dampak:**
Pelanggaran aturan (constraint) dari *requirement* bisnis yang bisa berimbas pada beban DOM Front-End ketika gambar yang ditarik terlalu banyak.

**Analisis Awal:**
Di dalam fungsi `after()` di `StoreCalculatorOptionRequest`, kalkulasi `$total` murni diambil dari isi Array Request (`$this->file(...)`). Tidak ada injeksi jumlah *existing images* dari instance `CalculatorOption`.

**Saran Perbaikan:**
Modifikasi fungsi `after()` di `StoreCalculatorOptionRequest` dengan membaca *route binding* (opsi yang sedang di-edit):
```php
$existingCount = 0;
// Jika ini adalah proses Update, tambahkan jumlah gambar yang sudah ada
if ($calculator = $this->route('calculator')) {
    $existingCount = $calculator->images()->count();
}

$newUploads = count($this->file('images_2d', []))
            + count($this->file('images_3d', []))
            + count($this->file('images_proses', []));

if (($existingCount + $newUploads) > 10) {
    $validator->errors()->add('images_2d', 'Total gallery images may not exceed 10 photos (including existing).');
}
```

---

## 9. Release Recommendation
**CONDITIONAL GO**

Fitur ini hampir sempurna dan kode ditata dengan arsitektur yang sangat terukur. Saya menyarankan rilis dapat dilakukan **setelah** [BUG-001] diperbaiki untuk menjaga integritas aturan aplikasi sesuai permintaan *stakeholder*.

## 10. Retest Checklist
- [ ] Proses *Update* menolak gambar jika total gambar (Lama + Baru) melebihi 10.
- [ ] Proses *Create* menolak gambar jika total gambar (Baru saja) melebihi 10.
- [ ] Pengujian unggahan dengan tipe MIME dan besaran ukuran di ambang batas maksimum (4MB).
