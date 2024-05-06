<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Webxpay extends Controller
{
    public function __construct()
    {
        $this->api_client = new Client();
        $this->base_url = "https://superapp.webxpaydev.com/api";
        $this->auth_email = "amarpay@amar.com";
        $this->auth_password = "1qaz2wsx@A";
        $this->token_lifetime_hour = 3;
    }

    private function getAccessToken()
    {
        try {
            $token_expiry_time = Cache::get('ipg_token_expiry', 0);
            $access_token = Cache::get('ipg_access_token');
            if (!$access_token || now()->timestamp >= $token_expiry_time) {
                $options = [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'email' => $this->auth_email,
                        'password' => $this->auth_password
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/login', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    if (key_exists('token', $response)) {
                        $access_token = $response['token'];
                        Cache::put('ipg_access_token', $access_token);
                        Cache::put('ipg_token_expiry', now()->timestamp + ($this->token_lifetime_hour * 60 * 60));
                    } else {
                        $access_token = null;
                        Cache::put('ipg_token_expiry', 0);
                    }
                }

                return [
                    'status' => $status_code == 200,
                    'access_token' => $access_token
                ];
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

    public function initializePayment($request_id, $merchantNumber, $amount, $bankMID, $customer_first_name, $customer_last_name, $customer_mobile_number, $txnType = 'save_and_pay', $currency = "LKR", $customData = "", $card_token = "")
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];

                $options = [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $access_token
                    ],
                    'json' => [
                        'storeReference' => $request_id,
                        'amount' => $amount,
                        'currency' => $currency,
                        'bankMID' => $bankMID,
                        'txnType' => $txnType,
                        'merchantNumber' => $merchantNumber,
                        'customData' => $customData,
                        'customer' => [
                            'appCustomerId' => $customer_mobile_number,
                            'appContactNumber' => $customer_mobile_number,
                            'appFirstName' => $customer_first_name,
                            'appLastName' => $customer_last_name,
                        ]
                    ]
                ];

                $endpoint = "";
                if ($txnType == 'save_and_pay') {
                    $endpoint = 'token';
                } elseif ($txnType == 'save_another_and_pay' || $txnType == 'save_another') {
                    $endpoint = 'saveanother';
                } elseif ($txnType == 'pay_only') {
                    $options['json']['token'] = $card_token;
                    $endpoint = 'pay';
                }

                $response = $this->api_client->post($this->base_url . '/app/' . $endpoint, $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    $response = $response ?? [];

                    if ($txnType == 'pay_only' || $txnType == 'save_another') {
                        if (key_exists('success', $response) && key_exists('statusCode', $response) && $response['success'] && $response['statusCode'] == 100) {
                            return [
                                'status' => true,
                                'message' => $response['transaction']['responseText'],
                                'data' => $response
                            ];
                        }
                    }

                    if (key_exists('referenceNo', $response) && key_exists('paymentPageUrl', $response)) {
                        return [
                            'status' => true,
                            'message' => 'Payment has been initialized successfully.',
                            'data' => [
                                'reference' => $response['referenceNo'],
                                'payment_url' => $response['paymentPageUrl']
                            ]
                        ];
                    } elseif (key_exists('success', $response) && key_exists('statusCode', $response) && !$response['success'] && $response['statusCode'] == 15) {
                        return [
                            'status' => false,
                            'message' => $response['transaction']['responseText']
                        ];
                    } else {
                        if ($txnType != 'pay_only' && $txnType != 'save_another') {
                            return $this->initializePayment(
                                $request_id,
                                $merchantNumber,
                                $amount,
                                $bankMID,
                                $customer_first_name,
                                $customer_last_name,
                                $customer_mobile_number,
                                'save_another_and_pay'
                            );
                        }
                    }
                }
            }

            return [
                'status' => false,
                'message' => 'Failed to initialize payment.'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to initialize payment. Err: ' . $e->getMessage()
            ];
        }
    }

    public function verifyTransaction($request_id)
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];

                $options = [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $access_token
                    ]
                ];
                $response = $this->api_client->get($this->base_url . '/app/transaction/' . $request_id, $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    return [
                        'status' => true,
                        'message' => "Transaction data received from api",
                        'data' => $response
                    ];
                }
            }

            return [
                'status' => false,
                'message' => 'Failed to get saved cards.'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to get saved cards. Err: ' . $e->getMessage()
            ];
        }
    }

    public function getCustomerCards($contact_number)
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];

                $options = [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $access_token
                    ],
                    'json' => [
                        'appCustomerId' => $contact_number,
                        'appContactNumber' => $contact_number
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/app/cards', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    if (key_exists('success', $response) && !$response['success'] && $response['statusCode'] == 15) {
                        return [
                            'status' => false,
                            'message' => $response['message']
                        ];
                    } else {
                        return [
                            'status' => true,
                            'message' => "Saved card received successfully.",
                            'data' => $response
                        ];
                    }
                }
            }

            return [
                'status' => false,
                'message' => 'Failed to get saved cards.'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to get saved cards. Err: ' . $e->getMessage()
            ];
        }
    }

    public function deleteCustomerCards($token_id, $token, $contact_number)
    {
        try {
            $token_response = $this->getAccessToken();
            if ($token_response['status']) {
                $access_token = $token_response['access_token'];

                $options = [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $access_token
                    ],
                    'json' => [
                        'token_id' => $token_id,
                        'token' => $token,
                        'customer' => [
                            'appCustomerId' => $contact_number,
                            'appContactNumber' => $contact_number
                        ]
                    ]
                ];
                $response = $this->api_client->post($this->base_url . '/app/cards/delete', $options);
                $status_code = $response->getStatusCode();
                if ($status_code == 200) {
                    $response = json_decode($response->getBody()->getContents(), true);
                    return [
                        'status' => true,
                        'message' => "Card has been removed successfully.",
                        'data' => $response
                    ];
                }
            }

            return [
                'status' => false,
                'message' => 'Failed to remove saved cards.'
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to remove saved cards. Err: ' . $e->getMessage()
            ];
        }
    }
}
