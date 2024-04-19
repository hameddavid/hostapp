<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'productId' => $this->product_id,
            'amount' => $this->amount,
            'partPay' => $this->partPay,
            'paymentStatus' => $this->payment_status,
            'invoiceReference' => $this->invoiceReference,
            'transactionReference' => $this->transactionReference,
            'url' => $this->url,
            'accountNumber' => $this->account_number,
            'paymentDateTime' => $this->payment_date_time,
            'deleted' => $this->deleted,
            'user' => $this->whenLoaded('user', function() {
                return UserResource::collection($this->user);
            }),
            'product' => $this->whenLoaded('product', function() {
                return ProductResource::collection($this->product);
            }),
        ];
    }
}
