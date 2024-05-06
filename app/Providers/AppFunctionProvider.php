<?php

namespace App\Providers;

use \App\Http\Controllers\API\v2\Service\ServiceController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppFunctionProvider
{
    public static function readCSV($csvFile, $delimiter = ',')
    {
        $file_handle = fopen($csvFile, 'r');
        while ($csvRow = fgetcsv($file_handle, null, $delimiter)) {
            $line_of_text[] = $csvRow;
        }
        fclose($file_handle);
        return $line_of_text;
    }

    public static function readJSON($jsonFile)
    {
        $file_handle = File::get($jsonFile);
        return json_decode(json: $file_handle, associative: true);
    }

    public static function deleteFile($file_path)
    {
        if (!str_contains($file_path, '/defaults/')) {
            $file_path = substr($file_path, 1);
            if (File::exists(public_path($file_path))) {
                File::delete(public_path($file_path));
            }
        }
    }

    public static function paymentSuccessHandler($biller_id, $transaction_request)
    {
        if ($transaction_request->payment_status == 'Successful' && !$transaction_request->is_merchant_payment) {
            // Queue for service activation
            $service_controller = new ServiceController();
            $service_controller->activateService($biller_id, $transaction_request);
        }
    }
}
