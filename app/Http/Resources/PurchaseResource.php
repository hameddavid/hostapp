<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'userId' => $this->user_id,
            'productId' => $this->product_id,
            'paymentId' => $this->payment_id,
            'quantity' => $this->quantity,
            'purchaseDate' => $this->purchase_date,
            'expiringDate' => $this->expiring_date,
            'invoiceNumber' => $this->invoice_number,
            'purchaseStatus' => $this->purchase_status,
            'product' => $this->whenLoaded('product', function(){
                return ProductResource::collection($this->product);
            }),
            'payment' => $this->whenLoaded('payment', function(){
                return PaymentResource::collection($this->payment);
            }),
            'metaData' => $this->metaData
        ];
    }
}
