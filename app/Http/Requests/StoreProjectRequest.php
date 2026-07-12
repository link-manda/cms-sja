<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:2048|dimensions:max_width=4096,max_height=4096',
            'status' => 'required|in:Completed,Ongoing',
            'client' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:255',
            'building_area' => 'nullable|string|max:255',
            'land_area' => 'nullable|string|max:255',
            'execution_team' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:4096|dimensions:max_width=4096,max_height=4096',
        ];
    }

    public function messages(): array
    {
        return [
            'image.uploaded' => 'Main photo failed to upload. Use a photo up to 2 MB and make sure the server upload limit allows it.',
            'image.image' => 'Main photo must be a valid image.',
            'image.mimes' => 'Main photo format must be JPG, PNG, or WEBP.',
            'image.extensions' => 'Main photo extension must be .jpg, .jpeg, .png, or .webp.',
            'image.max' => 'Main photo may not be greater than 2 MB.',
            'image.dimensions' => 'Main photo resolution may not be greater than 4096×4096 pixels.',
            'gallery_images.max' => 'Gallery may not contain more than 10 photos.',
            'gallery_images.*.uploaded' => 'Gallery photo failed to upload. Each gallery photo may not be greater than 4 MB and the server upload limit must allow 4 MB files.',
            'gallery_images.*.image' => 'Gallery file must be a valid image.',
            'gallery_images.*.mimes' => 'Gallery photo format must be JPG, PNG, or WEBP.',
            'gallery_images.*.extensions' => 'Gallery photo extension must be .jpg, .jpeg, .png, or .webp.',
            'gallery_images.*.max' => 'Each gallery photo may not be greater than 4 MB.',
            'gallery_images.*.dimensions' => 'Gallery photo resolution may not be greater than 4096×4096 pixels.',
        ];
    }
}
