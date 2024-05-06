<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\BillerGroup;
use App\Models\BillerList;
use App\Providers\ValidationServiceProvider;
use Exception;
use Illuminate\Http\Request;

class BillerController extends Controller
{
    public function getBillerGroups(Request $request)
    {
        return response()->json([
            'message' => "API v1 (getBillerGroups): This api endpoint has been deprecated"
        ], 400);
//        try {
//            $biller_groups = BillerGroup::where('status', true)->get();
//            return response()->json([
//                'message' => 'Biller groups received successfully.',
//                'data' => $biller_groups
//            ]);
//        } catch (Exception $e) {
//            return response()->json([
//                'message' => $e->getMessage()
//            ], 400);
//        }
    }

    public function getBillerLists(Request $request)
    {
        return response()->json([
            'message' => "API v1 (getBillerLists): This api endpoint has been deprecated"
        ], 400);

//        try {
//            $validator = ValidationServiceProvider::validateRequestData($request, [
//                'group' => 'required|int|exists:biller_groups,id',
//            ]);
//
//            if (!$validator['is_valid']) {
//                return $validator['response'];
//            }
//
//            $validated_data = $validator['validated_data'];
//
//            $biller_lists = BillerList::where('group_id', $validated_data['group'])->where('status', true)->get();
//            return response()->json([
//                'message' => 'Biller lists received successfully.',
//                'data' => $biller_lists
//            ]);
//        } catch (Exception $e) {
//            return response()->json([
//                'message' => $e->getMessage()
//            ], 400);
//        }
    }
}
