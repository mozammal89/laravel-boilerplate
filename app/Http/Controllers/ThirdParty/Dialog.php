<?php

namespace App\Http\Controllers\ThirdParty;

use App\Http\Controllers\Controller;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Dialog extends Controller
{
    public function __construct($service_param, $credentials)
    {
        $this->api_client = new Client();
        $this->base_url = $credentials['base_url'];
        $this->get_token_auth = $credentials['get_token_auth'];
        $this->wallet_check_username = $credentials['wallet_check_username'];
        $this->wallet_check_password = $credentials['wallet_check_password'];
        $this->balance_check_username = $credentials['balance_check_username'];
        $this->balance_check_password = $credentials['balance_check_password'];
        $this->alias = $service_param['wallet_alias'];
        $this->pin = $service_param['wallet_pin'];
    }

    private function getAccessToken()
    {
        try {
            $token_expiry_time = Cache::get('service_recharge_dialog_token_expiry', 0);
            $access_token = Cache::get('service_recharge_dialog_access_token');
            if (!$access_token || now()->timestamp >= $token_expiry_time) {
                $options = [
                    'headers' => [
                        'Authorization' => $this->get_token_auth,
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    'form_params' => [
                        'grant_type' => 'client_credentials'
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/api_admin_00001', $options);
                $status_code = $response->getStatusCode();
                $response = json_decode($response->getBody()->getContents(), true);
                if ($status_code == 200 && key_exists('access_token', $response)) {
                    $expire_time = intval($response['expires_in']);
                    $access_token = $response['access_token'];
                    Cache::put('service_recharge_dialog_access_token', $access_token);
                    Cache::put('service_recharge_dialog_token_expiry', now()->timestamp + $expire_time);
                } else {
                    $access_token = null;
                    Cache::put('service_recharge_dialog_token_expiry', 0);
                }
            }

            return [
                'status' => true,
                'access_token' => $access_token
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'access_token' => null
            ];
        }
    }

    private function isValidMobileNumber($mobile)
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'connections' => [$mobile]
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/api_crm_0000120191004/24', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    if ($response['executionMessage'] == "SUCCESS") {
                        foreach ($response['response'] as $item) {
                            if ($item['connectionStatus'] == "AVAILABLE") {
                                return true;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    private function hasEnoughBalance($amount)
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'credentials' => json_encode([
                            "username" => "cpos_client",
                            "password" => "CposClient@2015"
                        ]),
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'emoneybalancerequest' => [
                            'subscriberAlias' => $this->alias,
                            'subscriberPin' => $this->pin
                        ]
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/api_software_0000120180524', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    $return_data = $response['getSubscriberBalanceResponse']['return'];
                    return $return_data['status'] == 5 && $return_data['availableAmount'] >= $amount;
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function isServiceAvailable()
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'credentials' => json_encode([
                            "userName" => $this->wallet_check_username,
                            "password" => $this->wallet_check_password
                        ]),
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'walletAlias' => $this->alias
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/api_software_0000920180523', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    $return_data = $response['isWalletAvailableResponse']['return'];
                    return $return_data['availability'] == "Y" && $return_data['status'] == 5;
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function validateServiceRequest(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'mobile_number' => 'required',
                'amount' => 'required'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

//            if (!$this->isValidMobileNumber($validated_data['mobile_number'])) {
//                return response()->json([
//                    'message' => 'Your mobile number is invalid. Please enter a valid one.'
//                ], 400);
//            }

            if (!$this->hasEnoughBalance($validated_data['amount'])) {
                return response()->json([
                    'message' => 'Biller does not have sufficient fund to process this transaction.'
                ], 400);
            }

            return [
                'data' => $validator['validated_data']
            ];
        } catch (Exception $e) {
            return $e;
        }
    }

    public function activateService($transaction_request)
    {
        try {
            $token_response = $this->getAccessToken();

            $service_request_id = "SV" . now()->timestamp;
            $transaction_request->service_request_id = $service_request_id;
            $transaction_request->save();

            if ($token_response['status']) {
                $access_token = $token_response['access_token'];
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'username' => $this->wallet_check_username,
                        'password' => $this->wallet_check_password,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'txType' => 'TX_DIC',
                        'agentAlias' => strtoupper($transaction_request->billers->domain_code) . '_PAY',
                        'agentPin' => $transaction_request->billers->biller_pin,
                        'txAmount' => $transaction_request->service_parameters['amount'],
                        'agentnotificationSend' => true,
                        'subscribernotificationSend' => true,
                        'requestId' => $transaction_request->service_request_id,
                        'subscriberMobile' => $transaction_request->service_parameters['mobile_number'],
                        'channel' => 'WEB',
                        'txReference' => $transaction_request->txn_reference,
                    ]
                ];

                $response = $this->api_client->post($this->base_url . '/api_software_0000320171129', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    $transaction_request->service_response_payload = json_encode($response);
                    $transaction_request->save();
                    if ($response['status'] == 5) {
                        $transaction_request->service_status = "Delivered";
                        $transaction_request->service_activation_time = now();
                        $transaction_request->save();
                        return true;
                    }
                    return false;
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }
}
