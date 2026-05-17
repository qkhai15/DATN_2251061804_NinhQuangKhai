<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'type', 'old_value', 'new_value', 'image_path', 'ai_confidence', 'read_date'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
