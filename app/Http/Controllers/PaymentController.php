<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    /**
     * Stripe決済セッションを作成する
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCheckoutSession(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            // 商品情報を取得
            $amount = $request->input('amount', 1000); // デフォルトは1000円
            $productName = $request->input('product_name', 'テスト商品');
            
            // 決済セッションを作成
            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $productName,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'metadata' => [
                    'product_id' => $request->input('product_id', '1'),
                    'user_id' => $request->input('user_id', '1'),
                ],
            ]);

            return response()->json([
                'id' => $session->id,
                'url' => $session->url,
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * 支払い成功ページを表示
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        
        // セッションIDが存在する場合、Stripe APIから情報を取得
        if ($sessionId) {
            try {
                $stripe = new StripeClient(config('services.stripe.secret'));
                $session = $stripe->checkout->sessions->retrieve($sessionId);
                
                // 支払い情報を表示するためのデータを渡す
                return view('payment.success', [
                    'session' => $session,
                    'payment' => Payment::where('stripe_id', $sessionId)->first()
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error retrieving checkout session', [
                    'error' => $e->getMessage(),
                    'session_id' => $sessionId
                ]);
            }
        }
        
        // セッションが見つからない場合は一般的な成功メッセージを表示
        return view('payment.success');
    }

    /**
     * 支払いキャンセルページを表示
     *
     * @return \Illuminate\View\View
     */
    public function cancel()
    {
        return view('payment.cancel');
    }
    
    /**
     * 支払い履歴を表示
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(10);
        return view('payment.history', compact('payments'));
    }
    
    /**
     * テスト用支払いフォームを表示
     *
     * @return \Illuminate\View\View
     */
    public function showPaymentForm()
    {
        return view('payment.form', [
            'stripeKey' => config('services.stripe.key')
        ]);
    }
}
