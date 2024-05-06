<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Webxpay;
use App\Models\Merchants;
use App\Models\PaymentSettings;
use App\Models\TransactionRequest;
use App\Providers\AppFunctionProvider;
use App\Providers\ValidationServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MerchantController extends Controller
{
    public function __construct()
    {
        $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
        Cache::put('app_cfg_payment_data', $payment_settings);
    }

    /**
     * @throws ValidationException
     */
    public function getMerchantInformation(Request $request)
    {
        $validator = ValidationServiceProvider::validateRequestData($request, [
            'token' => 'required|string'
        ]);

        if (!$validator['is_valid']) {
            return $validator['response'];
        }

        $validated_data = $validator['validated_data'];

        $merchant_information = Merchants::where('merchant_hash', $validated_data['token'])->where('status', true)->get()->first();

        return response()->json([
            'message' => 'Merchant information received successfully.',
            'data' => $merchant_information
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function getMerchantQR(Request $request)
    {
        $validator = ValidationServiceProvider::validateRequestData($request, [
            'merchant_number' => 'required|string'
        ]);

        if (!$validator['is_valid']) {
            return $validator['response'];
        }

        $validated_data = $validator['validated_data'];

        $merchant_information = Merchants::where('merchant_number', $validated_data['merchant_number'])->where('status', true)->get()->first();

        return response()->json([
            'message' => 'Merchant QR received successfully.',
            'data' => $merchant_information
        ]);
    }

    public function payMerchantWithQR(Request $request)
    {
        $validator = ValidationServiceProvider::validateRequestData($request, [
            'token' => 'required|string',
            'amount' => 'required'
        ]);

        if (!$validator['is_valid']) {
            return $validator['response'];
        }

        $validated_data = $validator['validated_data'];

        $merchant_information = Merchants::where('merchant_hash', $validated_data['token'])->where('status', true)->get()->first();

        $transaction_request = TransactionRequest::create([
            'user_id' => Auth::guard('api')->user()->id,
            'request_id' => Str::orderedUuid()->toString(),
            'service_identifier' => 'merchant_payment',
            'amount' => $validated_data['amount'],
            'merchant_id' => $merchant_information->id,
            'is_merchant_payment' => true,
            'service_status' => 'N/A'
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
                merchantNumber: $merchant_information->merchant_number,
                amount: $transaction_request->amount,
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
            'message' => 'Merchant payment request received and payment url has been generated.',
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

    public function addMerchantAPI(Request $request)
    {
        $request->validate([
            'merchant_title' => 'required|string|max:255',
            'merchant_number' => 'required|string|max:255',
            'merchant_logo' => 'required|string',
            'is_active' => 'bool'
        ]);

        $merchant_logo_img = "/storage/defaults/512x512.png";
        if ($image = $request->input('merchant_logo')) {
            $image = explode('base64,', $image);
            $extension = explode(';', explode('/', $image[0])[1])[0];
            $image = end($image);
            $image = str_replace(' ', '+', $image);
            $file_path = "uploads/merchants/" . Str::orderedUuid()->toString() . '.' . $extension;
            File::put($file_path, base64_decode($image));
            $merchant_logo_img = '/' . $file_path;
        }

        $merchant_hash = base64_encode(Str::orderedUuid()->toString());

        $qr_image = QrCode::format('svg')->size(512)->generate($merchant_hash);
        $qr_image_name = Str::orderedUuid()->toString() . '.svg';
        File::put(public_path('uploads/merchants/' . $qr_image_name), $qr_image);
        $qr_image_url = '/uploads/merchants/' . $qr_image_name;

        $merchant_information = Merchants::create([
            'merchant_title' => $request->input('merchant_title'),
            'merchant_number' => $request->input('merchant_number'),
            'merchant_logo' => $merchant_logo_img,
            'merchant_hash' => $merchant_hash,
            'merchant_qr' => $qr_image_url,
            'status' => $request->input('is_active') ?? 0
        ]);

        return response()->json([
            'message' => 'Merchant has been created successfully.',
            'data' => $merchant_information
        ]);
    }
}
