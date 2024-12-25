<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Partner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'cnic',
        'phone',
        'address',
    ];

    protected $appends = [
        'photo',
    ];

    public function sells(): HasMany
    {
        return $this->hasMany(Sell::class);
    }

    public function getPhotoAttribute()
    {
        $avatar = "https://ui-avatars.com/api/?name={$this->name}&background=0D8ABC&color=fff&font-size=0.25";

        return $this->getFirstMedia('partners') ?
            $this->getFirstMedia('partners')->getUrl('photo') :
            $avatar;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('photo')
            ->width('200')
            ->height('200');
    }
}
