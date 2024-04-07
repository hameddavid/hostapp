<?php

namespace App\Http\Controllers;

use App\HelperClass\PaymentHelper;
use App\HelperClass\Helper1;
use App\Http\Requests\MakePaymentRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Models\Purchase;
use App\Repositories\PaymentRepository;
use App\Repositories\PurchaseRepository;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;


class PaymentController extends Controller
{
    use HttpResponses;


    public function __construct(private UserRepository $userRepo, private PaymentRepository $payRepo, private PurchaseRepository $purchaseRepo)
    {
        
    }

    public function make_payment(MakePaymentRequest $request){
        try{
            $request->validated($request->all());
            $user = $this->userRepo->GetUserByEmail($request->email);
            if($user){
                $invoice_number = Carbon::now()->timestamp."-".$user->id;
                $monifyConfig = PaymentHelper::createInvoice($request->amount,'Desc',$request->email,$user->name );
                $payment_check = Payment::where(['user_id'=>$user->id, 'payment_status'=>'PENDING',
                'amount'=> $request->amount ])->first();
                if( $payment_check ){
                    $payment_check->product_id = $request->product_id;
                    $payment_check->payment_date_time = Carbon::now()->toDateTimeString();
                    $payment_check->invoiceReference = $monifyConfig->invoiceReference;
                    $payment_check->transactionReference = $monifyConfig->transactionReference;
                    $payment_check->url = $monifyConfig->checkoutUrl;
                    $payment_check->account_number = $monifyConfig->accountNumber;
                    $payment_check->save();
                    $purchase_check = Purchase::where(['payment_id' => $payment_check->id, 'purchase_status' => 'PENDING'])->first();
                    $purchase_check->meta_data = $request->metadata;
                    $purchase_check->save();
                    return $this->successResponse([ 'data' => $monifyConfig]);
                }else{
                    // Log new payment
                    $payment =  $this->payRepo->CreatePayment([
                        'user_id' => $user->id,
                        'product_id' => $request->product_id,
                        'amount' => $request->amount,
                        'part_pay' => $request->amount,
                        'invoice_number'=> $invoice_number,
                        'payment_date_time' => Carbon::now()->toDateTimeString(),
                        'invoiceReference' => $monifyConfig->invoiceReference,
                        'transactionReference' => $monifyConfig->transactionReference,
                        'url' => $monifyConfig->checkoutUrl,
                        'account_number' => $monifyConfig->accountNumber]);
                        if($payment["status"]=="OK"){
                            // Log new Purchase
                            $purchase = $this->purchaseRepo->CreatePurchase([
                                'user_id' => $user->id,
                                'product_id' => $request->product_id,
                                'payment_id' =>  $payment["newPayment"]->id,
                                'quantity' => 1,
                                'meta_data' => $request->metadata,
                                'purchase_date' => Carbon::now(),
                                'expiring_date' => Carbon::now(),
                                'invoice_number' => $invoice_number]);
                                if($purchase["status"]=="OK"){
                                    return $this->successResponse([ 'data' => $monifyConfig]);
                                }
                        }
                        
                }
            }
           elseif(!$user){
            $userDetails = $request->only(['name','email','password']);
            // Log New User
            $createUser = $this->userRepo->CreateUser($userDetails);
            if($createUser['status'] == "OK"){
                // Log New Payment
                 $invoice_number = Carbon::now()->timestamp."-".$createUser['user']->id;
                 $monifyConfig = PaymentHelper::createInvoice($request->amount,'Desc',$request->email,$createUser['user']->name );
                 $createPayment = $this->payRepo->CreatePayment([
                    'user_id' => $createUser['user']->id,
                    'product_id' => $request->product_id,
                    'amount' => $request->amount,
                    'invoice_number'=> $invoice_number,
                    'payment_date_time' => Carbon::now()->toDateTimeString(),
                    'invoiceReference' => $monifyConfig->invoiceReference,
                    'transactionReference' => $monifyConfig->transactionReference,
                    'url' => $monifyConfig->checkoutUrl,
                    'account_number' => $monifyConfig->accountNumber
                 ]);
                 if($createPayment["status"] == "OK"){
                    // log New purchase
                    $createPurchase = $this->purchaseRepo->CreatePurchase(
                        [
                            'user_id' => $createUser['user']->id,
                            'product_id' => $request->product_id,
                            'payment_id' =>  $createPayment['newPayment']->id,
                            'quantity' => 1,
                            'meta_data' => $request->metadata,
                            'purchase_date' => Carbon::now(),
                            'expiring_date' => Carbon::now(),
                            'invoice_number' => $invoice_number
                        ]
                        );
                        if($createPurchase["status"] == "OK"){
                            return $this->successResponse($monifyConfig);
                        }
                 }
              
                
            }
           
               
            }
            else{
                return $this->errorResponse('', 'Oops!!, issues with parameter(s) supplied', 401);
            }
        }
        catch(\Throwable $th){
            return $this->errorResponse('','Oops!!, Please try again', 401);
        }
         
        
    }
    
    public function make_second_payment($pay_ref, $email){
        $user = User::where('email',$email)->first();
        $monifyConfig = PaymentHelper::createInvoice($pay_ref->part_pay,'Desc',$email,$user->name);
        $payment = Payment::create([
            'user_id' => $user->id,
            'product_id' => $pay_ref->product_id,
            'amount' => $pay_ref->part_pay,
            'part_pay' => $pay_ref->part_pay,
            'invoice_number'=>$pay_ref->invoice_number,
            'payment_date_time' => Carbon::now()->toDateTimeString(),
            'invoiceReference' => $monifyConfig->invoiceReference,
            'transactionReference' => $monifyConfig->transactionReference,
            'url' => $monifyConfig->checkoutUrl,
            'account_number' => $monifyConfig->accountNumber
        ]);
        return redirect($monifyConfig->checkoutUrl);
    }

    
    public function get_payment_status(Request $request){
        try{
            $reference = $request->get('paymentReference');
            if(!$reference){
                return redirect('https://serversuits.com');
            }
            $check_ref=Payment::where(['invoiceReference'=>$reference,'payment_status'=>'PENDING'])->first();
            if(!$check_ref){
                return redirect('https://serversuits.com');
            }
            $status = PaymentHelper::getTransactionStatus($reference);
            if(!is_string($status) && $status->paymentStatus == 'PAID'){
                $check_ref->payment_status = "successResponse";
                $check_ref->save();
            }
            if($check_ref->part_pay < $check_ref->amount){
                return $this->make_second_payment($check_ref,$check_ref->user->email);
            }
            else{
                if(!is_string($status) && $status->paymentStatus == 'PAID'){
                    $purchase = Purchase::where(['user_id'=>$check_ref->user_id,'product_id'=>$check_ref->product_id,'invoice_number'=>$check_ref->invoice_number])->first();
                    $purchase->purchase_status = "PAID";
                    $purchase->save();
                    return redirect("https://serversuits.com");
                }
                    return redirect("https://serversuits.com");
            }
        }
        catch(\Throwable $th){
            return redirect("https://serversuits.com");
        }
        
        
    }
    
}
