<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Belong to Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    // Belong to Brand
     public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
