<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\AppCategory;
use App\Models\BillerList;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Http\Request;

class AppCategoryController extends Controller
{
    public function getAppCategories(Request $request)
    {
        try {
            $app_category = AppCategory::where('status', true)->orderBy('sorting_index', 'ASC')->get();
            return response()->json([
                'message' => 'App categories received successfully.',
                'data' => $app_category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getAppCategoryBiller(Request $request)
    {
        try {
            $validator = ValidationServiceProvider::validateRequestData($request, [
                'category' => 'required|int|exists:app_categories,id',
            ]);

            if (!$validator['is_valid']) {
                return $validator['response'];
            }

            $validated_data = $validator['validated_data'];

            $app_category = AppCategory::where('id', $validated_data['category'])->get()->first();
            $biller_lists = [];
            foreach ($app_category->billers as $biller) {
                if ($biller['status']) {
                    $biller['identifier'] = $biller['identifier'] ?? 'general';
                    $biller_lists[] = $biller;
                }
            }

            return response()->json([
                'message' => 'Biller lists received successfully.',
                'data' => $biller_lists
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
