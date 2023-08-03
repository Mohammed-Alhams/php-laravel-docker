<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacist_id',
        'card_number',
        'card_holder_name',
        'card_expiry_date',
        'card_cvv',
    ];

    public function pharmacist()
    {
        return $this->belongsTo(Pharmacist::class);
    }
}
