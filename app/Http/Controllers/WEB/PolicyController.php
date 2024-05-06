<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\PaymentSettings;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can Change Policy'], ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(string $key)
    {
        $policy = Policy::where(['key' => $key])->first();
        return view('policy.index', [
            'policy' => $policy
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
    public function edit(string $key)
    {
        $policy = Policy::where(['key' => $key])->first();
        return view('policy.edit', [
            'policy' => $policy
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $key)
    {
        $request->validate([
            'title' => 'required|string',
            'value' => 'required|string'
        ]);

        $policy = Policy::where(['key' => $key])->first();
        $policy->title = $request->input('title');
        $policy->value = $request->input('value');
        $policy->save();

        return redirect()->back()
            ->with('success', __('Policy has been updated successfully.'))
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
