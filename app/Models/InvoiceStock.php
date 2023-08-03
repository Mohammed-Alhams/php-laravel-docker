<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InvoiceStock extends Pivot
{
    use HasFactory;

    protected $table = 'invoice_stocks';

    public $incrementing = true;

    public $timestamps = false;

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Invoice::class);
    }
}
