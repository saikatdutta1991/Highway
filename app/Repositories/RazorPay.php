<?php

namespace App\Repositories;

use App\Repositories\Gateway;
use App\Models\Setting;
use Razorpay\Api\Api;


class RazorPay extends Gateway
{


	public function __construct(Setting $setting)
	{
		$this->setting = $setting;
	}


	public function publickeys()
	{
		return [
            'RAZORPAY_API_KEY' => $this->setting->get('RAZORPAY_API_KEY')
        ];
	}



	public function allKeys()
	{
		return [
            'RAZORPAY_API_KEY' => $this->setting->get('RAZORPAY_API_KEY'), 
            'RAZORPAY_API_SECRET' => $this->setting->get('RAZORPAY_API_SECRET')
        ];
	}


	public function gatewayName()
	{
		return 'RAZORPAY';
    }
    

    /**
     * initiate request
     */
    public function initiate($receipt, $amount, $currency =  'INR')
    {
        $api = new Api(
            $this->allKeys()['RAZORPAY_API_KEY'],
            $this->allKeys()['RAZORPAY_API_SECRET']
        );

        $order = $api->order->create(array('receipt' => $receipt, 'amount' => $amount, 'currency' => $currency)); // Creates order
        
        return $order;
    }



	public function charge($request)
	{
        $api = new Api(
            $this->allKeys()['RAZORPAY_API_KEY'],
            $this->allKeys()['RAZORPAY_API_SECRET']
        );

        try {

            $payment  = $api->payment->fetch($request->payment_id);
            $order = $api->order->fetch($payment->order_id);


            //if payment orderid and reuest pasded order id not same
            if($payment->order_id != $request->order_id) {
                return false;
            }
        
            if($payment->status == 'authorized') {
                $payment = $payment->capture(array('amount'=> $order->amount));
            }
            
            return [
                'success'        => true,
                'transaction_id' => $payment->id,
                'status'         => 'captured',
                'amount'         => $payment->amount / 100,
                'method'         => $payment->method,
                'currency_type'  => $payment->currency,
                'extra'          => [
                    'payment' => $payment->toArray(),
                    'order' => $order->toArray()
                ]
            ];


        } catch(\Exception $e) {
            \Log::info('RAZORPAY_CHARGE_ERROR');
            \Log::info($e->getMessage());
            return false;
        }

        
      
    }
    


    /**
     * initiate refund
     */
    public function refundFull($paymentId)
    {
        $api = new Api(
            $this->allKeys()['RAZORPAY_API_KEY'],
            $this->allKeys()['RAZORPAY_API_SECRET']
        );

        try {
            

            $refund = $api->refund->create(array('payment_id' => $paymentId));
            $payment  = $api->payment->fetch($paymentId);
           
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $payment->status,
                'amount' => $refund->amount / 100,
                'currency_type' => $refund->currency,
                'payment_id' => $refund->payment_id,
                'extra' => [
                    'refund' => $refund->toArray(),
                    'payment' => $payment->toArray()
                ]
            ];


        } catch(\Exception $e) {
            \Log::info('RAZORPAY_REFUND_FULL_ERROR');
            \Log::info($e->getMessage());
            return [
                'success' => false,
                'error_code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }




    /**
     * initiate partial refund
     */
    public function refundPartial($paymentId, $refundAmount)
    {
        $api = new Api(
            $this->allKeys()['RAZORPAY_API_KEY'],
            $this->allKeys()['RAZORPAY_API_SECRET']
        );

        try {
            

            $refund = $api->refund->create(array('payment_id' => $paymentId, 'amount' => $refundAmount * 100));
            $payment  = $api->payment->fetch($paymentId);
           
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $payment->status,
                'amount' => $refund->amount / 100,
                'currency_type' => $refund->currency,
                'payment_id' => $refund->payment_id,
                'extra' => [
                    'refund' => $refund->toArray(),
                    'payment' => $payment->toArray()
                ]
            ];


        } catch(\Exception $e) {
            \Log::info('RAZORPAY_REFUND_PARTIAL_ERROR');
            \Log::info($e->getMessage());
            return [
                'success' => false,
                'error_code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }



}