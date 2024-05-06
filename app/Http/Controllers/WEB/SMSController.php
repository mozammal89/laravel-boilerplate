<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\SMSSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class SMSController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can Change SMS Settings'], ['only' => ['index', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sms_settings = SMSSettings::where(['codename' => 'superapp'])->first();
        return view('settings.sms.edit', [
            'sms_settings' => $sms_settings
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
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'masking_name' => 'required|string|max:255',
            'status' => 'bool',
        ]);

        $sms_settings = SMSSettings::where(['codename' => 'superapp'])->first();
        $sms_settings->username = $request->input('username');
        $sms_settings->password = $request->input('password');
        $sms_settings->masking_name = $request->input('masking_name');
        $sms_settings->status = $request->input('status') ?? 0;
        $sms_settings->save();

        Session::put('app_cfg_last_fetch_time', 0);

        $sms_settings = SMSSettings::where(['codename' => 'superapp'])->first();
        Cache::put('app_cfg_sms_data', $sms_settings);

        return redirect()->route('admin.settings.sms')
            ->with('success', __('SMS settings has been updated successfully.'))
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
