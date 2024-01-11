<?php 
namespace App\HelperClass;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PaymentHelper{


    public static function monnifyLogin():object
    {
        $apiKeyAndSecret = env("MONNIFY_API_KEY") . ':' . env("MONNIFY_SECRET_KEY");
        $authorizationHeader = base64_encode($apiKeyAndSecret);
        $http = Http::withHeaders(["Authorization" => "Basic {$authorizationHeader}"])
            ->post("https://api.monnify.com/api/v1/auth/login");
        $response = json_decode(json_encode($http->json()), FALSE);

        // Log::info($response->requestSuccessful);
        // dd($response->responseBody->accessToken);
        // $this->reserveAccountForStudent($response->responseBody->accessToken);
        return $response;
    }



    public static function createInvoice($amount, $description, $email, $name)
    {
        $multiplier = 1160.42;
        $login = SELF::monnifyLogin();
        $generateInvoice = Http::withHeaders([
            "Authorization"=>"Bearer {$login->responseBody->accessToken}"
        ])->post(env("MONNIFY_TEST_ENDPOINT")."/api/v1/invoice/create",[
            "redirectUrl" => 'https',
            "amount"=>$amount * $multiplier,
            "invoiceReference"=>time(),
            "description"=>$description,
            "currencyCode"=>"NGN",
            "customerEmail"=>$email,
            "contractCode"=>env("MONNIFY_CONTRACT"),
            "customerName"=>$name,
            "expiryDate"=>Carbon::now()->addDays(30)->toDateTimeString()
        ]);
        // Log::info($generateInvoice->json());
        $response= json_decode(json_encode($generateInvoice->json()),FALSE);
        // Log::info($response);
        return $response->responseBody;
    }

 
}