<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'month', 'year', 'total_amount', 'status', 'payment_date'];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
