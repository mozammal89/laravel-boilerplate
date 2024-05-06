<?php

namespace App\Http\Controllers\API\v1\Service;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Webxpay;
use App\Http\Controllers\ThirdParty\Dialog;
use App\Models\BillerList;
use App\Models\PaymentSettings;
use App\Models\TransactionRequest;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function __construct($identifier = null)
    {
        $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
        Cache::put('app_cfg_payment_data', $payment_settings);

        if ($identifier) {
            $this->biller_instance = BillerList::where('identifier', $identifier)->where('status', true)->first();
            if ($this->biller_instance->use_group_credentials) {
                $credentials = json_decode($this->biller_instance->biller_groups->credentials, true);
            } else {
                $credentials = json_decode($this->biller_instance->credentials, true);
            }
            $service_parameters = [
                'wallet_alias' => $this->biller_instance->wallet_alias,
                'wallet_pin' => $this->biller_instance->biller_pin
            ];
            switch ($identifier) {
                case "recharge_dialog":
                    $this->service_provider = new Dialog($service_parameters, $credentials);
            }
        }
    }

    public function isServiceAvailable(string $identifier)
    {
        try {
            $this->__construct($identifier);
            $is_active = $this->service_provider->isServiceAvailable();
            return response()->json([
                'message' => $is_active ? 'Biller status received successfully.' : 'Biller service is currently unavailable. Please try again after sometime.',
                'data' => ['is_active' => $is_active]
            ], $is_active ? 200 : 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function createServiceRequest(Request $request, string $identifier)
    {
        try {
            $this->__construct($identifier);

            $response = $this->service_provider->validateServiceRequest($request);

            if (is_array($response)) {
                $transaction_request = TransactionRequest::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'request_id' => Str::orderedUuid()->toString(),
                    'service_identifier' => $identifier,
                    'biller_id' => $this->biller_instance->id,
                    'amount' => $response['data']['amount'],
                    'service_parameters' => json_encode($response['data']),
                    'payment_url' => ''
                ]);

                $user_instance = Auth::guard('api')->user();

                // Init Payment Here
                $webxpay = new Webxpay();
                $card_response = $webxpay->getCustomerCards(contact_number: Auth::guard('api')->user()->mobile);
                if ($card_response['status']) {
                    $next_step = 'saved_cards';
                    $saved_cards = $card_response['data'];
                } else {
                    $saved_cards = [];
                    $next_step = 'direct_payment';
                    $request_id = Str::orderedUuid()->toString();
                    $pgw_response = $webxpay->initializePayment(
                        request_id: $request_id,
                        merchantNumber: Cache::get('app_cfg_payment_data', [])['merchant_number'],
                        amount: $response['data']['amount'],
                        bankMID: Cache::get('app_cfg_payment_data', [])['bank_mid'],
                        customer_first_name: $user_instance->first_name,
                        customer_last_name: $user_instance->last_name,
                        customer_mobile_number: $user_instance->mobile
                    );
                    if ($pgw_response['status']) {
                        $transaction_request->request_id = $request_id;
                        $transaction_request->payment_reference = $pgw_response['data']['reference'];
                        $transaction_request->payment_url = $pgw_response['data']['payment_url'];
                        $transaction_request->save();
                    } else {
                        return response()->json([
                            'message' => $pgw_response['message']
                        ], 400);
                    }
                }

                return response()->json([
                    'message' => 'Service request received and payment url has been generated.',
                    'data' => [
                        'next_step' => $next_step,
                        'request_id' => $transaction_request->request_id,
                        'payment_url' => $transaction_request->payment_url,
                        'saved_cards' => $saved_cards,
                        'success_url' => route('ipg.app.return', ['trx_status' => 'success']),
                        'fail_url' => route('ipg.app.return', ['trx_status' => 'failed'])
                    ]
                ]);
            }

            return $response;
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function activateService($transaction_request)
    {
        try {
            $this->__construct($transaction_request->service_identifier);

            $status = $this->service_provider->activateService($transaction_request);

            return [
                'status' => $status,
                'message' => "Activate service action completed."
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
