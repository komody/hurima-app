# フリマアプリ

## 環境構築

### Dockerビルド

```bash
git clone https://github.com/komody/hurima-app
cd hurima-app
docker compose up -d --build
```

### Laravel環境構築

```bash
docker compose exec php bash
cd /var/www
composer install
cp .env.example .env
exit
```

`.env` の環境変数を以下のように設定してください。

```
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@example.com
```

`Stripe ダッシュボード` の テストモード で取得した STRIPE_KEY と STRIPE_SECRET を .env に設定してください

```
STRIPE_KEY=（Stripeダッシュボードで取得した公開可能キー）
STRIPE_SECRET=（Stripeダッシュボードで取得したシークレットキー）
```

### アプリケーションキーの生成

```bash
docker compose exec php php artisan key:generate
```

### ストレージリンクの作成

```bash
docker compose exec php php artisan storage:link
```

### データベースの作成

```bash
docker compose exec mysql mysql -u root -proot -e "CREATE DATABASE laravel_db;"
```

### データベースマイグレーションの実行

```bash
docker compose exec php php artisan migrate
```

### データベースシーディングの実行

```bash
docker compose exec php php artisan db:seed
```

### フロントエンド依存関係のインストール

```bash
docker compose exec php npm install
```

### フロントエンドのビルド

```bash
docker compose exec php npm run dev
```

## テスト実行

### 準備（初回のみ）

```bash
docker compose exec php bash
cd /var/www
cp .env.testing.example .env.testing
exit
```

`.env.testing` の `APP_KEY` を `.env` の `APP_KEY` で上書きしてください。

### 初回のみ: laravel_test を作成

```bash
docker compose exec mysql mysql -u root -proot -e "CREATE DATABASE laravel_test"
```

### テスト実行

```bash
docker compose exec php php artisan test
```

## テストユーザー（ログイン用）

| 項目 | 値 |
|------|-----|
| メールアドレス | test@example.com |
| パスワード | password |
| 名前 | テストユーザー |

## 使用技術(実行環境)

- PHP 8.1
- Laravel 8.x
- MySQL 8.0.26
- nginx 1.21.1
- Node.js 18.x
- Laravel Mix（Sass）
- Stripe（決済）

## ER図

![ER図](./er-diagram.png)

## テーブル構造（詳細）

### users（ユーザー）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| name | string(255) | NO | 名前 |
| email | string(255) | NO | メールアドレス（ユニーク） |
| email_verified_at | timestamp | YES | メール認証日時 |
| first_login_email_verified_at | timestamp | YES | 初回ログイン時のメール認証日時 |
| password | string(255) | NO | パスワード |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### accounts（アカウント）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| user_id | bigint | NO | ユーザーID（外部キー → users.id） |
| name | string(255) | NO | 名前 |
| profile_image | string(255) | YES | プロフィール画像 |
| postal_code | string(255) | YES | 郵便番号 |
| address | string(255) | YES | 住所 |
| building | string(255) | YES | 建物名 |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### items（商品）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| name | string(255) | NO | 商品名 |
| image_url | string(255) | NO | 画像URL |
| brand_name | string(255) | YES | ブランド名 |
| price | integer | NO | 価格 |
| description | text | NO | 説明 |
| seller_id | bigint | NO | 出品者ID（外部キー → users.id） |
| buyer_id | bigint | YES | 購入者ID（外部キー → users.id） |
| condition_id | bigint | NO | 状態ID（外部キー → conditions.id） |
| sold_out | boolean | NO | 売り切れフラグ（デフォルト: false） |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### conditions（商品状態マスタ）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| name | string(255) | NO | 状態名 |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### categories（カテゴリマスタ）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| name | string(255) | NO | カテゴリ名 |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### category_item（商品とカテゴリの中間テーブル）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| item_id | bigint | NO | 商品ID（外部キー → items.id） |
| category_id | bigint | NO | カテゴリID（外部キー → categories.id） |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### likes（いいね）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| user_id | bigint | NO | ユーザーID（外部キー → users.id） |
| item_id | bigint | NO | 商品ID（外部キー → items.id） |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### comments（コメント）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| user_id | bigint | NO | ユーザーID（外部キー → users.id） |
| item_id | bigint | NO | 商品ID（外部キー → items.id） |
| content | text | NO | コメント本文 |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

### orders（注文）

| カラム | 型 | NULL | 説明 |
|--------|------|------|------|
| id | bigint | NO | 主キー |
| user_id | bigint | NO | ユーザーID（外部キー → users.id） |
| item_id | bigint | NO | 商品ID（外部キー → items.id） |
| payment_method | string(255) | NO | 支払い方法 |
| delivery_postal_code | string(255) | NO | 配送先郵便番号 |
| delivery_address | string(255) | NO | 配送先住所 |
| delivery_building | string(255) | YES | 配送先建物名 |
| created_at | timestamp | YES | 作成日時 |
| updated_at | timestamp | YES | 更新日時 |

## URL

- 開発環境: http://localhost/
- 会員登録: http://localhost/register
- ログイン: http://localhost/login
- phpMyAdmin: http://localhost:8080/
- MailHog（メール確認）: http://localhost:8025/
