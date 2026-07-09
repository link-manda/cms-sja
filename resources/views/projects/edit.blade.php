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

                <!-- Add Gallery Photos -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-4 flex items-center gap-1">
                        <i class="size-4" data-lucide="image"></i> Add Gallery Photos
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

                        <p class="text-xs text-default-400 mt-2">Upload additional photos to the gallery. Max 10 photos, up to 4MB each. Format: JPG, PNG, WEBP.</p>
                        
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

    <!-- Gallery Management Card -->
    @if($project->images->count() > 0)
    <div class="card mt-6">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Current Project Gallery</h6>
            <p class="text-sm text-default-500 mt-1">Manage existing photos in the gallery. Deleting a photo here will remove it immediately.</p>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($project->images as $image)
                    <div class="relative group rounded-lg overflow-hidden border border-default-200 shadow-sm hover:shadow-md transition-all">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-32 object-cover" alt="Gallery Image">
                        
                        <!-- Delete Button (Triggers Modal) -->
                        <button type="button" 
                                class="dynamic-action-btn absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-danger/90 hover:bg-danger text-white p-1.5 rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform"
                                data-hs-overlay="#dynamic-action-modal"
                                data-action-url="{{ route('projects.gallery.delete', $image->id) }}"
                                data-action-type="delete-gallery">
                            <i class="size-4" data-lucide="x"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Dynamic Action Modal -->
    <div id="dynamic-action-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="w-full flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-zinc-900 dark:border-zinc-800 dark:shadow-slate-700/70">
                <div class="p-6 overflow-y-auto text-center">
                    <div class="inline-flex justify-center items-center size-[62px] rounded-full border-4 border-warning/20 bg-warning/10 text-warning mb-4">
                        <i class="size-6" data-lucide="alert-triangle"></i>
                    </div>
                    <h3 id="modal-title" class="mb-2 text-xl font-bold text-default-800">Are you sure?</h3>
                    <p id="modal-description" class="text-default-500 font-sans">Do you really want to perform this action?</p>
                    <form id="modal-action-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="_method" id="modal-method" value="DELETE">
                        <div class="mt-8 flex justify-center gap-3">
                            <button type="button" class="btn bg-default-200 text-default-800 hover:bg-default-300 transition-colors" data-hs-overlay="#dynamic-action-modal">Cancel</button>
                            <button type="submit" id="modal-submit-btn" class="btn bg-danger text-white transition-colors">Yes, Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
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

            // Dynamic Modal Logic
            const actionButtons = document.querySelectorAll('.dynamic-action-btn');
            const modalForm = document.getElementById('modal-action-form');
            const modalTitle = document.getElementById('modal-title');
            const modalDescription = document.getElementById('modal-description');
            const modalMethod = document.getElementById('modal-method');
            const modalSubmitBtn = document.getElementById('modal-submit-btn');

            if (actionButtons.length > 0) {
                actionButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const actionUrl = this.getAttribute('data-action-url');
                        const actionType = this.getAttribute('data-action-type');
                        
                        modalForm.action = actionUrl;
                        
                        if (actionType === 'delete-gallery') {
                            modalMethod.value = 'DELETE';
                            modalTitle.textContent = 'Delete Photo?';
                            modalDescription.textContent = 'Are you sure you want to delete this photo from the gallery? This action cannot be undone.';
                            modalSubmitBtn.className = 'btn bg-danger text-white hover:bg-red-700';
                            modalSubmitBtn.innerHTML = 'Yes, Delete';
                        }
                    });
                });
            }
        });
    </script>
@endsection
