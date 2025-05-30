<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'per_kg_price',
        'per_unit_weight',
        'size',
        'type',
    ];

    protected $appends = [
        'product_full_name'
    ];

    public function getProductFullNameAttribute()
    {
        $size = empty($this->size) ?  '' : " - " . number_format($this->size, 2);
        return $this->name . " - " . $this->type . $size;
    }
}
