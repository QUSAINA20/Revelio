<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Esp extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'lang', 'lat', 'battery_percentage', 'name', 'is_online', 'user_id',
    ];

    protected $casts = [

        'battery_percentage' => 'integer',
        'is_online' => 'boolean',
    ];
}
