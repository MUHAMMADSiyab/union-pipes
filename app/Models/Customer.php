<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'cnic',
        'phone',
        'address',
    ];

    protected $appends = [
        'photo'
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function getPhotoAttribute()
    {
        return $this->getFirstMedia('customers') ?
            $this->getFirstMedia('customers')->getUrl('photo') :
            NULL;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('photo')
            ->width('200')
            ->height('200');
    }
}
