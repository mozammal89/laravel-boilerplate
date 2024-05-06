<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'request_id',
        'service_identifier',
        'biller_id',
        'service_parameters',
        'service_status',
        'payment_status',
        'payment_url',
        'amount',
        'service_response_payload',
        'payment_response_payload',
        'card_info',
        'merchant_id',
        'is_merchant_payment'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'biller_id',
        'payment_url',
        'created_at',
        'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['payee_name'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function billers()
    {
        return $this->belongsTo(BillerList::class, 'biller_id');
    }

    public function merchants()
    {
        return $this->belongsTo(Merchants::class, 'merchant_id');
    }

    public function getServiceResponsePayloadAttribute($value)
    {
        if ($value != null) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getServiceParametersAttribute($value)
    {
        if ($value != null) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getPaymentResponsePayloadAttribute($value)
    {
        if ($value != null) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getCardInfoAttribute($value)
    {
        if ($value != null) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getTransactionVerificationResponseAttribute($value)
    {
        if ($value != null) {
            return json_decode($value, true);
        }
        return $value;
    }

    public function getPayeeNameAttribute()
    {
        if ($this->is_merchant_payment) {
            $payee = $this->merchants->merchant_title ?? "N/A";
        } else {
            $payee = $this->billers->biller_name ?? "N/A";
        }
        return $payee;
    }
}
