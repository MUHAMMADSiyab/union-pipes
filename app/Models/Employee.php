<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Employee extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'cnic',
        'phone',
        'join_date',
        'address',
        'salary',
    ];

    protected $appends = [
        'photo'
    ];

    public function getPhotoAttribute()
    {
        return $this->getFirstMedia('employees') ?
            $this->getFirstMedia('employees')->getUrl('photo') :
            NULL;
    }

    /**
     * Relations
     */


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('photo')
            ->width('200')
            ->height('200');
    }
}
