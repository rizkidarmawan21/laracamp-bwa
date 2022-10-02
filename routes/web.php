<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DiscountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');



Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success')->middleware(['auth'])->middleware('ensureUserRole:user');
Route::get('checkout/{camp:slug}', [CheckoutController::class, 'create'])->name('checkout.create')->middleware(['auth'])->middleware('ensureUserRole:user');
Route::post('checkout/{camp}', [CheckoutController::class, 'store'])->name('checkout.store')->middleware(['auth'])->middleware('ensureUserRole:user');

Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware(['auth']);
// Route::get('dashboard/checkout/invoice/{checkout}',[CheckoutController::class,'invoice'])->name('user.checkout.invoice')->middleware(['auth']);

// user dashboard
Route::prefix('user/dashboard')->namespace('User')->middleware(['ensureUserRole:user', 'auth'])->name('user.')->group(function () {
    Route::get('/', [UserDashboard::class, 'index'])->name('dashboard');
});

// admin dashboard
Route::prefix('admin/dashboard')->name('admin.')->middleware(['ensureUserRole:admin', 'admin'])->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::post('checkout/{checkout}', [AdminDashboard::class, 'update'])->name('checkout.update');

    // admin discount
    // Route::get('discount',[DiscountController::class,'index'])->name('discount');
    // Route::get('discount/create',[DiscountController::class,'create'])->name('discount.create');

    Route::resource('discount',DiscountController::class);
});


// sosialite routes
Route::get('sign-in-google', [UserController::class, 'google'])->name('user.login.google');
Route::get('auth/google/callback', [UserController::class, 'handleProviderCallback'])->name('user.google.callback');

Route::get('payment/success', [CheckoutController::class, 'midtransCallback']);
Route::post('payment/success', [CheckoutController::class, 'midtransCallback']);

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');



require __DIR__ . '/auth.php';
