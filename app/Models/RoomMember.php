<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'name', 'id_card_number', 'phone'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
