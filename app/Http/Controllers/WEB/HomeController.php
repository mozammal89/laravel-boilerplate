<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\BillerGroup;
use App\Models\BillerList;
use App\Models\Merchants;
use App\Models\TransactionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $user_count = User::where('is_mobile_verified', true)->whereHas(
            'roles', function ($q) {
            $q->where('name', 'User');
        })->count();
        $biller_count = BillerList::all()->count();
        $transaction_count = TransactionRequest::all()->count();
        $biller_groups = BillerGroup::all()->count();
        $merchant_count = Merchants::all()->count();

        return view('dashboard.home')->with([
            'user_count' => $user_count,
            'biller_count' => $biller_count,
            'transaction_count' => $transaction_count,
            'biller_groups' => $biller_groups,
            'merchant_count' => $merchant_count
        ]);
    }
}
