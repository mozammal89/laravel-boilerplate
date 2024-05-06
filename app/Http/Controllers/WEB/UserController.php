<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Countries;
use App\Models\User;
use App\Providers\AppFunctionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View Users|Can Create Users|Can Edit Users|Can Delete Users'], ['only' => ['index']]);
//        $this->middleware(['permission:Can Create Roles'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:Can Edit Users'], ['only' => ['edit', 'update', 'statusToggle', 'changeRole']]);
        $this->middleware(['permission:Can Delete Users'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_list = User::where('is_mobile_verified', true)->orderBy('first_name', 'ASC')->get();
        $user_roles = Role::orderBy('name', 'ASC')->get();
        return view('users.index', [
            'user_list' => $user_list,
            'user_roles' => $user_roles
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

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Display the specified resource.
     */
    public function myProfile(Request $request)
    {
        if ($request->method() == 'POST') {
            return $this->update($request, auth()->user()->id);
        }
        return $this->edit(auth()->user()->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        $country_list = Countries::orderBy('name', 'ASC')->get();
        return view('users.edit', [
            'user' => $user,
            'country_list' => $country_list
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'string|max:255|nullable',
            'address' => 'string|max:255|nullable',
            'city' => 'string|max:255|nullable',
            'post_code' => 'string|max:255|nullable',
            'country' => 'string|max:255|nullable',
            'profile_photo' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = User::find($id);

        if ($image = $request->file('profile_photo')) {
            $imageName = Str::orderedUuid()->toString() . '.' . $image->extension();
            $image->move(public_path('uploads/user'), $imageName);
            AppFunctionProvider::deleteFile($user->getRawOriginal('profile_photo'));
            $user->profile_photo = '/uploads/user/' . $imageName;
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->address = $request->input('address');
        $user->city = $request->input('city');
        $user->post_code = $request->input('post_code');
        $user->country = $request->input('country');
        $user->save();

        return redirect()->back()->with('success', __('Profile has been updated successfully.'));
    }

    public function statusToggle(string $id)
    {
        $user = User::find($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return redirect()->back()->with('success', __('User status has been toggled successfully.'));
    }

    public function changeRole(Request $request, string $id)
    {
        $request->validate([
            'user_role' => 'required|string|max:255',
        ]);
        $new_role = Role::find($request->input('user_role'))->name;
        $user = User::find($id);
        $user->syncRoles([$new_role]);
        return redirect()->back()->with('success', __('User role has been changed successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', __('User account has been deleted successfully.'));
    }
}
