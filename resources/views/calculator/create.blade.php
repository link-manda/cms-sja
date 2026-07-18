@extends('layouts.vertical', ['title' => 'Add Calculator Option'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Add Calculator Option'])

    <div class="card">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Add New Calculator Option</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('calculator.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="name">Option Name <span class="text-danger">*</span></label>
                    <input class="form-input" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Standard Package / Type 36" type="text" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="price_range">Price Range <span class="text-danger">*</span></label>
                    <input class="form-input" id="price_range" name="price_range" value="{{ old('price_range') }}" placeholder="e.g. IDR 150,000,000 - 250,000,000" type="text" required />
                    <p class="text-xs text-default-400 mt-1">This label is shown in the public dropdown.</p>
                    <x-input-error :messages="$errors->get('price_range')" class="mt-2" />
                </div>

                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="description">Description <span class="text-danger">*</span></label>
                    <textarea class="form-input min-h-[150px]" id="description" name="description" placeholder="Detailed explanation shown when this option is selected..." required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Image Zones -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-1 flex items-center gap-1">
                        <i class="size-4" data-lucide="image"></i> Project Visuals
                    </h6>
                    <p class="text-xs text-default-400 mb-4">Max 10 images total across all zones, up to 4MB each. Format: JPG, PNG, WEBP.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @include('calculator.partials.image-zone', ['zone' => '2d', 'label' => '2D Design'])
                        @include('calculator.partials.image-zone', ['zone' => '3d', 'label' => '3D Design'])
                        @include('calculator.partials.image-zone', ['zone' => 'proses', 'label' => 'Construction Process'])
                    </div>
                    @if ($errors->has('images_2d'))
                        <p class="text-sm text-danger mt-2 flex items-center gap-1"><i class="size-4" data-lucide="alert-circle"></i> {{ $errors->first('images_2d') }}</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                    <a href="{{ route('calculator.index') }}" class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">Cancel</a>
                    <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Option</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @include('calculator.partials.dropzone-script')
@endsection
