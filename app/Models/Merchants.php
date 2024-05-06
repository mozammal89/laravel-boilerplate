<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Merchants extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchant_title',
        'merchant_number',
        'merchant_logo',
        'merchant_hash',
        'merchant_qr',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getMerchantLogoAttribute($value)
    {
        return URL::to('/') . $value;
    }

    public function getMerchantQrAttribute($value)
    {
        return URL::to('/') . $value;
    }
}
