<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\Checkout\Paid;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index()
    {
        $checkout = Checkout::with('Camp')->get();
        return view('admin.dashboard', compact('checkout'));
    }

    public function update(Request $request, Checkout $checkout)
    {
        $checkout->is_paid = true;
        $checkout->save();

        Mail::to($checkout->User->email)->send(new Paid($checkout));

        $request->session()->flash('success',"Checkout with ID {$checkout->id} has ben updated");
        return redirect()->route('admin.dashboard');
    }
}
