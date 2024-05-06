<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View Permissions|Can Change Permissions'], ['only' => ['index']]);
        $this->middleware(['permission:Can Change Permissions'], ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permission_list = Permission::orderBy('id', 'ASC')->get();
        $role_list = Role::orderBy('name', 'ASC')->get();
        return view('permissions.index', [
            'permission_list' => $permission_list,
            'role_list' => $role_list
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Toggle the specified resource in storage.
     */
    public function toggle(Request $request)
    {
        $permission = Permission::find($request->input('permission_id'));
        $role = Role::find($request->input('role_id'));
        if ($role->hasPermissionTo($permission->name)) {
            $role->revokePermissionTo($permission->name);
        } else {
            $role->givePermissionTo($permission->name);
        }
        return response()->json([
            'code' => 200,
            'message' => __('Permission has been toggled successfully.')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
