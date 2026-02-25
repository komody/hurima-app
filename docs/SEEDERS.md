# シーダーファイル解説

このドキュメントでは、データベースシーダーの構成と各シーダーの役割を説明します。

## シーダー実行順序

`DatabaseSeeder` で以下の順序で実行されます：

1. **UsersTableSeeder** - ユーザーデータ（認証用）
2. **AccountsTableSeeder** - アカウントデータ（プロフィール・住所等）
3. **ConditionsTableSeeder** - 商品コンディション（マスタ）
4. **CategoriesTableSeeder** - カテゴリ（マスタ）
5. **ItemTableSeeder** - 商品データ
6. **CategoryItemTableSeeder** - 商品とカテゴリの紐付け（中間テーブル）

※ 依存関係があるため、この順序は変更しないでください。

---

## UsersTableSeeder

認証に必要な `users` テーブルのデータを登録します。

- **登録データ**: テストユーザー（test@example.com / password）
- **テーブル**: users

---

## AccountsTableSeeder

プロフィール情報を格納する `accounts` テーブルのデータを登録します。

- **登録データ**: テストユーザーに紐づくアカウント情報（名前、郵便番号、住所）
- **テーブル**: accounts
- **依存**: UsersTableSeeder（user_id で users を参照）

---

## CategoryItemTableSeeder 詳細解説

商品（items）とカテゴリ（categories）は **多対多** の関係です。  
中間テーブル `category_item` に紐付けデータを登録するシーダーです。

### 役割

- `ItemTableSeeder` で作成された商品に、必須のカテゴリを紐付ける
- 商品出品時に `category_ids` が必須のため、シードデータにもカテゴリが必要

### 処理の流れ

```
1. items テーブルから 商品名 => ID のマッピングを取得
2. categories テーブルから カテゴリ名 => ID のマッピングを取得
3. itemCategoryMap に従い、category_item テーブルにレコードを挿入
```

### 商品とカテゴリの紐付け一覧

| 商品名 | カテゴリ |
|--------|----------|
| 腕時計 | アクセサリー, メンズ |
| HDD | 家電 |
| 玉ねぎ3束 | キッチン |
| 革靴 | ファッション, メンズ |
| ノートPC | 家電 |
| マイク | 家電 |
| ショルダーバッグ | ファッション, レディース |
| タンブラー | キッチン |
| コーヒーミル | キッチン |
| メイクセット | コスメ |

### コードのポイント

- **商品名・カテゴリ名で検索**: ID に依存せず、名前でマッピングするため柔軟
- **存在チェック**: `isset()` で商品・カテゴリの存在を確認し、見つからない場合はスキップ
- **複数カテゴリ対応**: 1商品に複数カテゴリを紐付け可能（配列で指定）

### 中間テーブル構造（category_item）

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | 主キー |
| item_id | bigint | 商品ID（外部キー） |
| category_id | bigint | カテゴリID（外部キー） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

---

## Docker 環境での実行方法

```bash
# 全シーダー実行
make seed

# マイグレーションリセット + 全シーダー実行（開発初期やDB初期化時）
make seed-fresh
```

直接コマンドを実行する場合：

```bash
docker-compose exec php php artisan db:seed
docker-compose exec php php artisan migrate:fresh --seed
```
