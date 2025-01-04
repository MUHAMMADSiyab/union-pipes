<?php

namespace App\Models;

use Database\Factories\ProductionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'employee_id',
        'shift',
        'date',
        'product_id',
        'weight',
        'quantity',
        'total_weight',
        'description'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }


    protected static function newFactory()
    {
        return ProductionFactory::new();
    }
}
