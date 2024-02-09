<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product_variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function product_galleries(): HasMany
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['name'] ?? false, function($query, $name){
            $query->where('name', 'ilike', '%'.$name.'%');
        });

        $query->when($filters['description'] ?? false, function($query, $description){
            $query->where('description', 'ilike', '%'.$description.'%');
        });

        $query->when($filters['price'] ?? false, function($query, $price){
            $query->where('price','=', $price);
        });

        $query->when($filters['sizeItem'] ?? false, function($query, $size){
            return $query->whereHas('product_variants', function($query) use ($size){
                $query->where('size', $size);
            });
        });
    }
}
