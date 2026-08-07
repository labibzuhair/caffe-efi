<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Membuka gembok keamanan mass-assignment untuk semua kolom di tabel ini
    protected $guarded = [];
}
