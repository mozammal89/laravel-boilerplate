<?php

namespace App\Http\Controllers\API\v1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\Webxpay;
use App\Models\TransactionCallbacks;
use App\Models\TransactionRequest;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use function Termwind\render;

class CallbackController extends Controller
{
    public function webxpayCallback(Request $request)
    {
        $received_data = json_encode($request->input());
        $success = boolval($request->input('success')) ?? false;
        $statusCode = $request->input('statusCode');
        $request_id = $request->input('storeReference');
        $currency_code = $request->input('currency');
        $amount = $request->input('amount');
        $payment_reference = $request->input('referenceNo');
        $transaction_reference = $request->input('transaction')['txnReference'] ?? null;
        $transaction_response = $request->input('transaction')['responseText'] ?? null;

        if (TransactionRequest::where('request_id', $request_id)->count() == 1 && TransactionCallbacks::where('request_id', $request_id)->count() == 0) {
            $trx_callback = TransactionCallbacks::create([
                'request_id' => $request_id,
                'payment_reference' => $payment_reference,
                'transaction_reference' => $transaction_reference,
                'transaction_response' => $transaction_response,
                'currency_code' => $currency_code,
                'amount' => $amount,
                'success' => $success,
                'status_code' => $statusCode,
                'received_data' => $received_data
            ]);

            $transaction_request = TransactionRequest::where('request_id', $request_id)->get()->first();
            if ($trx_callback->success && $trx_callback->status_code == 100 && $trx_callback->amount == $transaction_request->amount && $trx_callback->payment_reference == $transaction_request->payment_reference) {
                $transaction_request->card_info = json_encode($request->input('tokenization'));
                $transaction_request->card_type = $request->input('tokenization')['cardType'] ?? null;
                $transaction_request->card_number_masked = $request->input('tokenization')['maskedCardNumber'] ?? null;
                $transaction_request->card_expiry = $request->input('tokenization')['cardExpiry'] ?? null;
                $transaction_request->payment_status = 'Successful';
                $transaction_request->payment_completion_time = now();
            } else {
                $transaction_request->payment_status = 'Failed';
            }
            $transaction_request->payment_response_payload = $received_data;
            $transaction_request->txn_reference = $received_data['transaction']['txnReference'] ?? null;
            $transaction_request->save();

            $webxpay = new Webxpay();
            $verification_data = $webxpay->verifyTransaction($trx_callback->request_id);
            if ($verification_data['status']) {
                $verification_data = $verification_data['data'];
                $transaction_request->transaction_verification_response = $verification_data;
                $transaction_request->txn_reference = $verification_data['transaction']['txnReference'] ?? null;
                $transaction_request->save();
                if ($verification_data['success'] == $trx_callback->success && $verification_data['statusCode'] == $trx_callback->status_code && $verification_data['referenceNo'] == $trx_callback->payment_reference && $verification_data['storeReference'] == $trx_callback->request_id) {
                    $transaction_request->is_transaction_verified = true;
                    $transaction_request->save();
                }
            }

            AppFunctionProvider::paymentSuccessHandler($transaction_request->biller_id, $transaction_request);

            // Redirect
            if ($transaction_request->payment_status == "Successful") {
                return redirect(route('ipg.app.return', ['trx_status' => 'success']));
            } else {
                return redirect(route('ipg.app.return', ['trx_status' => 'failed']));
            }
        } else {
            if ($success && $statusCode == 100) {
                $refund = boolval($request->input('refund')['success']) ?? false;
                $statusCode = $request->input('refund')['statusCode'];
                if ($refund && $statusCode == 100) {
                    return redirect(route('ipg.app.return', ['trx_status' => 'card-save-success']));
                } else {
                    return redirect(route('ipg.app.return', ['trx_status' => 'card-save-failed']));
                }
            } else {
                return view('errors.404');
            }
        }
    }
}
