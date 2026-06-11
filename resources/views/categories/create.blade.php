@extends('layouts.vertical', ['title' => 'Add Category'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Add Category'])

    <div class="card">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Add New Category</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Category Name -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="name">Category Name <span class="text-danger">*</span></label>
                        <input class="form-input" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Residential Construction" type="text" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block font-medium text-default-900 text-sm mb-2" for="slug">Slug (URL identifier) <span class="text-danger">*</span></label>
                        <input class="form-input" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. residential-construction" type="text" required />
                        <p class="text-xs text-default-400 mt-1">Generated automatically when typing the category name, must be unique.</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                    <a href="{{ route('categories.index') }}" class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">Cancel</a>
                    <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Category</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    let name = this.value;
                    let slug = name.toLowerCase()
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
