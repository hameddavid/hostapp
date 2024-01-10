<?php

namespace App\Http\Controllers;

use App\HelperClass\PaymentHelper;
use App\HelperClass\Helper1;
use App\Http\Requests\MakePaymentRequest;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Models\Purchase;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;


class PaymentController extends Controller
{
    use HttpResponses;

    public function make_payment(MakePaymentRequest $request){
    
        $request->validated($request->all());
        
        $user = User::where('email',$request->email)->first();
        if($user){
            $monifyConfig = PaymentHelper::createInvoice($request->amount,'Desc',$request->email,$user->name );
            $payment = Payment::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'amount' => $request->amount,
                'payment_date_time' => Carbon::now()->toDateTimeString(),
                'invoiceReference' => $monifyConfig->invoiceReference,
                'transactionReference' => $monifyConfig->transactionReference,
                'url' => $monifyConfig->checkoutUrl,
                'account_number' => $monifyConfig->accountNumber
            ]);
            // log purchase
            $purchase = Purchase::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'payment_id' =>  $payment->id,
                'quantity' => 1,
                'meta_data' => $request->metadata,
                'purchase_date' => Carbon::now(),
                'expiring_date' => Carbon::now(),
                'invoice_number' => Carbon::now()->timestamp."-".$user->id
            ]);
            // Return payment info from PaymentHelper
            // $request->amount,$request->metadata,$request->email,$user->name
            return $this->success([ 'data' => $monifyConfig->checkoutUrl]);
        }
        // elseif(!$user){
        //     $name = explode('@', $request->email);
        //     $password = Helper1::generateSixRandomCharacter();
        //     $user = User::create([
        //         'name' => $name[0],
        //         'email' => $request->email,
        //         'password' => Hash::make($password)
        //     ]);

        //     $payment = Payment::create([

        //   ]);
        // }
        // else{}

    }

    
}
