<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'month',
        'date',
        'total_paid',
        'additional_amount',
        'deducted_amount',
        'status',
        'employee_id',
    ];


    protected $appends = [
        'balance',
        'month_formatted'
    ];

    public function getBalanceAttribute()
    {
        $basicPay = Employee::find($this->employee_id)->salary;
        return $basicPay - $this->total_paid;
    }

    public function getMonthFormattedAttribute()
    {
        return date('M Y', strtotime($this->month));
    }

    public function getStatusAttribute()
    {
        $status = "";

        if ($this->balance === 0.0 || $this->balance < 0) {
            $status = "Paid";
        } elseif ($this->total_paid > 0 && $this->balance > 0) {
            $status = "Partial";
        } elseif ($this->total_paid === 0.0 || $this->balance === $this->salary) {
            $status = "Unpaid";
        }


        return $status;
    }

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'paymentable_id')
            ->where('model', Salary::class);
    }
}
