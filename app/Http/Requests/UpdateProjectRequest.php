<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProjectRequest extends FormRequest
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
        $projectId = is_object($this->route('project')) ? $this->route('project')->id : $this->route('project');

        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug,'.$projectId,
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:max_width=4096,max_height=4096',
            'status' => 'required|in:Completed,Ongoing',
            'client' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:255',
            'building_area' => 'nullable|string|max:255',
            'land_area' => 'nullable|string|max:255',
            'execution_team' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096|dimensions:max_width=4096,max_height=4096',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $project = $this->route('project');
            $uploadedImages = $this->file('gallery_images', []);

            if (! is_object($project) || empty($uploadedImages)) {
                return;
            }

            if ($project->images()->count() + count($uploadedImages) > 10) {
                $validator->errors()->add('gallery_images', 'Gallery may not contain more than 10 photos.');
            }
        });
    }
}
