<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'price',
        'image',
        'description',
        'discount',
        'quantity',
        'unit',
        'weight',
        'is_visible',
        'is_approved',
        'extra_tags',
        'extra_images'
    ];

    protected $casts = [
        'price' => MoneyCast::class,
        'discount' => MoneyCast::class,
        'extra_tags' => 'array',
        'extra_images' => 'array'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
