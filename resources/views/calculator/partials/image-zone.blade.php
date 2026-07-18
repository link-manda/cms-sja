{{-- Reusable drag-n-drop upload zone. Params: $zone (2d|3d|proses), $label --}}
@php
    $inputName = 'images_'.$zone;
    $errorKey = $inputName.'.*';
@endphp
<div>
    <label class="block font-medium text-default-900 text-sm mb-2">{{ $label }}</label>
    <div id="dropzone-{{ $zone }}"
        class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-default-300 rounded-lg bg-default-50 hover:bg-default-100 transition-colors cursor-pointer group calc-dropzone"
        data-preview="preview-{{ $zone }}" data-errors="errors-{{ $zone }}">
        <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 calc-file-input"
            id="{{ $inputName }}" name="{{ $inputName }}[]" type="file"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple />
        <div class="flex flex-col items-center pointer-events-none">
            <i class="size-8 text-default-400 group-hover:text-primary transition-colors mb-2" data-lucide="upload-cloud"></i>
            <p class="text-xs font-medium text-default-700">Drag & drop or click to browse</p>
        </div>
    </div>
    <div id="preview-{{ $zone }}" class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-3 hidden"></div>
    <div id="errors-{{ $zone }}" class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-2 text-xs hidden"></div>

    @if ($errors->has($errorKey))
        <div class="mt-2 bg-danger/10 text-danger border border-danger/20 rounded p-2">
            @foreach ($errors->get($errorKey) as $messages)
                @foreach ($messages as $message)
                    <p class="text-xs flex items-center gap-1"><i class="size-3.5" data-lucide="alert-circle"></i> {{ $message }}</p>
                @endforeach
            @endforeach
        </div>
    @endif
</div>
