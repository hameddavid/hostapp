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
            'user' => $this->user->email,
            'purchase' => [
                'product_name' => $this->product->name,
                'product_description' => $this->product->description,
                'product_amount' => $this->product->amount,
            ]
        ];
    }
}
