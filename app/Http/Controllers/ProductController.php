<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(
            Product::all()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $request->validated($request->all()); 

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount
        ]);

        return new ProductResource($product);
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


    // public function index(): JsonResponse 
    // {
    //     return response()->json([
    //         'data' => $this->productRepository->getAllproducts()
    //     ]);
    // }

    // public function store(Request $request): JsonResponse 
    // {
    //     $productDetails = $request->only([
    //         'client',
    //         'details'
    //     ]);

    //     return response()->json(
    //         [
    //             'data' => $this->productRepository->createproduct($productDetails)
    //         ],
    //         Response::HTTP_CREATED
    //     );
    // }

    // public function show(Request $request): JsonResponse 
    // {
    //     $productId = $request->route('id');

    //     return response()->json([
    //         'data' => $this->productRepository->getproductById($productId)
    //     ]);
    // }

    // public function update(Request $request): JsonResponse 
    // {
    //     $productId = $request->route('id');
    //     $productDetails = $request->only([
    //         'client',
    //         'details'
    //     ]);

    //     return response()->json([
    //         'data' => $this->productRepository->updateproduct($productId, $productDetails)
    //     ]);
    // }

    // public function destroy(Request $request): JsonResponse 
    // {
    //     $productId = $request->route('id');
    //     $this->productRepository->deleteproduct($productId);

    //     return response()->json(null, Response::HTTP_NO_CONTENT);
    // }
}
