<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id', 'pharmacist_id', 'quantity', 'invoice_no', 'description', 'total_price'
    ];

    public function pharmacist(){
        return $this->belongsTo(Stores::class);
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'invoice_stocks', 'invoice_id', 'stock_id', 'id', 'id');
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
