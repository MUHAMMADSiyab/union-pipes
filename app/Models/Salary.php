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
        'balance',
        'additional_amount',
        'deducted_amount',
        'employee_id',
    ];


    protected $appends = [
        'status',
        'month_formatted'
    ];

    public function getMonthFormattedAttribute()
    {
        return date('M Y', strtotime($this->month));
    }

    public function getStatusAttribute()
    {
        $status = "";

        if ($this->balance == 0 || $this->balance < 0) {
            $status = ($this->balance < 0) ? "Advance" : "Paid";
        } elseif ($this->total_paid > 0 && $this->balance > 0) {
            $status = "Partial";
        } elseif ($this->total_paid == 0.0 || $this->balance == $this->salary) {
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
