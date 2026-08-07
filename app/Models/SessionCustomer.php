<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SessionCustomer extends Model
{
    protected $fillable = ['table_session_id', 'display_name', 'device_identifier', 'is_host'];

    public function tableSession()
    {
        return $this->belongsTo(TableSession::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
