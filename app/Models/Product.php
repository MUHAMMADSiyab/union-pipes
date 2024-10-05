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
        'size',
        'type',
    ];

    protected $appends = [
        'product_full_name'
    ];

    public function getProductFullNameAttribute()
    {
        $size = empty($this->size) ?  '' : " - " . $this->size;
        return $this->name . " - " . $this->type . $size;
    }
}
