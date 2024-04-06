<?php

namespace App\Interfaces;



Interface ProductRepositoryInterface{
    public function getAllProducts();
    public function getProductById($orderId);
    public function stepProductDown($orderId);
    public function getActiveProducts();
    public function getSteppedDownProducts();
    public function createProduct(array $orderDetails);
    public function updateProduct($orderId, array $newDetails);
   
}