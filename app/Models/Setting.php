<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Setting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'app_name',
        'phone',
        'email',
        'fax',
        'address',
    ];

    protected $appends = [
        'app_logo',
    ];

    public function getAppLogoAttribute()
    {
        return $this->getFirstMedia('settings') ?
            $this->getFirstMedia('settings')->getUrl() :
            NULL;
    }
}
