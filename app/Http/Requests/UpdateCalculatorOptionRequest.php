<?php

namespace App\Http\Requests;

/**
 * Aturan validasi update identik dengan store (gambar bersifat additive,
 * tidak ada kolom unique). Extend agar tidak duplikasi.
 */
class UpdateCalculatorOptionRequest extends StoreCalculatorOptionRequest {}
