@extends('layouts.vertical', ['title' => 'Add Project'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Add Project'])

    <div class="card">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Add New Project</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Project Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="title">Project Name <span class="text-danger">*</span></label>
                        <input class="form-input" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Modern Tropical Villa" type="text" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="slug">Slug URL (SEO) <span class="text-danger">*</span></label>
                        <input class="form-input" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. modern-tropical-villa" type="text" required />
                        <p class="text-xs text-default-400 mt-1">Generated automatically when typing the project name, must be unique.</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Location -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="location">Project Location <span class="text-danger">*</span></label>
                        <input class="form-input" id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Canggu, Bali" type="text" required />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="status">Project Status <span class="text-danger">*</span></label>
                        <select class="form-input" id="status" name="status" required>
                            <option value="Ongoing" {{ old('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Category Selection -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="category_id">Project Category <span class="text-danger">*</span></label>
                        <select class="form-input" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <!-- Client Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="client">Client Name</label>
                        <input class="form-input" id="client" name="client" value="{{ old('client') }}" placeholder="e.g. Private Owner / PT. Developer" type="text" />
                        <x-input-error :messages="$errors->get('client')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Year -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="year">Year of Completion</label>
                        <input class="form-input" id="year" name="year" value="{{ old('year') }}" placeholder="e.g. 2025 or Ongoing" type="text" />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <!-- Execution Team -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="execution_team">Execution Team</label>
                        <input class="form-input" id="execution_team" name="execution_team" value="{{ old('execution_team') }}" placeholder="e.g. SJA Bali Engineering Unit" type="text" />
                        <x-input-error :messages="$errors->get('execution_team')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Building Area -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="building_area">Building Area (Luas Bangunan)</label>
                        <input class="form-input" id="building_area" name="building_area" value="{{ old('building_area') }}" placeholder="e.g. 450 sqm" type="text" />
                        <x-input-error :messages="$errors->get('building_area')" class="mt-2" />
                    </div>

                    <!-- Land Area -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="land_area">Land Area (Luas Tanah)</label>
                        <input class="form-input" id="land_area" name="land_area" value="{{ old('land_area') }}" placeholder="e.g. 800 sqm" type="text" />
                        <x-input-error :messages="$errors->get('land_area')" class="mt-2" />
                    </div>
                </div>

                <!-- Full Description -->
                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="description">Project Description <span class="text-danger">*</span></label>
                    <textarea class="form-input min-h-[150px]" id="description" name="description" placeholder="Write down the full description of the project..." required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Main Photo -->
                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="image">Main Project Photo (Max: 2MB) <span class="text-danger">*</span></label>
                    <input class="form-input p-1.5" id="image" name="image" type="file" accept="image/*" required />
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>

                <!-- Gallery Photos -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-4 flex items-center gap-1">
                        <i class="size-4" data-lucide="image"></i> Project Gallery (Optional)
                    </h6>
                    <div>
                        <!-- Native Drag and Drop Zone -->
                        <div id="gallery-dropzone" class="relative flex flex-col items-center justify-center p-8 border-2 border-dashed border-default-300 rounded-lg bg-default-50 hover:bg-default-100 transition-colors cursor-pointer group">
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" id="gallery_images" name="gallery_images[]" type="file" accept="image/*" multiple />
                            
                            <div class="flex flex-col items-center pointer-events-none">
                                <i class="size-10 text-default-400 group-hover:text-primary transition-colors mb-3" data-lucide="upload-cloud"></i>
                                <p class="text-sm font-medium text-default-700">Drag & Drop your images here</p>
                                <p class="text-xs text-default-400 mt-1">or click to browse from your computer</p>
                            </div>
                        </div>
                        
                        <!-- File Preview Area -->
                        <div id="gallery-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4 hidden">
                            <!-- JS will inject previews here -->
                        </div>

                        <p class="text-xs text-default-400 mt-2">You can select multiple files at once. Max 10 photos, up to 4MB each. Format: JPG, PNG, WEBP.</p>
                        
                        <!-- Fixed Validation Errors (Wildcard Array Loop) -->
                        @if($errors->hasAny(['gallery_images', 'gallery_images.*']))
                            <div class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-3">
                                @foreach($errors->get('gallery_images.*') as $messages)
                                    @foreach($messages as $message)
                                        <p class="text-sm flex items-center gap-1"><i class="size-4" data-lucide="alert-circle"></i> {{ $message }}</p>
                                    @endforeach
                                @endforeach
                                @if($errors->has('gallery_images'))
                                    <p class="text-sm flex items-center gap-1"><i class="size-4" data-lucide="alert-circle"></i> {{ $errors->first('gallery_images') }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SEO Settings (Optional) -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-4 flex items-center gap-1">
                        <i class="size-4" data-lucide="globe"></i> Search Engine Optimization (SEO) - Optional
                    </h6>
                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="meta_title">Meta Title</label>
                            <input class="form-input" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" placeholder="Custom search title..." type="text" />
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                        </div>
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="meta_description">Meta Description</label>
                            <textarea class="form-input min-h-[80px]" id="meta_description" name="meta_description" placeholder="Short search description for Google...">{{ old('meta_description') }}</textarea>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                    <a href="{{ route('projects.index') }}" class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">Cancel</a>
                    <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Project</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Drag and Drop Gallery Logic
            const dropzone = document.getElementById('gallery-dropzone');
            const fileInput = document.getElementById('gallery_images');
            const previewArea = document.getElementById('gallery-preview');

            if (dropzone && fileInput && previewArea) {
                const handleFiles = (files) => {
                    previewArea.innerHTML = ''; // Clear preview
                    previewArea.classList.remove('hidden');
                    
                    Array.from(files).forEach((file, index) => {
                        if (!file.type.startsWith('image/')) return;
                        
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const div = document.createElement('div');
                            div.className = 'relative rounded overflow-hidden aspect-square border border-default-200';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-semibold px-2 text-center line-clamp-2">${file.name}</span>
                                </div>
                            `;
                            previewArea.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                };

                // Handle file input change
                fileInput.addEventListener('change', (e) => {
                    handleFiles(e.target.files);
                });

                // Drag and Drop events
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, unhighlight, false);
                });

                function highlight(e) {
                    dropzone.classList.add('bg-primary/10', 'border-primary');
                }

                function unhighlight(e) {
                    dropzone.classList.remove('bg-primary/10', 'border-primary');
                }

                dropzone.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    let dt = e.dataTransfer;
                    let files = dt.files;
                    
                    // Assign files to the native input
                    const dataTransfer = new DataTransfer();
                    Array.from(files).forEach(file => dataTransfer.items.add(file));
                    fileInput.files = dataTransfer.files;
                    
                    handleFiles(files);
                }
            }

            // Slug Auto-generation
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
