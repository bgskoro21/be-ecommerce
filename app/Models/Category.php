<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['name'] ?? false, function($query, $name){
            $query->where('name', 'ilike', '%'.$name.'%');
        });

        $query->when($filters['description'] ?? false, function($query, $description){
            $query->where('description', 'ilike', '%'.$description.'%');
        });
    }
}
