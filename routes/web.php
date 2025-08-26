<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentAuthorizationController;
use App\Http\Controllers\AuthorizePaymentController;


// Public homepage (via HomeController)
Route::get('/', [HomeController::class, 'home'])->name('home');


// Laravel Auth
Auth::routes();

// Member Registration
Route::get('/register', [MemberController::class, 'createStripe'])->name('members.create');
Route::post('/register', [MemberController::class, 'storeStripe'])->name('members.store');

Route::get('/offline/register', [MemberController::class, 'createMemberOffline'])->name('members.create.offline');
Route::post('/offline/register', [MemberController::class, 'storeMemberOffline'])->name('members.store.offline');

//Route::get('/register', [MemberController::class, 'createAuthorize'])->name('members.create');
//Route::post('/register', [MemberController::class, 'storeAuthorize'])->name('members.store');

// Member Profile + Receipt
Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
Route::get('/members/{member}/receipt', [MemberController::class, 'receipt'])->name('members.receipt');

Route::get('/member/{uuid}', [MemberController::class, 'viewByUuid'])->name('members.view');

/* // Payment Authorization Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/required', [PaymentAuthorizationController::class, 'paymentRequired'])->name('payment.required');
    Route::get('/payment/history', [PaymentAuthorizationController::class, 'paymentHistory'])->name('payment.history');
    Route::post('/payment/verify', [PaymentAuthorizationController::class, 'verifyPayment'])->name('payment.verify');
    Route::get('/payment/check', [PaymentAuthorizationController::class, 'checkAuthorization'])->name('payment.check');
    Route::post('/payment/resend-verification', [PaymentAuthorizationController::class, 'resendVerification'])->name('payment.resend-verification');
    Route::post('/payment/cancel', [PaymentAuthorizationController::class, 'cancelPayment'])->name('payment.cancel');
    
    // Member payment authorization routes
    Route::get('/members/payment/status', [MemberController::class, 'getPaymentStatus'])->name('members.payment.status');
    Route::get('/members/payment/check', [MemberController::class, 'checkPaymentAuthorization'])->name('members.payment.check');
    Route::post('/members/payment/resend-verification', [MemberController::class, 'resendPaymentVerification'])->name('members.payment.resend-verification');
});

// Protected routes that require payment authorization
Route::middleware(['auth', 'payment.authorization'])->group(function () {
    Route::get('/protected-content', function () {
        return view('protected.content');
    })->name('protected.content');
}); */

Route::get('/authorize-payment', [AuthorizePaymentController::class, 'showForm'])->name('authorize.form');
Route::post('/authorize-payment', [AuthorizePaymentController::class, 'makePayment'])->name('authorize.pay');