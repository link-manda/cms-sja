<?php

namespace App\Services;

use App\Models\CalculatorOption;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Service Class untuk logika bisnis CalculatorOption.
 * Mengikuti pola ProjectService ("Fat Models, Skinny Controllers").
 */
class CalculatorOptionService
{
    public function createOption(array $data): CalculatorOption
    {
        $images = $this->extractImages($data);

        return DB::transaction(function () use ($data, $images) {
            $option = CalculatorOption::create($data);
            $this->storeImages($option, $images);

            return $option;
        });
    }

    public function updateOption(CalculatorOption $option, array $data): bool
    {
        $images = $this->extractImages($data);

        return DB::transaction(function () use ($option, $data, $images) {
            $updated = $option->update($data);
            $this->storeImages($option, $images);

            return $updated;
        });
    }

    /**
     * Pasangkan file dengan zona berdasarkan urutan multipart, lalu buang
     * kedua field agar tidak ikut ter-mass-assign ke model.
     *
     * @return array<int, array{file: UploadedFile, zone: string}>
     */
    private function extractImages(array &$data): array
    {
        $files = $data['images'] ?? [];
        $zones = $data['image_zones'] ?? [];
        unset($data['images'], $data['image_zones']);

        return array_map(
            fn (UploadedFile $file, string $zone): array => compact('file', 'zone'),
            $files,
            $zones,
        );
    }

    /**
     * Simpan seluruh gambar dengan zona pasangannya. Rollback file yang sudah
     * tersimpan jika salah satu gagal.
     */
    private function storeImages(CalculatorOption $option, array $images): void
    {
        $storedPaths = [];

        try {
            foreach ($images as ['file' => $image, 'zone' => $zone]) {
                $filename = Str::random(40).'.'.$image->extension();
                $path = 'calculator/'.$filename;

                if (! $image->storeAs('calculator', $filename, 'public')) {
                    throw new RuntimeException('Failed to store calculator image.');
                }

                $storedPaths[] = $path;

                $option->images()->create([
                    'image_path' => $path,
                    'type' => $zone,
                ]);
            }
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }
    }

    public function deleteImage(CalculatorOption $option, int $imageId): bool
    {
        $image = $option->images()->findOrFail($imageId);

        if (Storage::disk('public')->exists($image->image_path) && ! Storage::disk('public')->delete($image->image_path)) {
            return false;
        }

        return (bool) $image->delete();
    }

    /**
     * Soft delete — jangan hapus file agar bisa di-restore.
     */
    public function deleteOption(CalculatorOption $option): ?bool
    {
        return $option->delete();
    }

    /**
     * Force delete — bersihkan file dari disk lalu hapus record permanen.
     * FK cascade menghapus record images di DB, tapi file disk harus manual.
     */
    public function forceDeleteOption(CalculatorOption $option): ?bool
    {
        foreach ($option->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        return $option->forceDelete();
    }
}
