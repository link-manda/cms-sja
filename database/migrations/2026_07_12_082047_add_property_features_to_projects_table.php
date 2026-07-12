<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration (Menambah kolom baru).
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Kolom boolean untuk toggle on/off fitur jualan di admin
            $table->boolean('is_for_sale_or_rent')->default(false)->after('description');

            // Kolom jenis properti (Sewa atau Jual)
            $table->string('property_type')->nullable()->after('is_for_sale_or_rent');

            // Kolom harga properti
            $table->decimal('price', 15, 2)->nullable()->after('property_type');

            // Kolom teks untuk menjelaskan estimasi balik modal (ROE/ROI)
            $table->text('roi_estimation')->nullable()->after('price');
        });
    }

    /**
     * Batalkan migration (Menghapus kolom jika di-rollback).
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'is_for_sale_or_rent',
                'property_type',
                'price',
                'roi_estimation'
            ]);
        });
    }
};