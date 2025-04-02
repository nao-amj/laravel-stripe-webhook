# Laravel Stripe Webhook

LaravelとStripeを連携し、Webhookで決済完了イベントを受け取るアプリケーションです。

## 機能

- Stripe決済連携
- 決済完了Webhookのイベントハンドリング
- 決済履歴の保存と閲覧

## 必要条件

- PHP 8.1以上
- Composer
- Laravel 10.x
- Stripeアカウント

## インストール方法

```bash
# リポジトリのクローン
git clone https://github.com/your-username/laravel-stripe-webhook.git
cd laravel-stripe-webhook

# 依存パッケージのインストール
composer install

# 環境ファイルの設定
cp .env.example .env
php artisan key:generate

# データベース設定後にマイグレーション実行
php artisan migrate
```

## Stripe設定

1. [Stripe Dashboard](https://dashboard.stripe.com/)にログイン
2. APIキーを取得し、`.env`ファイルに設定
   ```
   STRIPE_KEY=your_stripe_publishable_key
   STRIPE_SECRET=your_stripe_secret_key
   STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
   ```
3. StripeダッシュボードでWebhookエンドポイントを設定
   - エンドポイントURL: `https://your-domain.com/api/webhook/stripe`
   - イベント: `checkout.session.completed`, `payment_intent.succeeded`など

## 使用方法

アプリケーションを起動し、決済フローを実行します：

```bash
php artisan serve
```

## ライセンス

[MIT](https://opensource.org/licenses/MIT)
