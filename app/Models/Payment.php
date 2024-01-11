<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Payment extends Model
{
    use HasFactory;
   
    protected $fillable = ['user_id' ,'product_id', 'amount','part_pay','invoice_number','invoiceReference','transactionReference','url','account_number','payment_date_time'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }

}
