@extends('layouts.app')

@section('title', '決済成功')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">決済成功</div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <h1 class="display-4">お支払いありがとうございました！</h1>
                    <p class="lead">決済が正常に完了しました。</p>
                </div>

                @if(isset($payment))
                <div class="mt-4">
                    <h5>支払い詳細</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>決済ID</th>
                                <td>{{ $payment->stripe_id }}</td>
                            </tr>
                            <tr>
                                <th>金額</th>
                                <td>{{ number_format($payment->amount) }}円</td>
                            </tr>
                            <tr>
                                <th>決済方法</th>
                                <td>{{ $payment->payment_method }}</td>
                            </tr>
                            <tr>
                                <th>決済日時</th>
                                <td>{{ $payment->created_at->format('Y年m月d日 H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif

                <div class="mt-4 text-center">
                    <a href="{{ route('payment.form') }}" class="btn btn-primary">別の支払いを行う</a>
                    <a href="{{ route('payment.history') }}" class="btn btn-secondary ms-2">支払い履歴を見る</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
