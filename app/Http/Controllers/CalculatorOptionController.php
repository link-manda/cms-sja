<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalculatorOptionRequest;
use App\Http\Requests\UpdateCalculatorOptionRequest;
use App\Models\CalculatorOption;
use App\Services\CalculatorOptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CalculatorOptionController extends Controller
{
    public function __construct(protected CalculatorOptionService $service) {}

    public function index(): View
    {
        $options = CalculatorOption::withCount('images')->latest()->paginate(10);

        return view('calculator.index', compact('options'));
    }

    public function create(): View
    {
        return view('calculator.create');
    }

    public function store(StoreCalculatorOptionRequest $request): RedirectResponse
    {
        $this->service->createOption($request->validated());

        return redirect()->route('calculator.index')->with('success', 'Calculator option created successfully!');
    }

    public function edit(CalculatorOption $calculator): View
    {
        $calculator->load('images');

        return view('calculator.edit', ['option' => $calculator]);
    }

    public function update(UpdateCalculatorOptionRequest $request, CalculatorOption $calculator): RedirectResponse
    {
        $this->service->updateOption($calculator, $request->validated());

        return redirect()->route('calculator.index')->with('success', 'Calculator option updated successfully!');
    }

    public function destroy(CalculatorOption $calculator): RedirectResponse
    {
        $this->service->forceDeleteOption($calculator);

        Log::channel('audit')->info('Calculator option deleted', [
            'user_id' => auth()->id(),
            'calculator_option_id' => $calculator->id,
            'name' => $calculator->name,
            'ip' => request()->ip(),
        ]);

        return redirect()->route('calculator.index')->with('success', 'Calculator option deleted permanently!');
    }

    public function deleteImage(CalculatorOption $calculator, int $image): RedirectResponse
    {
        if (! $this->service->deleteImage($calculator, $image)) {
            return back()->with('error', 'Failed to delete calculator image file.');
        }

        return back()->with('success', 'Image deleted successfully!');
    }
}
