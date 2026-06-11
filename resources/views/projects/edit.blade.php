@extends('layouts.vertical', ['title' => 'Edit Project'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Edit Project'])

    <div class="card">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Edit Project Form</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.update', $project->id) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Project Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="title">Project Name <span class="text-danger">*</span></label>
                        <input class="form-input" id="title" name="title" value="{{ old('title', $project->title) }}" placeholder="e.g. Modern Tropical Villa" type="text" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="slug">Slug URL (SEO) <span class="text-danger">*</span></label>
                        <input class="form-input" id="slug" name="slug" value="{{ old('slug', $project->slug) }}" placeholder="e.g. modern-tropical-villa" type="text" required />
                        <p class="text-xs text-default-400 mt-1">Generated automatically when typing the project name (can be edited manually if needed).</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Location -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="location">Project Location <span class="text-danger">*</span></label>
                        <input class="form-input" id="location" name="location" value="{{ old('location', $project->location) }}" placeholder="e.g. Canggu, Bali" type="text" required />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="status">Project Status <span class="text-danger">*</span></label>
                        <select class="form-input" id="status" name="status" required>
                            <option value="Ongoing" {{ old('status', $project->status) === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ old('status', $project->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Category Selection -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="category_id">Project Category <span class="text-danger">*</span></label>
                        <select class="form-input" id="category_id" name="category_id" required>
                            <option value="" disabled>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <!-- Client Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="client">Client Name</label>
                        <input class="form-input" id="client" name="client" value="{{ old('client', $project->client) }}" placeholder="e.g. Private Owner / PT. Developer" type="text" />
                        <x-input-error :messages="$errors->get('client')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Year -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="year">Year of Completion</label>
                        <input class="form-input" id="year" name="year" value="{{ old('year', $project->year) }}" placeholder="e.g. 2025 or Ongoing" type="text" />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <!-- Execution Team -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="execution_team">Execution Team</label>
                        <input class="form-input" id="execution_team" name="execution_team" value="{{ old('execution_team', $project->execution_team) }}" placeholder="e.g. SJA Bali Engineering Unit" type="text" />
                        <x-input-error :messages="$errors->get('execution_team')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Building Area -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="building_area">Building Area (Luas Bangunan)</label>
                        <input class="form-input" id="building_area" name="building_area" value="{{ old('building_area', $project->building_area) }}" placeholder="e.g. 450 sqm" type="text" />
                        <x-input-error :messages="$errors->get('building_area')" class="mt-2" />
                    </div>

                    <!-- Land Area -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="land_area">Land Area (Luas Tanah)</label>
                        <input class="form-input" id="land_area" name="land_area" value="{{ old('land_area', $project->land_area) }}" placeholder="e.g. 800 sqm" type="text" />
                        <x-input-error :messages="$errors->get('land_area')" class="mt-2" />
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="description">Project Description <span class="text-danger">*</span></label>
                    <textarea class="form-input min-h-[150px]" id="description" name="description" placeholder="Write down the full description of the project..." required>{{ old('description', $project->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Main Photo -->
                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="image">Replace Main Project Photo (Max: 2MB)</label>
                    @if ($project->image)
                        <div class="mb-3">
                            <p class="text-xs font-semibold text-default-500 mb-1.5">Current Photo:</p>
                            <img src="{{ asset('storage/projects/' . $project->image) }}" alt="{{ $project->title }}" class="h-24 w-36 object-cover rounded border border-default-200">
                        </div>
                    @endif
                    <input class="form-input p-1.5" id="image" name="image" type="file" accept="image/*" />
                    <p class="text-xs text-default-400 mt-1">Leave blank if you do not want to replace the project photo.</p>
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>

                <!-- Search Engine Settings (SEO) -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-4 flex items-center gap-1">
                        <i class="size-4" data-lucide="globe"></i> Search Engine Optimization (SEO) - Optional
                    </h6>
                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="meta_title">Meta Title</label>
                            <input class="form-input" id="meta_title" name="meta_title" value="{{ old('meta_title', $project->meta_title) }}" placeholder="Custom search title..." type="text" />
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                        </div>
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="meta_description">Meta Description</label>
                            <textarea class="form-input min-h-[80px]" id="meta_description" name="meta_description" placeholder="Short search description for Google...">{{ old('meta_description', $project->meta_description) }}</textarea>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                    <a href="{{ route('projects.index') }}" class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">Cancel</a>
                    <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');

            if (titleInput && slugInput) {
                titleInput.addEventListener('input', function() {
                    let title = this.value;
                    let slug = title.toLowerCase()
                                    .trim()
                                    .replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                                    .replace(/\s+/g, '-') // collapse whitespace and replace by -
                                    .replace(/-+/g, '-'); // collapse dashes
                    slugInput.value = slug;
                });
            }
        });
    </script>
@endsection
