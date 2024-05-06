<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Merchants;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $merchant_list = Merchants::orderBy('merchant_title', 'ASC')->get();
        return view('merchants.index')->with([
            'merchant_list' => $merchant_list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('merchants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'merchant_title' => 'required|string|max:255',
            'merchant_number' => 'required|string|max:255',
            'merchant_logo' => 'image|mimes:png|max:500',
            'is_active' => 'bool'
        ]);

        $merchant_logo_img = "/storage/defaults/512x512.png";
        if ($image = $request->file('merchant_logo')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/merchants'), $imageName);
            $merchant_logo_img = '/uploads/merchants/' . $imageName;
        }

        $merchant_hash = base64_encode(Str::orderedUuid()->toString());

        $qr_image = QrCode::format('svg')->size(512)->generate($merchant_hash);
        $qr_image_name = Str::orderedUuid()->toString() . '.svg';
        File::put(public_path('uploads/merchants/' . $qr_image_name), $qr_image);
        $qr_image_url = '/uploads/merchants/' . $qr_image_name;

        Merchants::create([
            'merchant_title' => $request->input('merchant_title'),
            'merchant_number' => $request->input('merchant_number'),
            'merchant_logo' => $merchant_logo_img,
            'merchant_hash' => $merchant_hash,
            'merchant_qr' => $qr_image_url,
            'status' => $request->input('is_active') ?? 0
        ]);

        return redirect()->route('admin.merchants.index')
            ->with('success', __('Merchant has been created successfully.'))
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
        $merchant_instance = Merchants::find($id);

        return view('merchants.edit', [
            'merchant_instance' => $merchant_instance
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'merchant_title' => 'required|string|max:255',
            'merchant_number' => 'required|string|max:255',
            'merchant_logo' => 'image|mimes:png|max:500',
            'is_active' => 'bool'
        ]);

        $merchant_instance = Merchants::find($id);

        if ($image = $request->file('merchant_logo')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/merchants'), $imageName);
            AppFunctionProvider::deleteFile($merchant_instance->getRawOriginal('merchant_logo'));
            $merchant_instance->merchant_logo = '/uploads/merchants/' . $imageName;
        }

        $merchant_instance->merchant_title = $request->input('merchant_title');
        $merchant_instance->merchant_number = $request->input('merchant_number');
        $merchant_instance->status = $request->input('is_active') ?? 0;
        $merchant_instance->save();

        return redirect()->route('admin.merchants.index')
            ->with('success', __('Merchant information has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Update the status of specified resource in storage.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $merchant_instance = Merchants::find($id);
        $merchant_instance->status = !$merchant_instance->status;
        $merchant_instance->save();

        return redirect()->route('admin.merchants.index')
            ->with('success', __('Merchant status has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $merchant_instance = Merchants::find($id);
        AppFunctionProvider::deleteFile($merchant_instance->getRawOriginal('merchant_logo'));
        AppFunctionProvider::deleteFile($merchant_instance->getRawOriginal('merchant_qr'));
        $merchant_instance->delete();
        return redirect()->route('admin.merchants.index')
            ->with('success', __('Merchant has been deleted successfully.'));
    }

    public function printQR(string $id)
    {
        $merchant_instance = Merchants::find($id);
        return view('merchants.qr', [
            'merchant_instance' => $merchant_instance
        ]);
    }

    public function downloadQR(string $id)
    {
        $merchant_instance = Merchants::find($id);
        $file_name = $merchant_instance->merchant_title . '-QR.svg';
        return Response::download(public_path($merchant_instance->getRawOriginal('merchant_qr')), $file_name);
    }
}
