<?php

namespace App\Http\Controllers\API\v1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Webxpay;
use App\Models\PaymentSettings;
use App\Models\TransactionRequest;
use App\Providers\AppFunctionProvider;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
        Cache::put('app_cfg_payment_data', $payment_settings);
    }

    public function payWithSavedCard(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'request_id' => 'required|string|exists:transaction_requests,request_id',
                'token' => 'required|string',
                'card_info' => 'required|array'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            $transaction_request = TransactionRequest::where('request_id', $validated_data['request_id'])->get()->first();

            if ($transaction_request->payment_status == 'Successful') {
                return response()->json([
                    'message' => 'This transaction has already been completed.'
                ], 400);
            }

            $user_instance = Auth::guard('api')->user();

            $webxpay = new Webxpay();

            $transaction_request->card_info = json_encode($request->input('card_info'));
            $transaction_request->card_type = $request->input('card_info')['card_type'] ?? null;
            $transaction_request->card_number_masked = $request->input('card_info')['card_number_masked'] ?? null;
            $transaction_request->card_expiry = $request->input('card_info')['card_expiry'] ?? null;
            $transaction_request->save();

            $pgw_response = $webxpay->initializePayment(
                request_id: $transaction_request->request_id,
                merchantNumber: Cache::get('app_cfg_payment_data', [])['merchant_number'],
                amount: $transaction_request->amount,
                bankMID: Cache::get('app_cfg_payment_data', [])['bank_mid'],
                customer_first_name: $user_instance->first_name,
                customer_last_name: $user_instance->last_name,
                customer_mobile_number: $user_instance->mobile,
                txnType: 'pay_only',
                card_token: $validated_data['token']
            );
            if ($pgw_response['status']) {
                $transaction_request->payment_status = 'Successful';
                $transaction_request->payment_completion_time = now();
                $transaction_request->payment_response_payload = $pgw_response['data'];
                $transaction_request->txn_reference = $pgw_response['data']['transaction']['txnReference'] ?? null;
                $transaction_request->save();

                $verification_data = $webxpay->verifyTransaction($transaction_request->request_id);
                if ($verification_data['status']) {
                    $verification_data = $verification_data['data'];
                    $transaction_request->transaction_verification_response = $verification_data;
                    $transaction_request->payment_reference = $verification_data['referenceNo'] ?? null;
                    $transaction_request->txn_reference = $verification_data['transaction']['txnReference'] ?? null;
                    $transaction_request->save();
                    if ($verification_data['success'] == $pgw_response['status'] && $verification_data['storeReference'] == $transaction_request->request_id) {
                        $transaction_request->is_transaction_verified = true;
                        $transaction_request->save();
                    }
                }

                AppFunctionProvider::paymentSuccessHandler($transaction_request->biller_id, $transaction_request);

                return response()->json([
                    'message' => 'Your payment has been completed successfully.',
                    'data' => [
                        'request_id' => $transaction_request->request_id,
                        'transaction_status' => $transaction_request->payment_status
                    ]
                ]);
            } else {
                $transaction_request->payment_status = 'Failed';
                $transaction_request->save();
                return response()->json([
                    'message' => $pgw_response['message']
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function payWithAnotherCard(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'request_id' => 'required|string|exists:transaction_requests,request_id'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            $transaction_request = TransactionRequest::where('request_id', $validated_data['request_id'])->get()->first();

            if ($transaction_request->payment_status == 'Successful') {
                return response()->json([
                    'message' => 'This transaction has already been completed.'
                ], 400);
            }

            $user_instance = Auth::guard('api')->user();

            // Init Payment Here
            $webxpay = new Webxpay();
            $saved_cards = [];
            $next_step = 'direct_payment';
            $request_id = Str::orderedUuid()->toString();
            $pgw_response = $webxpay->initializePayment(
                request_id: $request_id,
                merchantNumber: Cache::get('app_cfg_payment_data', [])['merchant_number'],
                amount: $transaction_request->amount,
                bankMID: Cache::get('app_cfg_payment_data', [])['bank_mid'],
                customer_first_name: $user_instance->first_name,
                customer_last_name: $user_instance->last_name,
                customer_mobile_number: $user_instance->mobile,
                txnType: 'save_another_and_pay'
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
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getTransactions(Request $request)
    {
        try {
            $transaction_request = TransactionRequest::where('user_id', Auth::guard('api')->user()->id)->where(function ($query) {
                $query->where('payment_status', 'Successful')
                    ->orWhere('payment_status', 'Failed');
            })->orderBy('updated_at', 'DESC')->get();

            return response()->json([
                'message' => 'Transaction list received successfully.',
                'data' => $transaction_request
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function addNewCard(Request $request)
    {
        try {
            $user_instance = Auth::guard('api')->user();

            $webxpay = new Webxpay();
            $pgw_response = $webxpay->initializePayment(
                request_id: Str::orderedUuid()->toString(),
                merchantNumber: Cache::get('app_cfg_payment_data', [])['merchant_number'],
                amount: 20,
                bankMID: Cache::get('app_cfg_payment_data', [])['bank_mid'],
                customer_first_name: $user_instance->first_name,
                customer_last_name: $user_instance->last_name,
                customer_mobile_number: $user_instance->mobile,
                txnType: 'save_another'
            );
            if ($pgw_response['status']) {
                return response()->json([
                    'message' => 'Add new card URL received successfully.',
                    'data' => [
                        'next_step' => 'direct_payment',
                        'request_id' => $pgw_response['data']['reference'],
                        'payment_url' => $pgw_response['data']['payment_url'],
                        'saved_cards' => [],
                        'success_url' => route('ipg.app.return', ['trx_status' => 'card-save-success']),
                        'fail_url' => route('ipg.app.return', ['trx_status' => 'card-save-failed'])
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => $pgw_response['message']
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getSavedCards(Request $request)
    {
        try {
            $webxpay = new Webxpay();
            $card_response = $webxpay->getCustomerCards(contact_number: Auth::guard('api')->user()->mobile);
            if ($card_response['status']) {
                $saved_cards = $card_response['data'];
            } else {
                $saved_cards = [];
            }
            return response()->json([
                'message' => 'Saved card list received successfully.',
                'data' => $saved_cards
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteSavedCard(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'token_id' => 'required|string',
                'token' => 'required|string'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            $webxpay = new Webxpay();
            $card_response = $webxpay->deleteCustomerCards(
                token_id: $validated_data['token_id'],
                token: $validated_data['token'],
                contact_number: Auth::guard('api')->user()->mobile);
            if ($card_response['status']) {
                return response()->json([
                    'message' => 'Card has been removed successfully.',
                    'data' => []
                ]);
            }
            return response()->json([
                'message' => 'Failed to remove saved card.',
                'data' => []
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getTransactionDetails(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'request_id' => 'required|string|exists:transaction_requests,request_id'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            $transaction_request = TransactionRequest::where('request_id', $validated_data['request_id'])->get()->first();

            return response()->json([
                'message' => 'Transaction details received successfully.',
                'data' => $transaction_request
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
