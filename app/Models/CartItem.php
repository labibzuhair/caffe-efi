<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Mengubah JSON string menjadi Array otomatis
    protected $casts = [
        'addons' => 'array',
    ];

    public function tableSession()
    {
        return $this->belongsTo(TableSession::class);
    }

    public function customer()
    {
        return $this->belongsTo(SessionCustomer::class, 'session_customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
