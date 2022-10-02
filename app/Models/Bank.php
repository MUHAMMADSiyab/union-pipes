<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'account_no',
        'branch_name',
        'branch_code',
    ];

    /**
     * Relations
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
