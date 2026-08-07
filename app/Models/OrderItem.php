<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Pelanggan Pemilik Makanan (Split Bill)
    public function customer()
    {
        return $this->belongsTo(SessionCustomer::class, 'session_customer_id');
    }

    // KODE BARU YANG HARUS DITAMBAHKAN (Relasi ke Varian/Add-ons)
    public function selectedAddons()
    {
        return $this->hasMany(OrderItemAddon::class);
    }
}