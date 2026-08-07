<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'image', 'is_active', 'cogs'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function addons()
    {
        return $this->hasMany(ProductAddon::class);
    }
    public function selectedAddons()
    {
        return $this->hasMany(OrderItemAddon::class);
    }
}
