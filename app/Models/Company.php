<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Company extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $appends = [
        'logo'
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('logo')
            ->width('200')
            ->height('200');
    }

    public function getLogoAttribute()
    {
        return $this->getFirstMedia('companies') ?
            $this->getFirstMedia('companies')->getUrl('logo') :
            NULL;
    }
}
