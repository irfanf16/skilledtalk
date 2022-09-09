<?php

namespace App\Repositories\Stripe;

use App\Repositories\Stripe\iStripeRepository;
use Stripe;
class StripeRepository implements iStripeRepository{

   private $stripe;

   public function __construct(){

       $this->stripe=new \Stripe\StripeClient(env('STRIPE_SECRET'));
   }

    public function payment($CardToken)
    {

        $payment=$this->stripe->charges->create([
            "amount" => 50*100,
            "currency" => "usd",
            "source" => $CardToken, // obtained with Stripe.js

        ]);
        return $payment;


    }

    public function captureSessionPrice($token, $price){
        $payment=$this->stripe->charges->create([
            "amount" => (int) $price * 100,
            "currency" => "usd",
            "source" => $token, // obtained with Stripe.js

        ]);
        return $payment;
    }


}
