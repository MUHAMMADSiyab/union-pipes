<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'amount',
        'partner_id',
        'description',
    ];

    protected $with = [
        'payment',
    ];

    /**
     * Relations
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'paymentable_id')->where('model', PartnerTransaction::class);
    }
}
