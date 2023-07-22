<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id', 'pharmacist_id', 'quantity', 'invoice_no', 'description',
    ];

    public function pharmacist(){
        return $this->belongsTo(Pharmacy::class);
    }

    public function stock(){
        return $this->belongsTo(Stock::class);
    }
}
