<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'quantity_by_units',
        'unit_price',
        'box_price',
        'box_wholesale_price',
        'unit_wholesale_price',
        'quantity_by_boxes',
        'expiration_date',
    ];

}
