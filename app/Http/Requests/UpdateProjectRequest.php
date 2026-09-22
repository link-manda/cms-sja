<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:5120',
            'status' => 'required|in:Completed,Ongoing',
            'client' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:255',
            'building_area' => 'nullable|string|max:255',
            'land_area' => 'nullable|string|max:255',
            'execution_team' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_for_sale_or_rent' => 'boolean',
            'property_type' => [
                'nullable',
                'string',
                'in:Rent,Sale,Investment',
                Rule::requiredIf(fn () => $this->boolean('is_for_sale_or_rent')),
            ],
            'price' => 'nullable|numeric|min:0|max:9999999999999.99',
            'roi_estimation' => [
                'nullable',
                'string',
                'max:65535',
                Rule::requiredIf(function () {
                    return $this->boolean('is_for_sale_or_rent') && in_array($this->input('property_type'), ['Sale', 'Investment'], true);
                }),
            ],
            'gallery_images' => 'nullable|array|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp|extensions:jpg,jpeg,png,webp|max:10240',
            'temp_gallery_images' => 'nullable|array|max:10',
            'temp_gallery_images.*' => [
                'required',
                'string',
                'max:255',
                'starts_with:projects/temp-gallery/',
                function ($attribute, $value, $fail) {
                    $filename = basename(str_replace('\\', '/', (string) $value));
                    if (! Storage::disk('public')->exists('projects/temp-gallery/'.$filename)) {
                        $fail("Temporary gallery photo [{$filename}] does not exist or has expired.");
                    }
                },
            ],
            'gallery_videos' => 'nullable|array',
            'gallery_videos.*' => ['nullable', 'string', 'url', 'regex:/^https?:\/\/((www|m)\.)?(youtube\.com\/(watch\?.*v=|embed\/|shorts\/|live\/)|youtu\.be\/)[\w\-]+/i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('gallery_videos') && is_array($this->gallery_videos)) {
            $normalizedVideos = [];
            foreach ($this->gallery_videos as $video) {
                if (is_string($video)) {
                    $video = trim($video);
                    if ($video !== '' && ! preg_match('/^https?:\/\//i', $video)) {
                        $video = 'https://'.$video;
                    }
                }
                $normalizedVideos[] = $video;
            }
            $this->merge(['gallery_videos' => $normalizedVideos]);
        }
    }

    public function messages(): array
    {
        return [
            'image.uploaded' => 'Main photo failed to upload. Use a photo up to 5 MB and make sure the server upload limit allows it.',
            'image.image' => 'Main photo must be a valid image.',
            'image.mimes' => 'Main photo format must be JPG, PNG, or WEBP.',
            'image.extensions' => 'Main photo extension must be .jpg, .jpeg, .png, or .webp.',
            'image.max' => 'Main photo may not be greater than 5 MB.',
            'property_type.required' => 'Please select a property offer type when property promotion is enabled.',
            'property_type.in' => 'Selected property offer type is invalid.',
            'roi_estimation.required' => 'The ROI estimation / profit details field is required when the property offer is for Sale or Investment.',
            'gallery_images.max' => 'Gallery may not contain more than 10 photos.',
            'gallery_images.*.uploaded' => 'Gallery photo failed to upload. Each gallery photo may not be greater than 10 MB and the server upload limit must allow 10 MB files.',
            'gallery_images.*.image' => 'Gallery file must be a valid image.',
            'gallery_images.*.mimes' => 'Gallery photo format must be JPG, PNG, or WEBP.',
            'gallery_images.*.extensions' => 'Gallery photo extension must be .jpg, .jpeg, .png, or .webp.',
            'gallery_images.*.max' => 'Each gallery photo may not be greater than 10 MB.',
            'gallery_videos.*.url' => 'Video link must be a valid URL.',
            'gallery_videos.*.regex' => 'Video link must be a valid YouTube URL (e.g. youtube.com/watch?v=... or youtu.be/...).',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $project = $this->route('project');
            $uploadedImages = $this->file('gallery_images', []);
            $tempImages = $this->input('temp_gallery_images', []);
            $videoUrls = array_filter($this->input('gallery_videos', []), fn ($v) => ! empty(trim((string) $v)));

            if (! ($project instanceof Project)) {
                $project = Project::find($project);
            }

            if (! $project) {
                return;
            }

            $currentCount = $project->images()->count();
            $newCount = count($uploadedImages) + count($tempImages) + count($videoUrls);

            if ($currentCount + $newCount > 10) {
                $message = count($videoUrls) > 0
                    ? "Gallery may not contain more than 10 total items (photos and videos combined) per project. Currently has {$currentCount} items."
                    : 'Gallery may not contain more than 10 photos per project.';

                $validator->errors()->add('gallery_images', $message);
                if (count($videoUrls) > 0) {
                    $validator->errors()->add('gallery_videos', $message);
                }
            }
        });
    }
}
