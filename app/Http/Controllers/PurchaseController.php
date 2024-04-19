<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PurchaseResource::collection(
            Purchase::where('user_id', Auth::user()->id)->with(['product', 'payment'])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $request->validated($request->all()); 

        $purchase = Purchase::create([
            'user_id' => Auth::user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'purchase_date' => $request->purchase_date,
            'expiring_date' => $request->expiring_date,
            'invoice_number' => $request->invoice_number,
        ]);

        return new PurchaseResource($purchase);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return PurchaseResource::collection(
            Purchase::with(['product', 'payment'])->find($id)
        );
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
