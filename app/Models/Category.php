<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'subdomain_id',
        'name',
        'slug',
        'is_visible'
    ];

    public function scopeSubdomain(Builder $query): void 
    {
        $query->where('subdomain_id', auth()->user()->subdomain->id);
    }

    public function subdomain()
    {
        return $this->belongsTo(Subdomain::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
