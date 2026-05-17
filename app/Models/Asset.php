<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'name', 'quantity', 'condition'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
