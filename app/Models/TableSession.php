<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{
    protected $fillable = ['table_id', 'status'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customers()
    {
        return $this->hasMany(SessionCustomer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
