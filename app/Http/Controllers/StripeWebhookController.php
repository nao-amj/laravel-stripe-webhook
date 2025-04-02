<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Stripe\Stripe;

class StripeWebhookController extends Controller
{
    /**
     * Stripeからのwebhookを処理する
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        // Stripeキーの設定
        Stripe::setApiKey(config('services.stripe.secret'));
        
        // リクエストからペイロードを取得
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');
        
        try {
            // Webhookの署名を検証
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            
            // イベントタイプに基づいて処理を振り分け
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event);
                    break;
                    
                case 'checkout.session.async_payment_succeeded':
                    $this->handleAsyncPaymentSucceeded($event);
                    break;
                
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event);
                    break;
                
                default:
                    // その他のイベントは記録するだけ
                    Log::info('Unhandled Stripe event', ['type' => $event->type]);
            }
            
            return response()->json(['status' => 'success']);
            
        } catch (SignatureVerificationException $e) {
            // 署名検証エラー
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
            
        } catch (\Exception $e) {
            // その他のエラー
            Log::error('Webhook error', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * checkout.session.completedイベントを処理する
     * 
     * @param Event $event
     * @return void
     */
    protected function handleCheckoutSessionCompleted(Event $event)
    {
        $session = $event->data->object;
        
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'customer' => $session->customer,
            'amount_total' => $session->amount_total,
            'payment_status' => $session->payment_status,
        ]);
        
        // 支払い情報をデータベースに保存
        // 非同期決済の場合はここでは保留状態として記録する
        $status = $session->payment_status === 'paid' ? 'completed' : 'pending';
        
        Payment::create([
            'stripe_id' => $session->id,
            'customer_id' => $session->customer,
            'amount' => $session->amount_total / 100, // セントから円に変換
            'status' => $status,
            'payment_method' => $session->payment_method_types[0] ?? 'unknown',
            'metadata' => json_encode($session->metadata ?? []),
        ]);
    }
    
    /**
     * checkout.session.async_payment_succeededイベントを処理する
     * 
     * @param Event $event
     * @return void
     */
    protected function handleAsyncPaymentSucceeded(Event $event)
    {
        $session = $event->data->object;
        
        Log::info('Async payment succeeded', [
            'session_id' => $session->id,
            'customer' => $session->customer,
            'amount_total' => $session->amount_total,
        ]);
        
        // 既存の支払い情報を完了状態に更新
        Payment::where('stripe_id', $session->id)
            ->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);
    }
    
    /**
     * payment_intent.succeededイベントを処理する
     * 
     * @param Event $event
     * @return void
     */
    protected function handlePaymentIntentSucceeded(Event $event)
    {
        $paymentIntent = $event->data->object;
        
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
        ]);
        
        // 既存の支払い情報を更新するか新規作成
        Payment::updateOrCreate(
            ['stripe_id' => $paymentIntent->id],
            [
                'customer_id' => $paymentIntent->customer,
                'amount' => $paymentIntent->amount / 100,
                'status' => 'completed',
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                'metadata' => json_encode($paymentIntent->metadata ?? []),
            ]
        );
    }
}
