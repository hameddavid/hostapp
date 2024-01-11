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
        session(['part_pay' => $request->part_pay]);
        session(['request' => $request]);
        $user = User::where('email',$request->email)->first();
        if($user){
            $monifyConfig = PaymentHelper::createInvoice($request->amount,'Desc',$request->email,$user->name );

            $payment_check = Payment::where(['user_id'=>$user->id, 'amount'=>$request->amount])->first();
            if( $payment_check ){
                $payment_check->product_id = $request->product_id;
                $payment_check->payment_date_time = Carbon::now()->toDateTimeString();
                $payment_check->invoiceReference = $monifyConfig->invoiceReference;
                $payment_check->transactionReference = $monifyConfig->transactionReference;
                $payment_check->url = $monifyConfig->checkoutUrl;
                $payment_check->account_number = $monifyConfig->accountNumber;
                $payment_check->save();
                $purchase_check = Purchase::where('payment_id', $payment_check->id)->first();
                $purchase_check->meta_data = $request->metadata;
                $purchase_check->save();
                return $this->success([ 'data' => $monifyConfig]);
            }
            $payment = Payment::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'amount' => $request->total_amount,
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
            return $this->success([ 'data' => $monifyConfig]);
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
    
    public function make_second_payment($request){
        $user = User::where('email',$request->email)->first();
        $monifyConfig = PaymentHelper::createInvoice($request->amount,'Desc',$request->email,$user->name);
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
        session(['part_pay' => false]);
        return redirect($monifyConfig->checkoutUrl);
    }

    
    public function get_payment_status(Request $request){
        $reference = $request->get('paymentReference');
        $multiplier = PaymentHelper::getMultiplier();
        if(!$reference){
            return redirect('https://serversuits.com');
        }
        $check_ref=Payment::where(['invoiceReference'=>$reference,'payment_status'=>'PENDING'])->first();
        if(!$check_ref){
            return redirect('https://serversuits.com');
        }
        $status = PaymentHelper::getTransactionStatus($reference);
        if(!is_string($status) && $status->paymentStatus == 'PAID'){
            session('part_pay')? $check_ref->part_pay = $status->amountPaid/$multiplier : null;
            $check_ref->payment_status = "SUCCESS";
            $check_ref->save();
        }
        if(session('part_pay')){
            return $this->make_second_payment(session('request'));
        }
        return redirect("https://serversuits.com");
    }
    
}
