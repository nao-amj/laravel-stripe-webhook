@extends('layouts.app')

@section('title', '支払いフォーム')

@section('head-scripts')
    <!-- Stripe JavaScript SDK -->
    <script src="https://js.stripe.com/v3/"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Stripeテスト決済</div>

            <div class="card-body">
                <form id="payment-form">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">商品名</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" value="テスト商品" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">金額（円）</label>
                        <input type="number" class="form-control" id="amount" name="amount" value="1000" min="100" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button id="checkout-button" type="submit" class="btn btn-primary btn-lg">
                            Stripeで決済する
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <div class="alert alert-info">
                <h5>テストカード情報</h5>
                <ul>
                    <li>カード番号: 4242 4242 4242 4242</li>
                    <li>有効期限: 未来の任意の日付</li>
                    <li>CVC: 任意の3桁の数字</li>
                    <li>郵便番号: 任意の数字</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stripe = Stripe('{{ $stripeKey }}');
        const form = document.getElementById('payment-form');
        const checkoutButton = document.getElementById('checkout-button');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            // ボタンを無効化して二重送信を防止
            checkoutButton.disabled = true;
            checkoutButton.textContent = '処理中...';

            // フォームデータ取得
            const formData = new FormData(form);
            const data = {
                product_name: formData.get('product_name'),
                amount: formData.get('amount')
            };

            try {
                // チェックアウトセッション作成リクエスト
                const response = await fetch('{{ route("payment.create.session") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error('サーバーエラーが発生しました');
                }

                const session = await response.json();
                
                // Stripe Checkoutセッションにリダイレクト
                window.location.href = session.url;
                
            } catch (error) {
                console.error('Error:', error);
                alert('エラーが発生しました: ' + error.message);
                
                // ボタンを再度有効化
                checkoutButton.disabled = false;
                checkoutButton.textContent = 'Stripeで決済する';
            }
        });
    });
</script>
@endsection
