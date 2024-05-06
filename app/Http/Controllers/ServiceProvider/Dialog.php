<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Providers\ValidationServiceProvider;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Dialog extends Controller
{
    public function __construct($service_param, $credentials)
    {
        $this->api_client = new Client();
        $this->base_url = $credentials['base_url'];
        $this->get_token_auth = $credentials['get_token_auth'];
        $this->wallet_username = $credentials['wallet_username'];
        $this->wallet_password = $credentials['wallet_password'];
        $this->balance_check_username = $credentials['balance_check_username'];
        $this->balance_check_password = $credentials['balance_check_password'];
        $this->transaction_type = $service_param['transaction_type'];
        $this->domain_code = $service_param['domain_code'];
        $this->identifier = $service_param['identifier'];
        $this->wallet_number = $credentials['wallet_number'];
        $this->wallet_pin = $credentials['wallet_pin'];
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
                            'subscriberAlias' => $this->wallet_number,
                            'subscriberPin' => $this->wallet_pin
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

    private function getAccessToken()
    {
        try {
            $token_expiry_time = Cache::get('service_dialog_token_expiry', 0);
            $access_token = Cache::get('service_dialog_access_token');
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
                    Cache::put('service_dialog_access_token', $access_token);
                    Cache::put('service_dialog_token_expiry', now()->timestamp + $expire_time);
                } else {
                    $access_token = null;
                    Cache::put('service_dialog_token_expiry', 0);
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

    public function validateServiceRequest(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'account_number' => 'required',
                'amount' => 'required'
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            if ($this->identifier == 'recharge_dialog') {
                if (!$this->isValidMobileNumber($validated_data['account_number'])) {
                    return response()->json([
                        'message' => 'Your mobile number is invalid. Please enter a valid one.'
                    ], 400);
                }
            }

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

    private function validateServiceStatus($service_request_id)
    {
        try {
            $token_response = $this->getAccessToken();

            if ($token_response['status']) {
                $access_token = $token_response['access_token'];
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'credentials' => json_encode([
                            "username" => $this->wallet_username,
                            "password" => $this->wallet_password
                        ]),
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'requesttransactionstatus' => [
                            'ownerAlias' => $this->wallet_number,
                            'ownerPin' => $this->wallet_pin,
                            'requestId' => $service_request_id,
                        ]
                    ]
                ];

                $response = $this->api_client->post($this->base_url . '/api_software_0000220171129', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    return json_decode($response->getBody()->getContents(), true);
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    public function activateService($transaction_request)
    {
        try {
            $token_response = $this->getAccessToken();

            $service_request_id = "SV" . now()->timestamp;
            $transaction_request->service_request_id = $service_request_id;
            $transaction_request->save();

            $activation_parameters = $transaction_request->service_parameters;

            if ($token_response['status']) {
                $access_token = $token_response['access_token'];
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $access_token,
                        'username' => $this->wallet_username,
                        'password' => $this->wallet_password,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'accountNumber' => $activation_parameters['account_number'],
                        'txType' => 'TX_' . $this->transaction_type,
                        'domain' => $this->domain_code,
                        'agentAlias' => $this->wallet_number,
                        'agentPin' => $this->wallet_pin,
                        'txAmount' => $activation_parameters['amount'],
                        'agentnotificationSend' => true,
                        'requestId' => $service_request_id,
                        'refernceMobileNumber' => $activation_parameters['account_number'],
                        'channel' => 'WEB'
                    ]
                ];

                $response = $this->api_client->post($this->base_url . '/api_software_0000320171129', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    $transaction_request->service_response_payload = json_encode($response);
                    $transaction_request->save();

                    $validation_response = $this->validateServiceStatus($service_request_id);
                    if ($validation_response) {
                        $transaction_request->service_validation_response = $validation_response;
                        $transaction_request->save();
                        if ($response['status'] == $validation_response['getTransactionStatusViaRequestIdResponse']['return']['status']) {
                            $transaction_request->service_validated = true;
                            $transaction_request->save();
                        }
                    }

                    if ($response['status'] == 5) {
                        $transaction_request->service_status = "Delivered";
                        $transaction_request->service_activation_time = now();
                        $transaction_request->save();
                    }
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }
}
