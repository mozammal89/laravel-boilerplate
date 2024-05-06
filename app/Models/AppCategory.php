<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class AppCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icon',
        'status',
        'sorting_index'
    ];

    public function billers()
    {
        return $this->hasMany(BillerList::class, 'category_id')->orderBy('sorting_index', 'ASC');
    }

    public function getIconAttribute($value)
    {
        return URL::to('/') . $value;
    }
}
