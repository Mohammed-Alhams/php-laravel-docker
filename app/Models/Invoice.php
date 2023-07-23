<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id', 'pharmacist_id', 'quantity', 'invoice_no', 'description',
    ];

    public function pharmacist(){
        return $this->belongsTo(Stores::class);
    }

    public function stock(){
        return $this->belongsTo(Stock::class);
    }

    public function scopeFilter(Builder $builder, $filters)
    {

        $options = array_merge(
            [
                'invoice_no' => null,
            ], $filters
        );

        $builder->when($options['invoice_no'] ?? false, function($builder, $value) {
            $builder->where('invoice_no', '=', $value);
        });

    }
}
