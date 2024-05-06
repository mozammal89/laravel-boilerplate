<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AppBanners;
use App\Models\Merchants;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppBannerController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View App Banners|Can Create App Banners|Can Edit App Banners|Can Delete App Banners'], ['only' => ['index']]);
        $this->middleware(['permission:Can Create App Banners'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Can Edit App Banners'], ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware(['permission:Can Delete App Banners'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banner_list = AppBanners::orderBy('sorting_index', 'ASC')->get();
        return view('app.banners.index')->with([
            'banner_list' => $banner_list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'banner_image' => 'required|image|mimes:png|max:500',
            'sorting_index' => 'required|int',
            'is_active' => 'bool'
        ]);

        if ($image = $request->file('banner_image')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/app'), $imageName);
            $banner_image = '/uploads/app/' . $imageName;

            AppBanners::create([
                'banner_image' => $banner_image,
                'sorting_index' => $request->input('sorting_index'),
                'status' => $request->input('is_active') ?? 0
            ]);
        }

        return redirect()->route('admin.app.banners.index')
            ->with('success', __('Banner has been created successfully.'))
            ->withInput($request->input());
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
        $banner_instance = AppBanners::find($id);

        return view('app.banners.edit', [
            'banner_instance' => $banner_instance
        ]);
    }

    /**
     * Update the status of specified resource in storage.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $banner_instance = AppBanners::find($id);
        $banner_instance->status = !$banner_instance->status;
        $banner_instance->save();

        return redirect()->route('admin.app.banners.index')
            ->with('success', __('Banner status has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'banner_image' => 'image|mimes:png|max:500',
            'sorting_index' => 'required|int',
            'is_active' => 'bool'
        ]);

        $banner_instance = AppBanners::find($id);

        if ($image = $request->file('banner_image')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/app'), $imageName);
            AppFunctionProvider::deleteFile($banner_instance->getRawOriginal('banner_image'));
            $banner_instance->banner_image = '/uploads/app/' . $imageName;
        }

        $banner_instance->sorting_index = $request->input('sorting_index');
        $banner_instance->status = $request->input('is_active') ?? 0;
        $banner_instance->save();

        return redirect()->route('admin.app.banners.index')
            ->with('success', __('Banner has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $banner_instance = AppBanners::find($id);
        AppFunctionProvider::deleteFile($banner_instance->getRawOriginal('banner_image'));
        $banner_instance->delete();
        return redirect()->route('admin.app.banners.index')
            ->with('success', __('Banner has been deleted successfully.'));
    }
}
