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
        $login = SELF::monnifyLogin();
        $generateInvoice = Http::withHeaders([
            "Authorization"=>"Bearer {$login->responseBody->accessToken}"
        ])->post(env("MONNIFY_LIVE_ENDPOINT")."/api/v1/invoice/create",[
            "redirectUrl" => 'https://api.serversuits.com/get-transaction-status',
            "amount"=>$amount * SELF::getMultiplier(),
            "invoiceReference"=>time(),
            "description"=>$description,
            "currencyCode"=>"NGN",
            "customerEmail"=>$email,
            "contractCode"=>env("MONNIFY_CONTRACT"),
            "customerName"=>$name,
            "expiryDate"=>Carbon::now()->addDays(30)->toDateTimeString()
        ]);
        $response= json_decode(json_encode($generateInvoice->json()),FALSE);
        return $response->responseBody;
    }
    
    public static function getTransactionStatus($reference){
        $login = SELF::monnifyLogin();
        $status = Http::withHeaders([
            "Authorization"=>"Bearer {$login->responseBody->accessToken}"
        ])->get(env("MONNIFY_LIVE_ENDPOINT")."/api/v2/transactions/",[
            "transactionReference " => $reference,
        ]);
        $response= json_decode(json_encode($status->json()),FALSE);
        if(isset($response->error)){
            return $response->error;
        }
        elseif($response->responseMessage == "success" && $response->requestSuccessful == true){
            return $response->responseBody;
        }
        else{
            return null;
        }
    }
    
    public static function getMultiplier(){
        $multiplier = 1600.00;
        return $multiplier;
    }

    public static function payStack($amount, $description, $email, $name) {
        info($amount * SELF::getMultiplier());
        $request = Http::withHeaders([
             "Authorization"=>"Bearer ". env('PAYSTACK_SERCET_KEY'),
             'Content-Type'=> 'application/json'
        ])->post('https://api.paystack.co/transaction/initialize', [ 
            'email' => $email,
            'amount' =>($amount * SELF::getMultiplier()) * 100,
        ]);
        
        $response = json_decode(json_encode($request->json()),FALSE);

        $data = $response->data;
        $array = [
            'invoiceReference' => $data->reference,
            'transactionReference' => $data->reference,
            'checkoutUrl' => $data->authorization_url,
            'accountNumber' => 'PayStack'
        ];

        return json_decode(json_encode($array));
    }

    public static function cancelIvoiceMonnify() {
        $login = SELF::monnifyLogin();
        $response = Http::withHeaders([
            "Authorization"=>"Bearer {$login->responseBody->accessToken}",
            "Accept" => "application/json",
            'Content-Type'=> 'application/json'
        ])->delete(env("MONNIFY_LIVE_ENDPOINT")."/api/v1/invoice/1740674266/cancel");
        return $response;
    }
 
}