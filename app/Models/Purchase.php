<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id' ,'product_id', 'payment_id','quantity','meta_data','purchase_date','expiring_date','invoice_number'];

    protected $casts = ['meta_data' => 'array',];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function payment(){
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
