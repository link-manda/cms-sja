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
            'images_2d' => 'nullable|array',
            'images_3d' => 'nullable|array',
            'images_proses' => 'nullable|array',
            'images_2d.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:4096|dimensions:max_width=4096,max_height=4096',
            'images_3d.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:4096|dimensions:max_width=4096,max_height=4096',
            'images_proses.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:4096|dimensions:max_width=4096,max_height=4096',
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

                $newUploads = count($this->file('images_2d', []))
                    + count($this->file('images_3d', []))
                    + count($this->file('images_proses', []));

                if (($existingCount + $newUploads) > 10) {
                    $validator->errors()->add('images_2d', 'Total gallery images (existing + new uploads) may not exceed 10 photos.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'images_2d.*.max' => 'Each 2D image may not be greater than 4 MB.',
            'images_3d.*.max' => 'Each 3D image may not be greater than 4 MB.',
            'images_proses.*.max' => 'Each process image may not be greater than 4 MB.',
            'images_2d.*.mimes' => '2D image format must be JPG, PNG, or WEBP.',
            'images_3d.*.mimes' => '3D image format must be JPG, PNG, or WEBP.',
            'images_proses.*.mimes' => 'Process image format must be JPG, PNG, or WEBP.',
        ];
    }
}
