---
title: feat/global-settings-module
created: 2026-06-10
status: active
---

# Plan: Global Settings Module (Key-Value Store)

Dokumen ini mendefinisikan langkah-langkah implementasi untuk modul pengaturan global yang dinamis, memungkinkan Administrator/Owner mengubah konten statis (seperti Kontak dan Alamat) tanpa perlu mengubah kode.

---

## 1. Arsitektur & Skema Database

Kita akan menggunakan arsitektur **Key-Value Store** untuk fleksibilitas maksimal.

**Tabel: `settings`**
* `id` (Primary Key)
* `key` (String, Unique) - cth: `contact_whatsapp`, `company_address`, `social_instagram`
* `value` (Text, Nullable) - cth: `081234567890`, `Jl. Raya Bypass Ngurah Rai...`
* `type` (String) - cth: `text`, `textarea`, `image` (untuk menentukan jenis input di UI)
* `group` (String) - cth: `general`, `contact`, `social` (untuk pengelompokan Tab di Admin UI)
* Timestamps

---

## 2. Business Logic & Caching Strategy

Mengingat setting akan diakses secara global, kita akan meminimalisir interaksi database.

* **Model `Setting`**: Menggunakan PHP 8.x Attributes (`#[Fillable]`).
* **Cache Observer**: Membuat *event listener* pada Model `Setting`. Setiap kali data di-Update/Create/Delete, cache dengan key `global_settings` akan otomatis di-*flush* (dihapus).
* **Global Helper**: Membuat custom file `app/Helpers/SettingsHelper.php` (didaftarkan via composer).
  * Fungsi `setting('contact_whatsapp')` yang akan mengambil data dari Cache (jika tidak ada di cache, ambil dari DB lalu simpan ke Cache selamanya).

---

## 3. Implementasi Admin Panel (UI/UX)

* **Controller**: `SettingController` dengan method `index()` (menampilkan form) dan `update()` (memproses penyimpanan masal).
* **View (`settings.index`)**: 
  * Menggunakan gaya desain **Tailwick Admin** yang rapi.
  * Menggunakan sistem **Tabs** untuk mengelompokkan pengaturan:
    * 📁 **Contact Info**: Nomor WhatsApp, Email Perusahaan, Nomor Telepon Kantor.
    * 📍 **Company Details**: Alamat Kantor, Jam Operasional.
    * 🌐 **Social Media**: Link Instagram, LinkedIn, Facebook.
* **Form Submission**: Menerima array dari input dan melakukan operasi `upsert` (Update or Insert) ke database secara efisien.

---

## 4. Integrasi Frontend (Public Page)

* Melakukan *search and replace* pada file *Blade views* (terutama `layouts.public`, `header`, `footer`, dan halaman Landing).
* Mengganti teks statis:
  * Dari: `<p>Jl. Sunset Road No. 99, Bali</p>`
  * Menjadi: `<p>{{ setting('company_address', 'Default Address') }}</p>`
* Mengganti CTA Link:
  * Menjadi: `<a href="https://wa.me/{{ format_wa_number(setting('contact_whatsapp')) }}">Hubungi Kami</a>`

---

## 5. Testing & Verification

* **Unit Test**: Memastikan Global Helper `setting()` mereturn data yang benar dan Cache bekerja.
* **Feature Test**: 
  * Memastikan Owner dapat mengupdate form setting.
  * Memastikan data tervalidasi dengan benar.
* **UI/UX Check**: Menjalankan browser lokal untuk memastikan Tab berfungsi mulus dan tidak ada error layout di Tailwick.
