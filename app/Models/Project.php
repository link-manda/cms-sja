<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id',
    'title',
    'slug',
    'location',
    'description',
    'image',
    'status',
    'client',
    'year',
    'building_area',
    'land_area',
    'execution_team',
    'meta_title',
    'meta_description',
])]
class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the category that owns the project.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
