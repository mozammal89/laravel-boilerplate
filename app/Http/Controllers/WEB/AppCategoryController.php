<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\AppCategory;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppCategoryController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View App Category|Can Create App Category|Can Edit App Category|Can Delete App Category'], ['only' => ['index']]);
        $this->middleware(['permission:Can Create App Category'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Can Edit App Category'], ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware(['permission:Can Delete App Category'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $app_categories = AppCategory::orderBy('name', 'ASC')->get();
        return view('app.category.index', [
            'app_categories' => $app_categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_icon' => 'required|image|mimes:png|max:200',
            'is_active' => 'bool',
            'sorting_index' => 'required|int'
        ]);

        $category_icon_img = "";
        if ($image = $request->file('category_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/app'), $imageName);
            $category_icon_img = '/uploads/app/' . $imageName;
        }

        AppCategory::create([
            'name' => $request->input('name'),
            'icon' => $category_icon_img,
            'status' => $request->input('is_active') ?? 0,
            'sorting_index' => $request->input('sorting_index')
        ]);

        return redirect()->route('admin.app.category.index')
            ->with('success', __('Category has been created successfully.'))
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
        $app_category = AppCategory::find($id);

        return view('app.category.edit', [
            'app_category' => $app_category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_icon' => 'image|mimes:png|max:200',
            'is_active' => 'bool',
            'sorting_index' => 'required|int'
        ]);

        $app_category = AppCategory::find($id);

        if ($image = $request->file('category_icon')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/app'), $imageName);
            AppFunctionProvider::deleteFile($app_category->getRawOriginal('icon'));
            $app_category->icon = '/uploads/app/' . $imageName;
        }

        $app_category->name = $request->input('name');
        $app_category->status = $request->input('is_active') ?? 0;
        $app_category->sorting_index = $request->input('sorting_index');
        $app_category->save();

        return redirect()->route('admin.app.category.index')
            ->with('success', __('Category has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Update the status of specified resource in storage.
     */
    public function toggleStatus(Request $request, string $id)
    {
        $app_category = AppCategory::find($id);
        $app_category->status = !$app_category->status;
        $app_category->save();

        return redirect()->route('admin.app.category.index')
            ->with('success', __('Category status has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $app_category = AppCategory::find($id);
        AppFunctionProvider::deleteFile($app_category->getRawOriginal('icon'));
        $app_category->delete();
        return redirect()->route('admin.app.category.index')
            ->with('success', __('Category has been deleted successfully.'));
    }
}
