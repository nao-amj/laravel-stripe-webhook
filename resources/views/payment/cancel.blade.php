@extends('layouts.app')

@section('title', '決済キャンセル')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning">決済キャンセル</div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <h2>お支払いがキャンセルされました</h2>
                    <p class="lead">カード情報は請求されておりません。</p>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('payment.form') }}" class="btn btn-primary">もう一度試す</a>
                    <a href="{{ url('/') }}" class="btn btn-secondary ms-2">ホームに戻る</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
