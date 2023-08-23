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
            ->width('200')
            ->height('200');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'paymentable_id');
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'paymentable_id');
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
