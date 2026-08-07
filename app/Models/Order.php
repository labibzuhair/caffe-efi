<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke Sesi Meja
    public function session()
    {
        return $this->belongsTo(TableSession::class, 'table_session_id');
    }

    // Relasi ke Detail Item Pesanan
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
