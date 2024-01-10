<?php 
namespace App\HelperClass;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class Helper1{

    public static function generateSixRandomCharacter($length = 6) {
        $characters = 'abcdefghijkmnpqrstuvwxyz23456789';
        $randomString = str_shuffle($characters);
        return substr($randomString, 0, $length);
    }

   
}