<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AppCategory;
use App\Models\BillerGroup;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillerGroupController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View Biller Groups|Can Create Biller Groups|Can Edit Biller Groups|Can Delete Biller Groups'], ['only' => ['index']]);
        $this->middleware(['permission:Can Create Biller Groups'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Can Edit Biller Groups'], ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware(['permission:Can Delete Biller Groups'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $biller_groups = BillerGroup::orderBy('name', 'ASC')->get();
        return view('biller.group.index', [
            'biller_groups' => $biller_groups
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('biller.group.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group_icon' => 'image|mimes:png|max:200',
            'credentials' => 'string|nullable',
            'is_active' => 'bool'
        ]);

        $group_icon_img = "/storage/defaults/512x512.png";
        if ($image = $request->file('group_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/biller'), $imageName);
            $group_icon_img = '/uploads/biller/' . $imageName;
        }

        $credentials = null;
        if ($request->input('credentials')) {
            $credentials = $request->input('credentials');
        }

        BillerGroup::create([
            'name' => $request->input('name'),
            'icon' => $group_icon_img,
            'credentials' => $credentials,
            'status' => $request->input('is_active') ?? 0
        ]);

        return redirect()->route('admin.biller.group.index')
            ->with('success', __('Biller group has been created successfully.'))
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
        $biller_group = BillerGroup::find($id);

        return view('biller.group.edit', [
            'biller_group' => $biller_group
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group_icon' => 'image|mimes:png|max:200',
            'credentials' => 'string|nullable',
            'is_active' => 'bool'
        ]);

        $biller_group = BillerGroup::find($id);

        if ($image = $request->file('group_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/biller'), $imageName);
            AppFunctionProvider::deleteFile($biller_group->getRawOriginal('icon'));
            $biller_group->icon = '/uploads/biller/' . $imageName;
        }

        $credentials = null;
        if ($request->input('credentials')) {
            $credentials = $request->input('credentials');
        }

        $biller_group->name = $request->input('name');
        $biller_group->credentials = $credentials;
        $biller_group->status = $request->input('is_active') ?? 0;
        $biller_group->save();

        return redirect()->route('admin.biller.group.index')
            ->with('success', __('Biller group has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Update the status of specified resource in storage.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $biller_group = BillerGroup::find($id);
        $biller_group->status = !$biller_group->status;
        $biller_group->save();

        return redirect()->route('admin.biller.group.index')
            ->with('success', __('Biller group status has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $biller_group = BillerGroup::find($id);
        $biller_group->delete();
        return redirect()->route('admin.biller.group.index')
            ->with('success', __('Biller group has been deleted successfully.'));
    }
}
