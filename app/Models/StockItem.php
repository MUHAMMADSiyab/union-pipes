<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'available_quantity',
        'description',
    ];

    public function productions()
    {
        return $this->hasMany(Production::class);
    }
}
