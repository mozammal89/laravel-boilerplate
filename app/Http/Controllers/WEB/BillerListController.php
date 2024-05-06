<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AppCategory;
use App\Models\BillerGroup;
use App\Models\BillerList;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillerListController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View Biller Lists|Can Create Biller Lists|Can Edit Biller Lists|Can Delete Biller Lists'], ['only' => ['index']]);
        $this->middleware(['permission:Can Create Biller Lists'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Can Edit Biller Lists'], ['only' => ['edit', 'update', 'toggleStatus', 'setBillerGroup']]);
        $this->middleware(['permission:Can Delete Biller Lists'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $app_categories = AppCategory::orderBy('name', 'ASC')->get();
        $biller_list = BillerList::with('biller_groups')->orderBy('biller_name', 'ASC')->get();
        $biller_groups = BillerGroup::orderBy('name', 'ASC')->get();
        return view('biller.list.index', [
            'biller_list' => $biller_list,
            'biller_groups' => $biller_groups,
            'app_categories' => $app_categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $biller_groups = BillerGroup::orderBy('name', 'ASC')->get();
        return view('biller.list.create', [
            'biller_groups' => $biller_groups
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'biller_group' => 'nullable',
            'domain_code' => 'nullable|string|max:255',
            'biller_name' => 'required|string|max:255',
            'biller_icon' => 'required|image|mimes:png|max:200',
            'biller_category' => 'nullable|string|max:255',
            'transaction_type' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'identifier' => 'nullable|string|max:255',
            'credentials' => 'json|nullable',
            'use_group_credentials' => 'bool',
            'is_active' => 'bool',
            'sorting_index' => 'required|int'
        ]);

        if (BillerList::where('identifier', $request->input('identifier'))->count() > 0) {
            return redirect()->back()
                ->with('error', __('This biller identifier is already taken.'))
                ->withInput($request->input());
        }

        $biller_icon_img = "";
        if ($image = $request->file('biller_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/biller'), $imageName);
            $biller_icon_img = '/uploads/biller/' . $imageName;
        }

        $credentials = null;
        if ($request->input('credentials')) {
            $credentials = $request->input('credentials');
        }

        BillerList::create([
            'group_id' => $request->input('biller_group') ?? null,
            'domain_code' => $request->input('domain_code'),
            'biller_name' => $request->input('biller_name'),
            'biller_icon' => $biller_icon_img,
            'biller_category' => $request->input('biller_category'),
            'availability' => $request->input('availability'),
            'transaction_type' => $request->input('transaction_type'),
            'identifier' => $request->input('identifier'),
            'credentials' => $credentials,
            'use_group_credentials' => $request->input('use_group_credentials') ?? 0,
            'status' => $request->input('is_active') ?? 0,
            'sorting_index' => $request->input('sorting_index')
        ]);

        return redirect()->route('admin.biller.list.index')
            ->with('success', __('Biller has been created successfully.'))
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
        $biller_info = BillerList::find($id);
        $biller_groups = BillerGroup::orderBy('name', 'ASC')->get();
        return view('biller.list.edit', [
            'biller_info' => $biller_info,
            'biller_groups' => $biller_groups
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'domain_code' => 'nullable|string|max:255',
            'biller_name' => 'required|string|max:255',
            'biller_icon' => 'image|mimes:png|max:200',
            'biller_category' => 'nullable|string|max:255',
            'transaction_type' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'identifier' => 'nullable|string|max:255',
            'credentials' => 'json|nullable',
            'use_group_credentials' => 'bool',
            'is_active' => 'bool',
            'sorting_index' => 'required|int'
        ]);

        $biller_info = BillerList::find($id);

        if ($image = $request->file('biller_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/biller'), $imageName);
            AppFunctionProvider::deleteFile($biller_info->getRawOriginal('biller_icon'));
            $biller_info->biller_icon = '/uploads/biller/' . $imageName;
        }

        $credentials = null;
        if ($request->input('credentials')) {
            $credentials = $request->input('credentials');
        }

        $biller_info->domain_code = $request->input('domain_code');
        $biller_info->biller_name = $request->input('biller_name');
        $biller_info->biller_category = $request->input('biller_category');
        $biller_info->availability = $request->input('availability');
        $biller_info->transaction_type = $request->input('transaction_type');
        $biller_info->identifier = $request->input('identifier');
        $biller_info->credentials = $credentials;
        $biller_info->use_group_credentials = $request->input('use_group_credentials') ?? 0;
        $biller_info->status = $request->input('is_active') ?? 0;
        $biller_info->sorting_index = $request->input('sorting_index');
        $biller_info->save();

        return redirect()->route('admin.biller.list.index')
            ->with('success', __('Biller has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Update the biller group of specified resource in storage.
     */
    public function setAppCategory(Request $request, string $id)
    {
        $request->validate([
            'app_category' => 'required|string|max:255',
        ]);

        $biller_group = BillerList::find($id);
        $biller_group->category_id = $request->input('app_category');
        $biller_group->save();

        return redirect()->route('admin.biller.list.index')
            ->with('success', __('App category has been set successfully.'));
    }

    /**
     * Update the status of specified resource in storage.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $biller = BillerList::find($id);
        $biller->status = !$biller->status;
        $biller->save();

        return redirect()->route('admin.biller.list.index')
            ->with('success', __('Biller status has been toggled successfully.'));
    }

    /**
     * Update the biller group of specified resource in storage.
     */
    public function setBillerGroup(Request $request, string $id)
    {
        $request->validate([
            'biller_group' => 'required|string|max:255',
        ]);

        $biller = BillerList::find($id);
        $biller->group_id = $request->input('biller_group');
        $biller->save();

        return redirect()->route('admin.biller.list.index')
            ->with('success', __('Biller group has been set successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $biller_instance = BillerList::find($id);
        $biller_instance->delete();
        return redirect()->route('admin.biller.list.index')
            ->with('success', __('Biller has been deleted successfully.'));
    }
}
