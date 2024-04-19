<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    // there is an issue when product price has changed and needs to be updated
    // when updated there should be a way to reference the old using same or related product Id
    protected $fillable = ['name','description','amount', 'deleted', 'status','promo','discount'];
}
