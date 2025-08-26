<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip',
        'referred_chapter_name',
        'referred_by',
        'uuid',
        'membership_number',
        'amount',
        'payment_status',
        'stripe_payment_id',
        'qr_code',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            $member->membership_number = 'SRRR' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);

        });
    }
}
