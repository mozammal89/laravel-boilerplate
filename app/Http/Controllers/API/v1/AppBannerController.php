<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\AppBanners;
use Exception;
use Illuminate\Http\Request;

class AppBannerController extends Controller
{
    public function getAppBanners(Request $request)
    {
        try {
            $app_banners = AppBanners::where('status', true)->orderBy('sorting_index', 'ASC')->get();
            return response()->json([
                'message' => 'App banners received successfully.',
                'data' => $app_banners
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
