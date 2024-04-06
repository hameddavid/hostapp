<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;



class ProductRepository implements  ProductRepositoryInterface
{

    public function getAllProducts()
    {
        return Product::all();
    }

    public function getProductById($productId)
    {
        return Product::findOrFail($productId);
    }

    public function stepProductDown($productId)
    {

    }

    public function GetActiveProducts()
    {

    }

    public function GetSteppedDownProducts()
    {

    }

    public function createProduct($arrayOfProductDetails)
    {

    }

    public function updateProduct($productId,$newArrayOfProductDetails)
    {

    }
  

}