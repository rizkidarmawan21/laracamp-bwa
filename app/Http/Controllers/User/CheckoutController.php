<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Checkout\Store;
use App\Mail\Checkout\AfterCheckout;
use App\Models\Camp;
use App\Models\Checkout;
use App\Models\Discount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Str;

use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class CheckoutController extends Controller
{

    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVERKEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        Config::$is3ds = env('MIDTRANS_IS_3DS');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Camp $camp, Request $request)
    {
        if ($camp->isRegistered) {
            $request->session()->flash('error', "You already registred on {$camp->title} camp.");
            return redirect()->route('user.dashboard');
        }

        return view('checkout', compact('camp'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request, Camp $camp)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['camp_id'] = $camp->id;

        $user = Auth::user();
        $user->occupation = $data['occupation'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->save();

        // checkout discount
        if ($request->discount) {
            $discount = Discount::whereCode($request->discount)->first();
            $data['discount_id'] = $discount->id;
            $data['discount_percentage'] = $discount->percentage;
        }

        $checkout = Checkout::create($data);

        $url = $this->getSnapRedirect($checkout);

        // send email
        Mail::to(Auth::user()->email)->send(new AfterCheckout($checkout));

        // return redirect()->route('checkout.success');
        return redirect($url);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function show(Checkout $checkout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function edit(Checkout $checkout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Checkout $checkout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Checkout  $checkout
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checkout $checkout)
    {
        //
    }

    public function success()
    {
        return view('success_checkout');
    }

    public function invoice(Checkout $checkout)
    {
        return $checkout;
    }

    public function getSnapRedirect(Checkout $checkout)
    {
        $orderId = $checkout->id . '-' . Str::random(5);
        $checkout->midtrans_booking_code = $orderId;


        $items_details[] = [
            'id'       => $orderId,
            'price'    => $checkout->Camp->price * 1000,
            'quantity' => 1,
            'name'     => "Payment for {$checkout->camp->title} Camp"
        ];

        $discountPrice = 0;
        if ($checkout->Discount) {
            $discountPrice = ($checkout->Camp->price * 1000) * ($checkout->discount->percentage / 100);
            $items_details[] = [
                'id'       => $checkout->discount->code,
                'price'    => -$discountPrice,
                'quantity' => 1,
                'name'     => "Discount {$checkout->discount->name} ({$checkout->discount->percentage} %)"
            ];
        }

        $total = ($checkout->Camp->price * 1000) - $discountPrice;

        $transaction_details = [
            'order_id'     => $orderId,
            'gross_amount' => $total
        ];


        $userData = [
            'first_name'   => $checkout->user->name,
            'last_name'    => "",
            'address'      => $checkout->user->address,
            'city'         => "",
            'postal_code'  => "",
            'phone'        => $checkout->user->phone,
            'country_code' => "IDN"
        ];

        $customer_details = [
            'first_name'       => $checkout->user->name,
            'last_name'        => "",
            'email'            => $checkout->user->email,
            'billing_address'  => $userData,
            'shipping_address' => $userData,
        ];

        $midtrans_params = [
            'transaction_details' => $transaction_details,
            'customer_details'    => $customer_details,
            'item_details'        => $items_details

        ];


        try {
            $paymentUrl = Snap::createTransaction($midtrans_params)->redirect_url;
            $checkout->midtrans_url = $paymentUrl;
            $checkout->total = $total;
            $checkout->save();

            return $paymentUrl;
        } catch (Exception $e) {
            return false;
        }
    }

    public function midtransCallback(Request $request)
    {

        // dd($request);
        $notif = $request->method() == 'POST' ? new Notification() : Transaction::status($request->order_id);
        // dd($notif);
        // $notif = new Notification();

        $transaction_status = $notif->transaction_status;
        $fraud = $notif->fraud_status;

        $checkout_id = explode('-', $notif->order_id)[0];
        $checkout = Checkout::find($checkout_id);

        if ($transaction_status == 'capture') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'challenge'
                $checkout->payment_status = 'pending';
            } else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'success'
                $checkout->payment_status = 'paid';
            }
        } else if ($transaction_status == 'cancel') {
            if ($fraud == 'challenge') {
                // TODO Set payment status in merchant's database to 'failure'
                $checkout->payment_status = 'failed';
            } else if ($fraud == 'accept') {
                // TODO Set payment status in merchant's database to 'failure'
                $checkout->payment_status = 'failed';
            }
        } else if ($transaction_status == 'deny') {
            // TODO Set payment status in merchant's database to 'failure'
            $checkout->payment_status = 'failed';
        } else if ($transaction_status == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $checkout->payment_status = 'paid';
        } else if ($transaction_status == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $checkout->payment_status = 'pending';
        } else if ($transaction_status == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $checkout->payment_status = 'failed';
        }

        $checkout->save();

        return view('success_checkout');
    }
}
