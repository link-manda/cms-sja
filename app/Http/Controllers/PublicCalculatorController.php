<?php

namespace App\Http\Controllers;

use App\Models\CalculatorOption;
use Illuminate\View\View;

class PublicCalculatorController extends Controller
{
    /**
     * Halaman kalkulator publik single-page.
     * Seluruh opsi + gambar (dikelompokkan per type) di-bootstrap ke view;
     * interaksi dropdown ditangani vanilla JS tanpa reload.
     */
    public function index(): View
    {
        $options = CalculatorOption::with('images')->latest()->get()
            ->map(function (CalculatorOption $option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price_range' => $option->price_range,
                    'description' => $option->description,
                    'images' => [
                        '2d' => $this->imageUrls($option, '2d'),
                        '3d' => $this->imageUrls($option, '3d'),
                        'proses' => $this->imageUrls($option, 'proses'),
                    ],
                ];
            })->values();

        return view('public.calculator.index', compact('options'));
    }

    /**
     * @return array<string>
     */
    private function imageUrls(CalculatorOption $option, string $type): array
    {
        return $option->images
            ->where('type', $type)
            ->map(fn ($image) => asset('storage/'.$image->image_path))
            ->values()
            ->all();
    }
}
