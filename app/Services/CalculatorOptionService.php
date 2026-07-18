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
    /**
     * Mapping key input form ke nilai kolom `type` pada calculator_images.
     */
    private const IMAGE_ZONES = [
        'images_2d' => '2d',
        'images_3d' => '3d',
        'images_proses' => 'proses',
    ];

    public function createOption(array $data): CalculatorOption
    {
        $zones = $this->extractZones($data);

        return DB::transaction(function () use ($data, $zones) {
            $option = CalculatorOption::create($data);
            $this->storeImages($option, $zones);

            return $option;
        });
    }

    public function updateOption(CalculatorOption $option, array $data): bool
    {
        $zones = $this->extractZones($data);

        return DB::transaction(function () use ($option, $data, $zones) {
            $updated = $option->update($data);
            $this->storeImages($option, $zones);

            return $updated;
        });
    }

    /**
     * Pisahkan file upload per-zona dari payload data, sekalian buang key-nya
     * agar tidak ikut ter-mass-assign ke model.
     *
     * @return array<string, array<UploadedFile>>
     */
    private function extractZones(array &$data): array
    {
        $zones = [];

        foreach (array_keys(self::IMAGE_ZONES) as $key) {
            $zones[$key] = $data[$key] ?? [];
            unset($data[$key]);
        }

        return $zones;
    }

    /**
     * Simpan seluruh gambar dari tiap zona dengan menandai kolom `type`.
     * Rollback file yang sudah tersimpan jika salah satu gagal.
     */
    private function storeImages(CalculatorOption $option, array $zones): void
    {
        $storedPaths = [];

        try {
            foreach ($zones as $key => $images) {
                $type = self::IMAGE_ZONES[$key];

                foreach ($images as $image) {
                    if (! $image instanceof UploadedFile) {
                        continue;
                    }

                    $filename = Str::random(40).'.'.$image->extension();
                    $path = 'calculator/'.$filename;

                    if (! $image->storeAs('calculator', $filename, 'public')) {
                        throw new RuntimeException('Failed to store calculator image.');
                    }

                    $storedPaths[] = $path;

                    $option->images()->create([
                        'image_path' => $path,
                        'type' => $type,
                    ]);
                }
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
