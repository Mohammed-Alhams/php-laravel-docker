<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id',
        'quantity_by_units',
        'unit_price',
        'box_price',
        'box_wholesale_price',
        'unit_wholesale_price',
        'quantity_by_boxes',
        'expiration_date',
        'pharmacy_id'
    ];

    public function store()
    {
        return $this->belongsTo(Stores::class);
    }


    public function scopeFilter(Builder $builder, $filters)
    {

        $options = array_merge(
            [
                'pharmacy_id' => null,
                'name' => null,
                'barcode' => null,
            ], $filters
        );

        $builder->when($options['name'] ?? false, function($builder, $value) {
            $builder->where('name', 'LIKE', "%{$value}%");
        });

        $builder->when($options['pharmacy_id'] ?? false, function($builder, $value) {
            $builder->where('pharmacy_id', '=', $value);
        });

        $builder->when($options['barcode'] ?? false, function($builder, $value) {
            $builder->where('barcode', '=', $value);
        });

    }
}
