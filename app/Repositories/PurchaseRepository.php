<?php

namespace App\Repositories;

use App\Interfaces\IPurchaseRepository;
use App\Models\Purchase;

class PurchaseRepository implements IPurchaseRepository{
    

    public function CreatePurchase(array $purchase)
    {
        $createNewPurchase = new Purchase();
        $createNewPurchase->user_id = $purchase['user_id'];
        $createNewPurchase->product_id = $purchase['product_id'];
        $createNewPurchase->payment_id = $purchase['payment_id'];
        $createNewPurchase->quantity = $purchase['quantity'];
        $createNewPurchase->meta_data = $purchase['meta_data'];
        $createNewPurchase->purchase_date = $purchase['purchase_date'];
        $createNewPurchase->expiring_date =$purchase['expiring_date'];
        $createNewPurchase->invoice_number = $purchase['invoice_number'];
        $createNewPurchase->save();
        if($createNewPurchase){
            return ["purchase" => $createNewPurchase, "status" => "OK"];
        }
        return ["purchase" => "", "status" => "NOK"];
    }
}