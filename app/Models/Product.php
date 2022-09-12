<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\Category;


class Product extends Model
{
    use HasFactory;
    protected $table = "products";
    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
    public function category()
    {
        return  $this->belongsTo(Category::class, 'category_id', 'id');
    }

    function comments()
    {
        return $this->hasMany(Comments::class, 'product_id', 'id');
    }
}
