<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    //Stripe Checkout
    public function checkout(Item $item)
    {
        $user = Auth::user();

        if($item->purchase){
            return redirect()->route('items.index');
        }

        session(['payment_method' => request('payment_method')]);
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([

            'customer_email'=>$user->email,
            'payment_method_types'=>['card'],
            'line_items'=>[
                [
                    'price_data'=>[
                        'currency'=>'jpy',
                        'product_data'=>[
                            'name'=>$item->name
                        ],
                        'unit_amount'=>$item->price
                    ],
                    'quantity'=>1
                ]
            ],

            'mode'=>'payment',
            'success_url'=>route('payment.success',['item'=>$item]),
            'cancel_url'=>route('items.show',$item)

        ]);

        return redirect($session->url);
    }

    //Stripe Success
    public function success(Item $item)
    {
        $user = Auth::user();

        if(!$item->purchase){

            Purchase::create([
                'user_id'=>$user->id,
                'item_id'=>$item->id,
                'payment_method'=>session('payment_method'),
                'postal_code'=>$user->postal_code,
                'address'=>$user->address,
                'building_name'=>$user->building_name
            ]);
        }

        return redirect()->route('items.index');
    }
}