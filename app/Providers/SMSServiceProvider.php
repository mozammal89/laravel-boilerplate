<?php

namespace App\Providers;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class SMSServiceProvider
{
    public static function sendSMS($recipient, $sms_body)
    {
        if (Cache::get('app_cfg_sms_data', [])['status']) {
            $api_client = new Client();
            $options = [
                'query' => [
                    'username' => Cache::get('app_cfg_sms_data', [])['username'],
                    'password' => Cache::get('app_cfg_sms_data', [])['password'],
                    'src' => Cache::get('app_cfg_sms_data', [])['masking_name'],
                    'dst' => $recipient,
                    'msg' => $sms_body,
                    'dr' => 1,
                ]
            ];
            $response = $api_client->get('https://sms.textware.lk:5001/sms/send_sms.php', $options);
            $status_code = $response->getStatusCode();
            return true;
        }
        return true;
    }
}
