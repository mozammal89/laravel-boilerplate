<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class AppBanners extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'banner_image',
        'sorting_index',
        'status'
    ];

    public function getBannerImageAttribute($value)
    {
        return URL::to('/') . $value;
    }
}
