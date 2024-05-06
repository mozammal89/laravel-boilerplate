<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AppSettings;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AppSettingsController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can Change Settings'], ['only' => ['index', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $app_settings = AppSettings::where(['codename' => 'superapp'])->first();
        return view('settings.app.edit', [
            'app_settings' => $app_settings
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
            'app_name' => 'required|string|max:255',
            'app_logo' => 'image|mimes:png|max:500',
            'app_icon' => 'image|mimes:png|max:500',
            'primary_color' => 'required|string|max:255',
            'secondary_color' => 'required|string|max:255'
        ]);

        $app_settings = AppSettings::where(['codename' => 'superapp'])->first();

        if ($image = $request->file('app_logo')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/app'), $imageName);
            AppFunctionProvider::deleteFile($app_settings->getRawOriginal('app_logo'));
            $app_settings->app_logo = '/uploads/app/' . $imageName;
        }

        if ($image = $request->file('app_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/app'), $imageName);
            AppFunctionProvider::deleteFile($app_settings->getRawOriginal('app_icon'));
            $app_settings->app_icon = '/uploads/app/' . $imageName;
        }

        $app_settings->app_name = $request->input('app_name');
        $app_settings->primary_color = $request->input('primary_color');
        $app_settings->secondary_color = $request->input('secondary_color');
        $app_settings->save();

        Session::put('app_cfg_last_fetch_time', 0);

        return redirect()->route('admin.settings.app')
            ->with('success', __('App settings has been updated successfully.'))
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
