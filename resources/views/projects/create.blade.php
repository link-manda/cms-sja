@extends('layouts.vertical', ['title' => 'Add Project'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Add Project'])

    @if ($errors->any())
        <div id="global-error-banner" class="mb-5 rounded-lg border border-rose-500/20 bg-rose-500/10 p-4 text-rose-700 dark:text-rose-400 shadow-sm animate-in fade-in">
            <div class="flex items-start gap-3">
                <div class="rounded-full bg-rose-500/20 p-2 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5">
                    <i data-lucide="alert-circle" class="size-5"></i>
                </div>
                <div class="flex-1 text-sm">
                    <h6 class="font-semibold text-rose-800 dark:text-rose-300 text-base mb-1">
                        Terdapat {{ $errors->count() }} kesalahan validasi formulir:
                    </h6>
                    <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 dark:text-rose-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-xs text-rose-600/80 dark:text-rose-400/80">
                        Silakan periksa dan perbaiki field terkait di bawah ini sebelum menyimpan kembali.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Add New Project</h6>
        </div>
        <div class="card-body">
            <form id="project-form" method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Project Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="title">Project Name <span
                                class="text-danger">*</span></label>
                        <input class="form-input" id="title" name="title" value="{{ old('title') }}"
                            placeholder="e.g. Modern Tropical Villa" type="text" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="slug">Slug URL (SEO) <span
                                class="text-danger">*</span></label>
                        <input class="form-input" id="slug" name="slug" value="{{ old('slug') }}"
                            placeholder="e.g. modern-tropical-villa" type="text" required />
                        <p class="text-xs text-default-400 mt-1">Generated automatically when typing the project name, must
                            be unique.</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Location -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="location">Project Location <span
                                class="text-danger">*</span></label>
                        <input class="form-input" id="location" name="location" value="{{ old('location') }}"
                            placeholder="e.g. Canggu, Bali" type="text" required />
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="status">Project Status <span
                                class="text-danger">*</span></label>
                        <select class="form-input" id="status" name="status" required>
                            <option value="Ongoing" {{ old('status') === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Completed" {{ old('status') === 'Completed' ? 'selected' : '' }}>Completed
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Category Selection -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="category_id">Project Category
                            <span class="text-danger">*</span></label>
                        <select class="form-input" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <!-- Client Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="client">Client Name</label>
                        <input class="form-input" id="client" name="client" value="{{ old('client') }}"
                            placeholder="e.g. Private Owner / PT. Developer" type="text" />
                        <x-input-error :messages="$errors->get('client')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Year -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="year">Year of
                            Completion</label>
                        <input class="form-input" id="year" name="year" value="{{ old('year') }}"
                            placeholder="e.g. 2025 or Ongoing" type="text" />
                        <x-input-error :messages="$errors->get('year')" class="mt-2" />
                    </div>

                    <!-- Execution Team -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="execution_team">Execution
                            Team</label>
                        <input class="form-input" id="execution_team" name="execution_team"
                            value="{{ old('execution_team') }}" placeholder="e.g. SJA Bali Engineering Unit"
                            type="text" />
                        <x-input-error :messages="$errors->get('execution_team')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Building Area -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="building_area">Building Area
                            (Luas Bangunan)</label>
                        <input class="form-input" id="building_area" name="building_area"
                            value="{{ old('building_area') }}" placeholder="e.g. 450 sqm" type="text" />
                        <x-input-error :messages="$errors->get('building_area')" class="mt-2" />
                    </div>

                    <!-- Land Area -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="land_area">Land Area (Luas
                            Tanah)</label>
                        <input class="form-input" id="land_area" name="land_area" value="{{ old('land_area') }}"
                            placeholder="e.g. 800 sqm" type="text" />
                        <x-input-error :messages="$errors->get('land_area')" class="mt-2" />
                    </div>
                </div>

                <!-- Full Description -->
                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="description">Project Description
                        <span class="text-danger">*</span></label>
                    <textarea class="form-input min-h-[150px]" id="description" name="description"
                        placeholder="Write down the full description of the project..." required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Main Photo -->
                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="image">Main Project Photo (Max:
                        2MB) <span class="text-danger">*</span></label>
                    <input class="form-input p-1.5" id="image" name="image" type="file"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required />
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                    @if ($errors->any())
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1.5 flex items-center gap-1">
                            <i data-lucide="alert-triangle" class="size-3.5 inline"></i>
                            Pilih ulang file gambar utama jika form sebelumnya mengalami kesalahan validasi (kebijakan keamanan browser).
                        </p>
                    @endif
                </div>

                <!-- SEO Image Studio Helper Callout -->
                <x-seo-image-studio-callout />

                <!-- Gallery Photos -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-4 flex items-center gap-1">
                        <i class="size-4" data-lucide="image"></i> Project Gallery (Optional)
                    </h6>
                    <div>
                        <!-- Native Drag and Drop Zone -->
                        <div id="gallery-dropzone"
                            class="relative flex flex-col items-center justify-center p-8 border-2 border-dashed border-default-300 rounded-lg bg-default-50 hover:bg-default-100 transition-colors cursor-pointer group">
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                id="gallery_images" name="gallery_images[]" type="file"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple />

                            <div class="flex flex-col items-center pointer-events-none">
                                <i class="size-10 text-default-400 group-hover:text-primary transition-colors mb-3"
                                    data-lucide="upload-cloud"></i>
                                <p class="text-sm font-medium text-default-700">Drag & Drop your images here</p>
                                <p class="text-xs text-default-400 mt-1">or click to browse from your computer</p>
                            </div>
                        </div>

                        <!-- File Preview Area -->
                        <div id="gallery-preview"
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4 hidden">
                            <!-- JS will inject previews here -->
                        </div>

                        <!-- Container for Old Uploaded Temporary Images (if validation failed) -->
                        <div id="persisted-temp-images-container">
                            @if(old('temp_gallery_images'))
                                @foreach(old('temp_gallery_images') as $tempImg)
                                    <input type="hidden" name="temp_gallery_images[]" value="{{ $tempImg }}" data-temp-path="{{ $tempImg }}">
                                @endforeach
                            @endif
                        </div>

                        <p class="text-xs text-default-400 mt-2">You can select multiple files at once. Combined gallery quota: max 10 items (photos + videos). Up to 4MB each for photos, max resolution 4096×4096px. Format: JPG, PNG, WEBP.</p>
                        <div id="gallery-upload-errors"
                            class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-3 hidden"></div>

                        <!-- Fixed Validation Errors (Wildcard Array Loop) -->
                        @if ($errors->hasAny(['gallery_images', 'gallery_images.*']))
                            <div class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-3">
                                @if ($errors->has('gallery_images'))
                                    <p class="text-sm flex items-center gap-1"><i class="size-4"
                                            data-lucide="alert-circle"></i> {{ $errors->first('gallery_images') }}</p>
                                @endif
                                @foreach ($errors->get('gallery_images.*') as $messages)
                                    @foreach ($messages as $message)
                                        <p class="text-sm flex items-center gap-1"><i class="size-4"
                                                data-lucide="alert-circle"></i> {{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery Videos (YouTube) -->
                <div class="border-t border-default-200 pt-5">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h6 class="text-sm font-semibold text-default-800 flex items-center gap-1">
                                <i class="size-4 text-danger" data-lucide="video"></i> Project Videos (YouTube) - Optional
                            </h6>
                            <p class="text-xs text-default-500 mt-0.5">Add YouTube video links (Standard, Shorts, or youtu.be). Combined with photos, maximum 10 items.</p>
                        </div>
                        <button type="button" id="add-video-btn" class="btn btn-sm border border-default-300 hover:bg-default-100 text-default-700 flex items-center gap-1 cursor-pointer">
                            <i class="size-3.5" data-lucide="plus"></i> Add Video Link
                        </button>
                    </div>

                    <div id="video-inputs-container" class="space-y-3 mt-3">
                        @php
                            $oldVideos = old('gallery_videos', ['']);
                        @endphp
                        @foreach($oldVideos as $index => $videoVal)
                            <div class="video-input-group space-y-2 p-3 rounded-lg border border-default-200 bg-default-50/50">
                                <div class="video-input-row flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-default-400">
                                            <i class="size-4 text-danger" data-lucide="youtube"></i>
                                        </span>
                                        <input type="url" name="gallery_videos[]" value="{{ $videoVal }}" placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..." class="form-input youtube-url-input pl-9 text-sm">
                                    </div>
                                    <button type="button" class="remove-video-row-btn p-2 text-default-400 hover:text-danger rounded border border-default-200 hover:border-danger/30 transition-colors {{ count($oldVideos) <= 1 && empty($videoVal) ? 'hidden' : '' }}" title="Remove link">
                                        <i class="size-4" data-lucide="trash-2"></i>
                                    </button>
                                </div>
                                <div class="youtube-preview-card hidden rounded-lg border border-default-200 bg-card p-2.5 text-xs flex items-center gap-3"></div>
                            </div>
                        @endforeach
                    </div>

                    @if ($errors->hasAny(['gallery_videos', 'gallery_videos.*']))
                        <div class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-3">
                            @if ($errors->has('gallery_videos'))
                                <p class="text-sm flex items-center gap-1"><i class="size-4"
                                        data-lucide="alert-circle"></i> {{ $errors->first('gallery_videos') }}</p>
                            @endif
                            @foreach ($errors->get('gallery_videos.*') as $messages)
                                @foreach ($messages as $message)
                                    <p class="text-sm flex items-center gap-1"><i class="size-4"
                                            data-lucide="alert-circle"></i> {{ $message }}</p>
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Promotion & Investment Features -->
                <div class="p-6 bg-default-50 border border-default-200 rounded-lg mt-6 mb-6">
                    <h3 class="text-lg font-bold text-default-900 mb-4">Promotion & Investment Features</h3>

                    <!-- Toggle Switch (Checkbox) -->
                    <div class="mb-4 flex items-center">
                        <input type="hidden" name="is_for_sale_or_rent" value="0">
                        <input type="checkbox" id="is_for_sale_or_rent" name="is_for_sale_or_rent" value="1"
                            class="w-5 h-5 text-primary rounded border-default-300 cursor-pointer" {{ old('is_for_sale_or_rent') ? 'checked' : '' }}>
                        <label for="is_for_sale_or_rent" class="ml-2 text-sm font-medium text-default-700 cursor-pointer">
                            Enable Property Promotion (Display Rent/Sale/Investment Price on Public Page)
                        </label>
                    </div>

                    <div id="promotion-fields-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ old('is_for_sale_or_rent') ? '' : 'hidden' }}">
                        <!-- Property Type -->
                        <div>
                            <label class="block text-sm font-medium text-default-700 mb-1" for="property_type">
                                Offer Type <span class="text-danger">*</span>
                            </label>
                            <select id="property_type" name="property_type"
                                class="form-input">
                                <option value="">-- Select Type --</option>
                                <option value="Sale" {{ old('property_type') == 'Sale' ? 'selected' : '' }}>For Sale (Unit/Land)</option>
                                <option value="Investment" {{ old('property_type') == 'Investment' ? 'selected' : '' }}>For Investment (Commercial Property)</option>
                                <option value="Rent" {{ old('property_type') == 'Rent' ? 'selected' : '' }}>For Rent (Boarding/Kos / Villa)</option>
                            </select>
                            <x-input-error :messages="$errors->get('property_type')" class="mt-1" />
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-default-700 mb-1" for="price">Price (IDR)</label>
                            <input type="number" id="price" name="price" placeholder="Example: 1500000" value="{{ old('price') }}"
                                class="form-input">
                            <span class="text-xs text-default-500">Numbers only without dots (e.g., 1500000)</span>
                            <x-input-error :messages="$errors->get('price')" class="mt-1" />
                        </div>

                        @php
                            $isRoiVisible = old('is_for_sale_or_rent') && in_array(old('property_type'), ['Sale', 'Investment']);
                            $isKosVisible = old('is_for_sale_or_rent') && old('property_type') === 'Rent';
                        @endphp

                        <!-- ROI Estimation -->
                        <div id="roi-estimation-wrapper" class="md:col-span-2 {{ $isRoiVisible ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-default-700 mb-1" for="roi_estimation">
                                ROI Estimation / Profit Details <span class="text-danger">*</span>
                            </label>
                            <textarea id="roi_estimation" name="roi_estimation" rows="3"
                                placeholder="e.g. Projected ROI 12% per year with estimated payback period within 5-6 years."
                                class="form-input">{{ old('roi_estimation') }}</textarea>
                            <x-input-error :messages="$errors->get('roi_estimation')" class="mt-1" />
                        </div>

                        <!-- Rental / Kos-kosan Callout Info -->
                        <div id="kos-info-callout" class="md:col-span-2 {{ $isKosVisible ? '' : 'hidden' }}">
                            <div class="p-4 rounded-lg bg-primary/5 border border-primary/20 text-xs text-default-600 flex items-start gap-2.5">
                                <i class="size-4 text-primary shrink-0 mt-0.5" data-lucide="info"></i>
                                <div>
                                    <p class="font-semibold text-primary">Informasi Tipe Sewa / Kos-kosan</p>
                                    <p class="mt-0.5 text-default-500">Untuk properti kos-kosan atau sewa, rincian biaya sewa dan fasilitas kamar sudah tercakup pada bagian <strong>Project Description</strong> sehingga estimasi ROI tidak diperlukan dan otomatis dinonaktifkan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings (Optional) -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-4 flex items-center gap-1">
                        <i class="size-4" data-lucide="globe"></i> Search Engine Optimization (SEO) - Optional
                    </h6>
                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="meta_title">Meta
                                Title</label>
                            <input class="form-input" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                placeholder="Custom search title..." type="text" />
                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                        </div>
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="meta_description">Meta
                                Description</label>
                            <textarea class="form-input min-h-[80px]" id="meta_description" name="meta_description"
                                placeholder="Short search description for Google...">{{ old('meta_description') }}</textarea>
                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                    <a href="{{ route('projects.index') }}"
                        class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">Cancel</a>
                    <button type="submit" id="save-project-btn" class="btn bg-primary text-white cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="check" class="size-4"></i> Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Progress Modal -->
    <div id="upload-progress-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden transition-opacity">
        <div class="bg-card w-full max-w-lg rounded-xl border border-default-200 shadow-2xl p-6 space-y-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-default-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <i data-lucide="upload-cloud" class="size-5"></i>
                    </div>
                    <div>
                        <h5 class="text-base font-semibold text-default-900">Mengunggah Foto Galeri</h5>
                        <p class="text-xs text-default-500">Proses unggah bertahap untuk menjaga stabilitas data</p>
                    </div>
                </div>
                <span id="modal-percentage-badge" class="px-2.5 py-1 text-xs font-bold rounded-full bg-primary/10 text-primary">0%</span>
            </div>

            <!-- Overall Progress Bar -->
            <div class="space-y-1.5">
                <div class="flex justify-between text-xs text-default-600 font-medium">
                    <span id="modal-status-text">Menyiapkan antrean foto...</span>
                    <span id="modal-count-text">0 / 0</span>
                </div>
                <div class="w-full h-2.5 bg-default-100 rounded-full overflow-hidden">
                    <div id="modal-progress-bar" class="h-full bg-primary transition-all duration-300 ease-out rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- Active File Progress Info -->
            <div id="modal-active-file-box" class="p-3 bg-default-50 rounded-lg border border-default-200 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2 truncate max-w-[80%]">
                    <i data-lucide="file-image" class="size-4 text-primary shrink-0"></i>
                    <span id="modal-active-file-name" class="truncate font-medium text-default-700">Foto 1.jpg</span>
                </div>
                <span id="modal-active-file-status" class="text-default-500 shrink-0 font-medium">0%</span>
            </div>

            <!-- Error Action Panel -->
            <div id="modal-error-panel" class="hidden p-3 bg-rose-500/10 border border-rose-500/20 rounded-lg text-xs space-y-2">
                <div class="flex items-start gap-2 text-rose-600 dark:text-rose-400">
                    <i data-lucide="alert-triangle" class="size-4 shrink-0 mt-0.5"></i>
                    <span id="modal-error-message">Gagal mengunggah foto. Koneksi terputus atau file tidak valid.</span>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" id="modal-skip-btn" class="px-3 py-1.5 rounded bg-default-200 hover:bg-default-300 text-default-800 font-medium text-xs transition-colors cursor-pointer">
                        Lewati & Lanjutkan Simpan
                    </button>
                    <button type="button" id="modal-retry-btn" class="px-3 py-1.5 rounded bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs transition-colors cursor-pointer flex items-center gap-1">
                        <i data-lucide="refresh-cw" class="size-3"></i> Coba Lagi
                    </button>
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
                const errorBox = document.getElementById('gallery-upload-errors');
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const maxFileSize = 4 * 1024 * 1024;
                const maxFiles = 10;

                const showGalleryErrors = (messages) => {
                    if (!errorBox) return;

                    errorBox.innerHTML = '';
                    messages.forEach((message) => {
                        const p = document.createElement('p');
                        p.className = 'text-sm flex items-center gap-1';
                        p.textContent = message;
                        errorBox.appendChild(p);
                    });
                    errorBox.classList.remove('hidden');
                };

                const clearGalleryErrors = () => {
                    if (!errorBox) return;

                    errorBox.innerHTML = '';
                    errorBox.classList.add('hidden');
                };

                const validateFiles = (files) => {
                    const errors = [];
                    const selectedFiles = Array.from(files);

                    if (selectedFiles.length > maxFiles) {
                        errors.push('You can upload up to 10 gallery photos.');
                    }

                    selectedFiles.forEach((file) => {
                        const extension = file.name.split('.').pop().toLowerCase();

                        if (!allowedTypes.includes(file.type) || !allowedExtensions.includes(
                            extension)) {
                            errors.push(`${file.name}: format must be JPG, PNG, or WEBP.`);
                        }

                        if (file.size > maxFileSize) {
                            errors.push(`${file.name}: size may not be greater than 4 MB.`);
                        }
                    });

                    return errors;
                };

                const appendPreview = (file, src) => {
                    const div = document.createElement('div');
                    div.className = 'relative rounded overflow-hidden aspect-square border border-default-200';

                    const img = document.createElement('img');
                    img.src = src;
                    img.className = 'w-full h-full object-cover';
                    img.alt = file.name;

                    const overlay = document.createElement('div');
                    overlay.className =
                        'absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity';

                    const label = document.createElement('span');
                    label.className = 'text-white text-xs font-semibold px-2 text-center line-clamp-2';
                    label.textContent = file.name;

                    overlay.appendChild(label);
                    div.appendChild(img);
                    div.appendChild(overlay);
                    previewArea.appendChild(div);
                };

                const handleFiles = (files) => {
                    const selectedFiles = Array.from(files);
                    const errors = validateFiles(selectedFiles);

                    if (errors.length > 0) {
                        showGalleryErrors(errors);
                        previewArea.innerHTML = '';
                        previewArea.classList.add('hidden');
                        fileInput.value = '';
                        return false;
                    }

                    clearGalleryErrors();
                    previewArea.innerHTML = '';

                    if (selectedFiles.length === 0) {
                        previewArea.classList.add('hidden');
                        return true;
                    }

                    previewArea.classList.remove('hidden');

                    selectedFiles.forEach((file) => {
                        const reader = new FileReader();
                        reader.onload = (e) => appendPreview(file, e.target.result);
                        reader.readAsDataURL(file);
                    });

                    return true;
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
                    const files = e.dataTransfer.files;

                    if (!handleFiles(files)) {
                        return;
                    }

                    // Assign files to the native input
                    const dataTransfer = new DataTransfer();
                    Array.from(files).forEach(file => dataTransfer.items.add(file));
                    fileInput.files = dataTransfer.files;
                }
            }

            // YouTube URL Normalizer & Live Preview Helper
            function extractYouTubeVideoId(url) {
                if (!url) return null;
                url = url.trim();
                if (!/^https?:\/\//i.test(url)) {
                    url = 'https://' + url;
                }
                try {
                    const parsed = new URL(url);
                    const host = parsed.hostname.toLowerCase();
                    if (host === 'youtu.be' || host.endsWith('.youtu.be')) {
                        const id = parsed.pathname.slice(1).split('/')[0];
                        return /^[a-zA-Z0-9_-]{11}$/.test(id) ? id : null;
                    }
                    if (host.includes('youtube.com')) {
                        const path = parsed.pathname;
                        const match = path.match(/^\/(?:shorts|embed|v|live)\/([a-zA-Z0-9_-]{11})/i);
                        if (match) return match[1];
                        const v = parsed.searchParams.get('v');
                        if (v && /^[a-zA-Z0-9_-]{11}$/.test(v)) return v;
                    }
                } catch(e) {}
                return null;
            }

            function updateYouTubePreview(input) {
                const group = input.closest('.video-input-group');
                if (!group) return;
                const previewCard = group.querySelector('.youtube-preview-card');
                if (!previewCard) return;

                let val = input.value.trim();
                if (!val) {
                    previewCard.classList.add('hidden');
                    previewCard.innerHTML = '';
                    return;
                }

                const videoId = extractYouTubeVideoId(val);
                if (videoId) {
                    previewCard.innerHTML = `
                        <img src="https://img.youtube.com/vi/${videoId}/hqdefault.jpg" class="w-20 h-12 object-cover rounded border border-default-200 shrink-0" alt="Video Thumbnail" onerror="this.src='https://img.youtube.com/vi/${videoId}/default.jpg'">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 font-medium text-success text-xs">
                                <i data-lucide="check-circle" class="size-3.5"></i>
                                <span>URL YouTube Terverifikasi</span>
                            </div>
                            <a href="https://www.youtube.com/watch?v=${videoId}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-primary hover:underline flex items-center gap-1 mt-0.5 truncate">
                                Buka Video di YouTube <i data-lucide="external-link" class="size-3"></i>
                            </a>
                        </div>
                    `;
                    previewCard.classList.remove('hidden');
                } else {
                    previewCard.innerHTML = `
                        <div class="flex items-center gap-1.5 text-danger font-medium text-xs">
                            <i data-lucide="alert-circle" class="size-4 shrink-0"></i>
                            <span>Format URL YouTube tidak valid. Gunakan format youtube.com/watch?v=... atau youtu.be/...</span>
                        </div>
                    `;
                    previewCard.classList.remove('hidden');
                }

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({ root: previewCard });
                }
            }

            // Render Persisted Temporary Images from previous validation failure
            const persistedContainer = document.getElementById('persisted-temp-images-container');
            const persistedInputs = persistedContainer ? Array.from(persistedContainer.querySelectorAll('input[name="temp_gallery_images[]"]')) : [];
            if (persistedInputs.length > 0 && previewArea) {
                previewArea.classList.remove('hidden');
                persistedInputs.forEach((input) => {
                    const tempPath = input.value;
                    const div = document.createElement('div');
                    div.className = 'relative rounded overflow-hidden aspect-square border-2 border-primary/40 shadow-sm bg-default-100 group';
                    div.innerHTML = `
                        <img src="/storage/${tempPath}" class="w-full h-full object-cover" alt="Uploaded Photo" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'40\\' height=\\'40\\' viewBox=\\'0 0 24 24\\' fill=\\'none\\' stroke=\\'%23888\\' stroke-width=\\'2\\'><rect width=\\'18\\' height=\\'18\\' x=\\'3\\' y=\\'3\\' rx=\\'2\\'/><circle cx=\\'9\\' cy=\\'9\\' r=\\'2\\'/><path d=\\'m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21\\'/></svg>'">
                        <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-primary text-white shadow">Uploaded</span>
                        <button type="button" class="remove-persisted-temp-btn absolute top-1.5 right-1.5 size-6 rounded-full bg-danger text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer shadow hover:bg-danger/80" title="Remove photo">
                            <i data-lucide="x" class="size-3.5"></i>
                        </button>
                    `;
                    div.querySelector('.remove-persisted-temp-btn').addEventListener('click', () => {
                        input.remove();
                        div.remove();
                        if (previewArea.children.length === 0) {
                            previewArea.classList.add('hidden');
                        }
                    });
                    previewArea.appendChild(div);
                });
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({ root: previewArea });
                }
            }

            // Dynamic Video Links Repeater
            const videoContainer = document.getElementById('video-inputs-container');
            const addVideoBtn = document.getElementById('add-video-btn');

            if (addVideoBtn && videoContainer) {
                const updateRemoveButtons = () => {
                    const groups = videoContainer.querySelectorAll('.video-input-group');
                    groups.forEach(g => {
                        const btn = g.querySelector('.remove-video-row-btn');
                        const val = g.querySelector('input').value;
                        if (groups.length === 1 && !val) {
                            btn.classList.add('hidden');
                        } else {
                            btn.classList.remove('hidden');
                        }
                    });
                };

                addVideoBtn.addEventListener('click', () => {
                    const groups = videoContainer.querySelectorAll('.video-input-group');
                    const persistedTempCount = document.querySelectorAll('input[name="temp_gallery_images[]"]').length;
                    const galleryFilesCount = fileInput && fileInput.files ? fileInput.files.length : 0;
                    if (groups.length + persistedTempCount + galleryFilesCount >= 10) {
                        alert('Combined gallery quota is maximum 10 items (photos and videos combined).');
                        return;
                    }

                    const newGroup = document.createElement('div');
                    newGroup.className = 'video-input-group space-y-2 p-3 rounded-lg border border-default-200 bg-default-50/50';
                    newGroup.innerHTML = `
                        <div class="video-input-row flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-default-400">
                                    <i class="size-4 text-danger" data-lucide="youtube"></i>
                                </span>
                                <input type="url" name="gallery_videos[]" value="" placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..." class="form-input youtube-url-input pl-9 text-sm">
                            </div>
                            <button type="button" class="remove-video-row-btn p-2 text-default-400 hover:text-danger rounded border border-default-200 hover:border-danger/30 transition-colors" title="Remove link">
                                <i class="size-4" data-lucide="trash-2"></i>
                            </button>
                        </div>
                        <div class="youtube-preview-card hidden rounded-lg border border-default-200 bg-card p-2.5 text-xs flex items-center gap-3"></div>
                    `;
                    videoContainer.appendChild(newGroup);

                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                        window.lucide.createIcons({ root: newGroup });
                    }
                    updateRemoveButtons();
                });

                videoContainer.addEventListener('click', (e) => {
                    const removeBtn = e.target.closest('.remove-video-row-btn');
                    if (removeBtn) {
                        const group = removeBtn.closest('.video-input-group');
                        if (videoContainer.querySelectorAll('.video-input-group').length > 1) {
                            group.remove();
                        } else {
                            const input = group.querySelector('input');
                            input.value = '';
                            updateYouTubePreview(input);
                        }
                        updateRemoveButtons();
                    }
                });

                videoContainer.addEventListener('input', (e) => {
                    if (e.target.matches('input[name="gallery_videos[]"]')) {
                        updateRemoveButtons();
                        updateYouTubePreview(e.target);
                    }
                });

                videoContainer.addEventListener('blur', (e) => {
                    if (e.target.matches('input[name="gallery_videos[]"]')) {
                        let val = e.target.value.trim();
                        if (val && !/^https?:\/\//i.test(val) && (val.includes('youtube.com') || val.includes('youtu.be'))) {
                            e.target.value = 'https://' + val;
                        }
                        updateYouTubePreview(e.target);
                    }
                }, true);

                // Initial preview render for existing values
                videoContainer.querySelectorAll('input[name="gallery_videos[]"]').forEach(input => {
                    if (input.value.trim()) {
                        updateYouTubePreview(input);
                    }
                });
            }

            // Staged Asynchronous Gallery Upload Orchestration
            const projectForm = document.getElementById('project-form');
            const uploadModal = document.getElementById('upload-progress-modal');
            const modalProgressBar = document.getElementById('modal-progress-bar');
            const modalPercentageBadge = document.getElementById('modal-percentage-badge');
            const modalStatusText = document.getElementById('modal-status-text');
            const modalCountText = document.getElementById('modal-count-text');
            const modalActiveFileName = document.getElementById('modal-active-file-name');
            const modalActiveFileStatus = document.getElementById('modal-active-file-status');
            const modalErrorPanel = document.getElementById('modal-error-panel');
            const modalErrorMessage = document.getElementById('modal-error-message');
            const modalRetryBtn = document.getElementById('modal-retry-btn');
            const modalSkipBtn = document.getElementById('modal-skip-btn');

            let isStagedUploading = false;

            if (projectForm) {
                projectForm.addEventListener('submit', function(e) {
                    // Prevent double submission if upload is in progress
                    if (isStagedUploading) {
                        e.preventDefault();
                        return;
                    }

                    if (!projectForm.checkValidity()) {
                        projectForm.reportValidity();
                        return;
                    }

                    // Holistic Quota Check before initiating uploads
                    const persistedTempCount = document.querySelectorAll('input[name="temp_gallery_images[]"]').length;
                    const galleryFiles = fileInput && fileInput.files ? Array.from(fileInput.files) : [];
                    const activeVideoLinks = Array.from(document.querySelectorAll('input[name="gallery_videos[]"]'))
                        .filter(input => input.value.trim() !== '');

                    const totalItems = persistedTempCount + galleryFiles.length + activeVideoLinks.length;
                    if (totalItems > 10) {
                        e.preventDefault();
                        alert(`Total gallery quota is max 10 items (photos and videos combined). Currently proposed: ${totalItems} items (${persistedTempCount + galleryFiles.length} photos and ${activeVideoLinks.length} videos). Please reduce items.`);
                        return;
                    }

                    if (galleryFiles.length === 0) {
                        const saveBtn = document.getElementById('save-project-btn');
                        if (saveBtn) {
                            saveBtn.disabled = true;
                            saveBtn.classList.add('opacity-70', 'cursor-not-allowed');
                        }
                        return; // Proceed with native form submit
                    }

                    e.preventDefault();
                    isStagedUploading = true;
                    const saveBtn = document.getElementById('save-project-btn');
                    if (saveBtn) {
                        saveBtn.disabled = true;
                        saveBtn.classList.add('opacity-70', 'cursor-not-allowed');
                    }

                    if (uploadModal) {
                        uploadModal.classList.remove('hidden');
                        if (window.lucide && typeof window.lucide.createIcons === 'function') {
                            window.lucide.createIcons({ root: uploadModal });
                        }
                    }

                    let currentIndex = 0;
                    const totalFiles = galleryFiles.length;
                    const tempPaths = [];

                    function uploadNextFile() {
                        if (currentIndex >= totalFiles) {
                            if (modalStatusText) modalStatusText.textContent = 'Menyimpan data proyek...';
                            if (modalProgressBar) modalProgressBar.style.width = '100%';
                            if (modalPercentageBadge) modalPercentageBadge.textContent = '100%';

                            tempPaths.forEach(p => {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = 'temp_gallery_images[]';
                                hidden.value = p;
                                projectForm.appendChild(hidden);
                            });

                            fileInput.value = '';
                            projectForm.submit();
                            return;
                        }

                        const file = galleryFiles[currentIndex];
                        if (modalCountText) modalCountText.textContent = `${currentIndex + 1} / ${totalFiles}`;
                        if (modalActiveFileName) modalActiveFileName.textContent = file.name;
                        if (modalActiveFileStatus) modalActiveFileStatus.textContent = '0%';
                        if (modalStatusText) modalStatusText.textContent = `Mengunggah foto (${currentIndex + 1} dari ${totalFiles})`;
                        if (modalErrorPanel) modalErrorPanel.classList.add('hidden');

                        const overallPercentBefore = Math.round((currentIndex / totalFiles) * 100);
                        if (modalProgressBar) modalProgressBar.style.width = `${overallPercentBefore}%`;
                        if (modalPercentageBadge) modalPercentageBadge.textContent = `${overallPercentBefore}%`;

                        const formData = new FormData();
                        formData.append('file', file);
                        const tokenEl = document.querySelector('input[name="_token"]');
                        if (tokenEl) formData.append('_token', tokenEl.value);

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route("projects.upload-temp-gallery") }}', true);
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.timeout = 60000;

                        xhr.upload.onprogress = function(event) {
                            if (event.lengthComputable) {
                                const filePercent = Math.round((event.loaded / event.total) * 100);
                                if (modalActiveFileStatus) modalActiveFileStatus.textContent = `${filePercent}%`;
                                const totalPercent = Math.round(((currentIndex + (event.loaded / event.total)) / totalFiles) * 100);
                                if (modalProgressBar) modalProgressBar.style.width = `${totalPercent}%`;
                                if (modalPercentageBadge) modalPercentageBadge.textContent = `${totalPercent}%`;
                            }
                        };

                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                try {
                                    const res = JSON.parse(xhr.responseText);
                                    if (res.status === 'success' && res.temp_path) {
                                        tempPaths.push(res.temp_path);
                                        currentIndex++;
                                        uploadNextFile();
                                        return;
                                    }
                                } catch(err) {}
                            }
                            handleUploadError(xhr);
                        };

                        xhr.onerror = function() {
                            handleUploadError(xhr);
                        };

                        xhr.ontimeout = function() {
                            handleUploadError({ status: 408, responseText: JSON.stringify({ message: `Waktu unggah foto ${file.name} habis (timeout). Periksa koneksi internet Anda.` }) });
                        };

                        function handleUploadError(xhr) {
                            let msg = `Gagal mengunggah ${file.name}. Silakan coba lagi.`;
                            if (xhr.status === 419) {
                                msg = 'Sesi Anda telah kedaluwarsa (CSRF token expired). Silakan muat ulang halaman.';
                            } else {
                                try {
                                    const res = JSON.parse(xhr.responseText);
                                    if (res.message) msg = res.message;
                                } catch(err) {}
                            }

                            if (modalErrorMessage) modalErrorMessage.textContent = msg;
                            if (modalErrorPanel) modalErrorPanel.classList.remove('hidden');
                            if (modalActiveFileStatus) modalActiveFileStatus.textContent = 'Gagal';
                            if (window.lucide && typeof window.lucide.createIcons === 'function' && modalErrorPanel) {
                                window.lucide.createIcons({ root: modalErrorPanel });
                            }

                            if (modalRetryBtn) {
                                if (xhr.status === 419) {
                                    modalRetryBtn.classList.add('hidden');
                                } else {
                                    modalRetryBtn.classList.remove('hidden');
                                    modalRetryBtn.onclick = function() {
                                        uploadNextFile();
                                    };
                                }
                            }

                            if (modalSkipBtn) {
                                modalSkipBtn.onclick = function() {
                                    currentIndex++;
                                    uploadNextFile();
                                };
                            }
                        }

                        xhr.send(formData);
                    }

                    uploadNextFile();
                });
            }

            // Auto-scroll to first invalid element if validation failed
            @if ($errors->any())
                const firstInvalidField = document.querySelector('.has-error input, .has-error textarea, .has-error select, input:invalid, .border-danger, [aria-invalid="true"]');
                if (firstInvalidField) {
                    setTimeout(() => {
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                    }, 150);
                } else {
                    const errorBanner = document.getElementById('global-error-banner');
                    if (errorBanner) {
                        errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            @endif

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

            // Property Promotion & Conditional ROI Logic
            const isPromotionCheckbox = document.getElementById('is_for_sale_or_rent');
            const promotionContainer = document.getElementById('promotion-fields-container');
            const propertyTypeSelect = document.getElementById('property_type');
            const roiWrapper = document.getElementById('roi-estimation-wrapper');
            const roiInput = document.getElementById('roi_estimation');
            const kosInfoCallout = document.getElementById('kos-info-callout');

            function handlePropertyTypeChange() {
                if (!isPromotionCheckbox || !isPromotionCheckbox.checked) {
                    if (roiWrapper) roiWrapper.classList.add('hidden');
                    if (kosInfoCallout) kosInfoCallout.classList.add('hidden');
                    if (roiInput) roiInput.removeAttribute('required');
                    return;
                }

                const selectedType = propertyTypeSelect ? propertyTypeSelect.value : '';

                if (selectedType === 'Sale' || selectedType === 'Investment') {
                    if (roiWrapper) roiWrapper.classList.remove('hidden');
                    if (kosInfoCallout) kosInfoCallout.classList.add('hidden');
                    if (roiInput) roiInput.setAttribute('required', 'required');
                } else if (selectedType === 'Rent') {
                    if (roiWrapper) roiWrapper.classList.add('hidden');
                    if (kosInfoCallout) kosInfoCallout.classList.remove('hidden');
                    if (roiInput) {
                        roiInput.removeAttribute('required');
                    }
                } else {
                    if (roiWrapper) roiWrapper.classList.add('hidden');
                    if (kosInfoCallout) kosInfoCallout.classList.add('hidden');
                    if (roiInput) roiInput.removeAttribute('required');
                }
            }

            function handlePromotionToggle() {
                if (!isPromotionCheckbox || !promotionContainer) return;

                if (isPromotionCheckbox.checked) {
                    promotionContainer.classList.remove('hidden');
                    if (propertyTypeSelect) propertyTypeSelect.setAttribute('required', 'required');
                    handlePropertyTypeChange();
                } else {
                    promotionContainer.classList.add('hidden');
                    if (propertyTypeSelect) propertyTypeSelect.removeAttribute('required');
                    if (roiInput) roiInput.removeAttribute('required');
                    if (roiWrapper) roiWrapper.classList.add('hidden');
                    if (kosInfoCallout) kosInfoCallout.classList.add('hidden');
                }
            }

            if (isPromotionCheckbox) {
                isPromotionCheckbox.addEventListener('change', handlePromotionToggle);
            }

            if (propertyTypeSelect) {
                propertyTypeSelect.addEventListener('change', handlePropertyTypeChange);
            }

            // Initialize state on page load
            handlePromotionToggle();
        });
    </script>
@endsection


