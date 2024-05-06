<?php

namespace App\Http\Middleware;

use App\Models\AppSettings;
use App\Models\PaymentSettings;
use App\Models\SMSSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AppConfig
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if last fetch time is stored in session
        $lastFetchTime = Session::get('app_cfg_last_fetch_time');

        // If last fetch time is not set or more than 15 minutes have passed, refresh the data
        if (!$lastFetchTime || now()->diffInMinutes($lastFetchTime) >= 15) {
            // Fetch data from database (Replace this with your actual logic)
            $app_settings = AppSettings::where(['codename' => 'superapp'])->first();
            $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
            $sms_settings = SMSSettings::where(['codename' => 'superapp'])->first();

            // Store data in session
            Session::put('app_cfg_data', $app_settings);
            Session::put('app_cfg_payment_data', $payment_settings);
            Cache::put('app_cfg_payment_data', $payment_settings);
            Cache::put('app_cfg_sms_data', $sms_settings);

            // Update last fetch time
            Session::put('app_cfg_last_fetch_time', now());
        }

        return $next($request);
    }
}
