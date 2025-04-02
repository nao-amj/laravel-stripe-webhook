@extends('layouts.app')

@section('title', '支払い履歴')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">支払い履歴</div>

            <div class="card-body">
                @if($payments->isEmpty())
                    <div class="alert alert-info">
                        支払い履歴がありません。
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Stripe ID</th>
                                    <th>金額</th>
                                    <th>ステータス</th>
                                    <th>決済方法</th>
                                    <th>日時</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->stripe_id }}</td>
                                    <td>{{ number_format($payment->amount) }}円</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ $payment->status }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>{{ $payment->created_at->format('Y/m/d H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('payment.form') }}" class="btn btn-primary">新規支払いを行う</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
