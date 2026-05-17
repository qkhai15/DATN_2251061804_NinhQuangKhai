<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'room_number', 'price', 'max_people', 'status', 'area'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function members()
    {
        return $this->hasMany(RoomMember::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }
}
