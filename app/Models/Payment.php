<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Payment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public $timestamps = false;

    protected $fillable = [
        'amount',
        'additional_amount',
        'deducted_amount',
        'model',
        'paymentable_id',
        'payment_method',
        'cheque_no',
        'cheque_type',
        'cheque_due_date',
        'payment_date',
        'transaction_type',
        'bank_id',
        'first_payment',
        'description',
        'cheque_cleared_at',
        'bulk_payment_id',
    ];

    protected $with = [
        'bank'
    ];

    protected $appends = [
        'cheque_images',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('cheque_image')
            ->width('500')
            ->height('500');
    }

    // public function purchase()
    // {
    //     return $this->belongsTo(Purchase::class, 'paymentable_id');
    // }

    // public function sell()
    // {
    //     return $this->belongsTo(Sell::class, 'paymentable_id');
    // }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'paymentable_id')
            ->when($this->model === Purchase::class, function ($query) {
                return $query;
            });
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'paymentable_id')
            ->when($this->model === Sell::class, function ($query) {
                return $query;
            });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'paymentable_id')
            ->when($this->model === Transaction::class, function ($query) {
                return $query;
            });
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'paymentable_id')
            ->when($this->model === Expense::class, function ($query) {
                return $query;
            });
    }

    public function partner_transaction()
    {
        return $this->belongsTo(PartnerTransaction::class, 'paymentable_id')
            ->when($this->model === PartnerTransaction::class, function ($query) {
                return $query;
            });
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'paymentable_id')
            ->when($this->model === Salary::class, function ($query) {
                return $query;
            });
    }

    public function bulk_payment()
    {
        return $this->belongsTo(BulkPayment::class);
    }


    public function getChequeImagesAttribute()
    {
        $media = $this->getMedia('payments');
        $chequeImages = [];

        for ($i = 0; $i < count($media); $i++) {
            if (!is_null($media[0])) {
                array_push($chequeImages, $media[$i]->getUrl('cheque_image'));
            }
        }

        return $chequeImages;
    }

    /**
     * Relations
     */

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
