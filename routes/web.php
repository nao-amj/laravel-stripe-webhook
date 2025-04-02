<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 決済関連のルート
Route::prefix('payment')->group(function () {
    // テスト用支払いフォーム
    Route::get('/form', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    
    // Stripe Checkout セッション作成
    Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])->name('payment.create.session');
    
    // 支払い成功・キャンセル
    Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    
    // 支払い履歴
    Route::get('/history', [PaymentController::class, 'history'])->name('payment.history');
});
