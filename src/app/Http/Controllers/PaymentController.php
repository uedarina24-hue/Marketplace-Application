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

        if ($item->purchase) {
            return redirect()->route('items.index');
        }

        $paymentMethod = request('payment_method');
        if (!$paymentMethod) {
            return redirect()->route('purchase.index', $item)
                ->with('error', '支払い方法が選択されていません');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'customer_email' => $user->email,
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name
                        ],
                        'unit_amount' => $item->price
                    ],
                    'quantity' => 1
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('payment.success', [
                'item' => $item->id,
                'payment_method' => $paymentMethod
            ]),
            'cancel_url' => route('items.show', $item),
            'metadata' => [
                'payment_method' => $paymentMethod,
                'item_id' => $item->id
            ]
        ]);

        return redirect($session->url);
    }

    //Stripe Success
    public function success(Item $item)
    {
        $user = Auth::user();

        $paymentMethod = request('payment_method');

        if (!$paymentMethod) {
            return redirect()->route('purchase.index', $item)
                ->with('error','支払い方法が選択されていません');
        }

        if (!$item->purchase) {
            Purchase::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method' => $paymentMethod,
                'postal_code' => $user->postal_code,
                'address' => $user->address,
                'building_name' => $user->building_name
            ]);

            session()->forget('payment_method');
        }

        return redirect()->route('items.index');
    }
}