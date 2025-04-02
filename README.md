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

## Webhookテスト方法

ローカル開発環境でStripe Webhookをテストするには、以下の手順に従ってください：

### 1. Stripe CLIのインストール

[Stripe CLI](https://stripe.com/docs/stripe-cli)をインストールします：

macOS (Homebrew):
```bash
brew install stripe/stripe-cli/stripe
```

Windows (Chocolatey):
```bash
choco install stripe-cli
```

### 2. Stripe CLIでログイン

```bash
stripe login
```

### 3. ローカルWebhookの転送を開始

```bash
stripe listen --forward-to http://localhost:8000/api/webhook/stripe
```

このコマンドを実行すると、Webhook Signing Secretが表示されます。これを`.env`ファイルの`STRIPE_WEBHOOK_SECRET`に設定します。

### 4. イベントをトリガーしてテスト

別のターミナルで以下のコマンドを実行して、特定のイベントをトリガーできます：

```bash
# checkout.session.completedイベントをトリガー
stripe trigger checkout.session.completed

# payment_intent.succeededイベントをトリガー
stripe trigger payment_intent.succeeded
```

これにより、ローカル環境でWebhookの動作をテストできます。

## ディレクトリ構造

主な実装ファイル：

- `app/Http/Controllers/StripeWebhookController.php` - Webhookハンドリング
- `app/Http/Controllers/PaymentController.php` - 決済処理
- `app/Models/Payment.php` - 決済情報モデル
- `routes/api.php` - WebhookのAPIルート
- `routes/web.php` - Web側のルート
- `resources/views/payment/` - 決済関連のビュー

## ライセンス

[MIT](https://opensource.org/licenses/MIT)
