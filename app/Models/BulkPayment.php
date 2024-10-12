<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BulkPayment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'type',
        'customer_id',
        'company_id',
        'amount',
        'date',
        'payment_method',
        'cheque_no',
        'cheque_type',
        'cheque_due_date',
        'bank_id',
        'description',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getChequeImagesAttribute()
    {
        $media = $this->getMedia('bulk_payments');
        $chequeImages = [];

        for ($i = 0; $i < count($media); $i++) {
            if (!is_null($media[0])) {
                array_push($chequeImages, $media[$i]->getUrl('cheque_image'));
            }
        }

        return $chequeImages;
    }
}
