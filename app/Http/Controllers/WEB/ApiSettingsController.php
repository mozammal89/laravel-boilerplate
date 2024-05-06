<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ApiSettingsController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can Change API Key'], ['only' => ['index', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $api_settings = ApiToken::where(['codename' => 'superapp'])->first();
        return view('settings.api.edit', [
            'api_settings' => $api_settings
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
        $api_settings = ApiToken::where(['codename' => 'superapp'])->first();
        $api_settings->api_key = Str::orderedUuid()->toString();
        $api_settings->save();

        return redirect()->route('admin.settings.api')
            ->with('success', __('API key has been updated successfully.'))
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
