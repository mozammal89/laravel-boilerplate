<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * @var array|string[]
     */
    private array $locked_roles;

    function __construct()
    {
        $this->middleware(['permission:Can View Roles|Can Create Roles|Can Edit Roles|Can Delete Roles'], ['only' => ['index']]);
        $this->middleware(['permission:Can Create Roles'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Can Edit Roles'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:Can Delete Roles'], ['only' => ['destroy']]);

        $this->locked_roles = ["User"];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles_list = Role::withCount('users')->orderBy('name', 'ASC')->get();
        return view('roles.index', [
            'roles_list' => $roles_list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'role_name' => 'required|unique:roles,name',
            'is_admin_login_allowed' => 'bool',
            'is_active' => 'bool'
        ]);

        $role = Role::create(['name' => $request->input('role_name')]);
        $role->is_admin_login_allowed = $request->input('is_admin_login_allowed') ?? 0;
        $role->is_active = $request->input('is_active') ?? 0;
        $role->save();

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role has been created successfully.'))
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
        $role = Role::find($id);

        if (in_array($role->name, $this->locked_roles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('This role can not be edited.'));
        }

        return view('roles.edit', [
            'role' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'role_name' => 'required',
            'is_admin_login_allowed' => 'bool',
            'is_active' => 'bool'
        ]);

        $role = Role::find($id);

        if ($role->name != $request->input('role_name')) {
            if (Role::findByName($request->input('role_name'))) {
                return redirect()->back()
                    ->with('error', __('The role name has already been taken.'))
                    ->withInput($request->input());
            }
        }

        if (in_array($role->name, $this->locked_roles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('This role can not be edited.'));
        }

        $role->name = $request->input('role_name');
        $role->is_admin_login_allowed = $request->input('is_admin_login_allowed') ?? 0;
        $role->is_active = $request->input('is_active') ?? 0;
        $role->save();

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role has been updated successfully.'))
            ->withInput($request->input());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::where('id', $id)->withCount('users')->first();

        if (in_array($role['name'], $this->locked_roles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('This role can not be deleted.'));
        }

        if ($role['users_count'] > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('The number of users under the role has to be 0 (Zero) to be able delete the role.'));
        } else {
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', __('The role has been deleted successfully.'));
        }
    }
}
