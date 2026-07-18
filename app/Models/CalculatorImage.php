<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['calculator_option_id', 'image_path', 'type'])]
class CalculatorImage extends Model
{
    use HasFactory;

    public function option(): BelongsTo
    {
        return $this->belongsTo(CalculatorOption::class, 'calculator_option_id');
    }
}
