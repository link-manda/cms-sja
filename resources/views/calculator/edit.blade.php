@extends('layouts.vertical', ['title' => 'Edit Calculator Option'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Edit Calculator Option'])

    @if (session('error'))
        <div class="mb-5 p-4 text-sm text-red-800 rounded bg-red-50 dark:bg-zinc-900 dark:text-red-400 border border-red-200 dark:border-red-800 flex items-center gap-2" role="alert">
            <i class="size-4" data-lucide="alert-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h6 class="card-title text-base font-semibold text-default-800">Edit Calculator Option</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('calculator.update', $option->id) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="name">Option Name <span class="text-danger">*</span></label>
                    <input class="form-input" id="name" name="name" value="{{ old('name', $option->name) }}" type="text" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="price_range">Price Range <span class="text-danger">*</span></label>
                    <input class="form-input" id="price_range" name="price_range" value="{{ old('price_range', $option->price_range) }}" type="text" required />
                    <p class="text-xs text-default-400 mt-1">This label is shown in the public dropdown.</p>
                    <x-input-error :messages="$errors->get('price_range')" class="mt-2" />
                </div>

                <div>
                    <label class="block font-medium text-default-900 text-sm mb-2" for="description">Description <span class="text-danger">*</span></label>
                    <textarea class="form-input min-h-[150px]" id="description" name="description" required>{{ old('description', $option->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Add More Images -->
                <div class="border-t border-default-200 pt-5">
                    <h6 class="text-sm font-semibold text-default-800 mb-1 flex items-center gap-1">
                        <i class="size-4" data-lucide="image-plus"></i> Add More Visuals
                    </h6>
                    <p class="text-xs text-default-400 mb-4">Newly uploaded images are added to existing ones. Max 10 total per upload, up to 4MB each.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @include('calculator.partials.image-zone', ['zone' => '2d', 'label' => '2D Design'])
                        @include('calculator.partials.image-zone', ['zone' => '3d', 'label' => '3D Design'])
                        @include('calculator.partials.image-zone', ['zone' => 'proses', 'label' => 'Construction Process'])
                    </div>
                    <x-input-error :messages="array_merge((array)$errors->get('images'), (array)$errors->get('images.*'), (array)$errors->get('image_zones'))" class="mt-3" />
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                    <a href="{{ route('calculator.index') }}" class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer">Cancel</a>
                    <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Gallery Management -->
    @if ($option->images->count() > 0)
        <div class="card mt-6">
            <div class="card-header">
                <h6 class="card-title text-base font-semibold text-default-800">Current Images</h6>
                <p class="text-sm text-default-500 mt-1">Deleting an image here removes it immediately.</p>
            </div>
            <div class="card-body space-y-6">
                @foreach (['2d' => '2D Design', '3d' => '3D Design', 'proses' => 'Construction Process'] as $type => $label)
                    @php $group = $option->images->where('type', $type); @endphp
                    @if ($group->count() > 0)
                        <div>
                            <h6 class="text-xs font-semibold text-default-500 uppercase tracking-wider mb-3">{{ $label }} ({{ $group->count() }})</h6>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach ($group as $image)
                                    <div class="relative group rounded-lg overflow-hidden border border-default-200 shadow-sm hover:shadow-md transition-all">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-32 object-cover" alt="{{ $label }}">
                                        <button type="button"
                                                class="dynamic-action-btn absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-danger/90 hover:bg-danger text-white p-1.5 rounded-full flex items-center justify-center shadow-lg"
                                                data-hs-overlay="#dynamic-action-modal"
                                                data-action-url="{{ route('calculator.image.delete', [$option->id, $image->id]) }}">
                                            <i class="size-4" data-lucide="x"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Dynamic Action Modal (image delete) -->
    <div id="dynamic-action-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="w-full flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-zinc-900 dark:border-zinc-800">
                <div class="p-6 overflow-y-auto text-center">
                    <div class="inline-flex justify-center items-center size-[62px] rounded-full border-4 border-danger/20 bg-danger/10 text-danger mb-4">
                        <i class="size-6" data-lucide="alert-triangle"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-default-800">Delete Image?</h3>
                    <p class="text-default-500 font-sans">This action cannot be undone.</p>
                    <form id="modal-action-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <div class="mt-8 flex justify-center gap-3">
                            <button type="button" class="btn bg-default-200 text-default-800 hover:bg-default-300 transition-colors" data-hs-overlay="#dynamic-action-modal">Cancel</button>
                            <button type="submit" class="btn bg-danger text-white transition-colors">Yes, Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('calculator.partials.dropzone-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalForm = document.getElementById('modal-action-form');
            document.querySelectorAll('.dynamic-action-btn').forEach(button => {
                button.addEventListener('click', function () {
                    modalForm.action = this.getAttribute('data-action-url');
                });
            });
        });
    </script>
@endsection
