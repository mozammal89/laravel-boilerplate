<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IPGController extends Controller
{
    public function ipgReturn(string $trx_status)
    {
        return response()->json([
            'status' => $trx_status
        ]);
    }
}
