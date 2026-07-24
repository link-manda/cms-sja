<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCalculatorOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price_range' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:4096',
            'image_zones' => 'nullable|array',
            'image_zones.*' => 'required|in:2d,3d,proses',
        ];
    }

    /**
     * Batas total 10 gambar dihitung lintas ketiga zona.
     * Saat Update, jumlah gambar yang sudah tersimpan ikut dihitung agar
     * total (existing + upload baru) tidak dapat mem-bypass batas 10.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $existingCount = 0;

                // Jika request ini adalah Update, hitung gambar yang sudah ada di DB
                if ($calculator = $this->route('calculator')) {
                    $existingCount = $calculator->images()->count();
                }

                $newUploads = count($this->file('images', []));

                if (($existingCount + $newUploads) > 10) {
                    $validator->errors()->add('images', 'Total gallery images (existing + new uploads) may not exceed 10 photos.');
                }

                $zones = $this->input('image_zones', []);

                if (! is_array($zones) || $newUploads !== count($zones)) {
                    $validator->errors()->add('image_zones', 'Each image must have a valid zone.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'images.*.max' => 'Each image may not be greater than 4 MB.',
            'images.*.mimes' => 'Image format must be JPG, PNG, or WEBP.',
        ];
    }
}
