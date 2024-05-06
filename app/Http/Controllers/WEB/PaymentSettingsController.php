<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class PaymentSettingsController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can Change Payment Settings'], ['only' => ['index', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
        return view('settings.payment.edit', [
            'payment_settings' => $payment_settings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'bank_mid' => 'required|string|max:255',
            'merchant_number' => 'required|string|max:255'
        ]);

        $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
        $payment_settings->bank_mid = $request->input('bank_mid');
        $payment_settings->merchant_number = $request->input('merchant_number');
        $payment_settings->save();

        Session::put('app_cfg_last_fetch_time', 0);

        $payment_settings = PaymentSettings::where(['codename' => 'superapp'])->first();
        Cache::put('app_cfg_payment_data', $payment_settings);

        return redirect()->route('admin.settings.payment')
            ->with('success', __('Payment settings has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
