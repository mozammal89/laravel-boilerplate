<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class BillerList extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'biller_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'domain_code',
        'wallet_alias',
        'biller_name',
        'biller_icon',
        'biller_category',
        'availability',
        'transaction_type',
        'biller_pin',
        'credentials',
        'use_group_credentials',
        'status',
        'sorting_index'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'biller_pin',
        'credentials',
        'use_group_credentials'
    ];

    public function biller_groups()
    {
        return $this->belongsTo(BillerGroup::class, 'group_id');
    }

    public function app_categories()
    {
        return $this->belongsTo(AppCategory::class, 'category_id');
    }

    public function getBillerIconAttribute($value)
    {
        return URL::to('/') . $value;
    }
}
