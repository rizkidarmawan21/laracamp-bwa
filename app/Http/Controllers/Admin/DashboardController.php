<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->session()->flash('success',"Checkout with ID {$checkout->id} has ben updated");
        return redirect()->route('admin.dashboard');
    }
}
