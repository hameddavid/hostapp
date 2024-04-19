<?php

namespace App\Repositories;

use App\Interfaces\IPaymentRepository;
use App\Models\Payment;

class PaymentRepository implements IPaymentRepository{


    public function CreatePayment(array $userPayment)
    {
        $createNewPayment = new Payment();
        $createNewPayment->user_id = $userPayment['user_id'];
        $createNewPayment->product_id = $userPayment['product_id'];
        $createNewPayment->amount = $userPayment['amount'];
        $createNewPayment->invoice_number = $userPayment['invoice_number'];
        $createNewPayment->payment_date_time = $userPayment['payment_date_time'];
        $createNewPayment->invoiceReference = $userPayment['invoiceReference'];
        $createNewPayment->transactionReference = $userPayment['transactionReference'];
        $createNewPayment->url = $userPayment['url'];
        $createNewPayment->account_number = $userPayment['account_number'];
        $createNewPayment->save();
        if($createNewPayment){
            return ["newPayment" => $createNewPayment, "status" => "OK"];
        }

        return ["newPayment" => "", "status" => "NOK"];
    }

    public function getUserPayment($userId, $paymentId) {
        return Payment::where([
            'payment_id' => $paymentId,
            'user_id' => $userId
        ])->first();
    }

    public function getUserPayments($userId) {
        return Payment::where('user_id', $userId)->get();
    }

}