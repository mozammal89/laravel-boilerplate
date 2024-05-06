<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCallbacks extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'payment_reference',
        'transaction_reference',
        'transaction_response',
        'currency_code',
        'amount',
        'success',
        'status_code',
        'received_data'
    ];
}
