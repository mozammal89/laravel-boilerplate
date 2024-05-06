<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\TransactionRequest;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:Can View Transactions'], ['only' => ['index']]);
//        $this->middleware(['permission:Can Create Roles'], ['only' => ['create', 'store']]);
//        $this->middleware(['permission:Can Edit Roles'], ['only' => ['edit', 'update']]);
//        $this->middleware(['permission:Can Delete Roles'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment_success_count = TransactionRequest::where('payment_status', 'Successful')->count();
        $payment_failed_count = TransactionRequest::where('payment_status', 'Failed')->count();
        $service_delivery_count = TransactionRequest::where('service_status', 'Delivered')->count();
        $service_pending_count = TransactionRequest::where('service_status', 'Pending')->count();

        $transaction_list = TransactionRequest::orderBy('id', 'DESC')->limit(20)->get();
        return view('transactions.index', [
            'transaction_list' => $transaction_list,
            'payment_success_count' => $payment_success_count,
            'payment_failed_count' => $payment_failed_count,
            'service_delivery_count' => $service_delivery_count,
            'service_pending_count' => $service_pending_count
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
