# Marketplace-Application
## 概要
本アプリはフリマアプリを模したマーケットプレイスです。
ユーザーは商品を出品・購入・コメント・いいねすることができます。

## 環境構築

### Docker ビルド
1. git clone git@github.com:uedarina24-hue/Marketplace-Application.git
2. docker-compose up -d --build

### Laravel 環境構築
1. docker-compose exec php bash
2. composer install
3. cp .env.example .env
4. .env ファイルの変更

```
# DB 設定
DB_HOST=mysql
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

# メール送信設定（開発環境 MailHog）
MAIL_MAILER=smtp
MAIL_HOST=mailhog # Dockerコンテナ名に依存
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="test@example.com"
MAIL_FROM_NAME="Marketplace App"
```
※ MAIL_FROM_ADDRESS は任意のメールアドレスで問題ありません。
開発環境では実際の送信は行われず、MailHogで確認できます。

5. php artisan key:generate
6. php artisan migrate

### Seeder実行時の注意
本アプリでは、Seederで商品画像を登録しています。

そのため、Seeder実行前に storage ディレクトリの準備を行ってください。
### 手順
1. storage画像用ディレクトリ作成
```
mkdir -p storage/app/public/items
mkdir -p storage/app/public/profiles
```
2. php artisan storage:link
3. php artisan db:seed
### 注意
- 上記手順を行わずに Seeder を実行すると、画像が正しく表示されない場合があります。

7. php artisan test


## メール認証（MailHog）設定
本アプリでは MailHog コンテナを使用しています。
Docker起動時に自動で立ち上がるため、ローカルへのインストールは不要です。

### メール送信設定（.evn）
以下の設定を”.evn”に追加して下さい。

MAILHOG_URL=http://localhost:8025

※ この設定を行うことで、メール認証画面に「認証はこちらから」ボタンが表示されます。

### メール確認方法
以下のURLで送信されたメールを確認できます：
http://localhost:8025/


## PHPunitを用いたテスト手順
1. docker-compose exec mysql bash
2. mysql -u root -p
3. CREATE DATABASE demo_test;
4. SHOW DATABASES;
5. cp .env .env.testing を作成し、以下の内容に編集

```
APP_ENV=testing
APP_KEY=（php artisan key:generate --env=testing で自動生成されます）
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```
6. php artisan key:generate --env=testing
7. php artisan config:clear
8. php artisan migrate --env=testing
9. 各テストコードを実行する

## テーブル仕様

### users テーブル

| カラム名           | 型          　　| primary key | unique key | not null | foreign key |
| ----------------- | --------------- | ----------- | ---------- | -------- | ----------- |
| id                | unsigned bigint | ◯           |            | ◯        |             |
| name              | string          |             |            | ◯        |             |
| email             | string          |             | ◯          | ◯        |             |
| email_verified_at | timestamp       |             |            |          |             |
| password          | string          |             |            | ◯        |             |
| profile_image     | string          |             |            |          |             |
| postal_code       | string          |             |            |          |             |
| address           | string          |             |            |          |             |
| building_name     | string          |             |            |          |             |
| created_at        | timestamp       |             |            |          |             |
| updated_at        | timestamp       |             |            |          |             |



### items テーブル

| カラム名    | 型               | primary key | unique key | not null | foreign key |
| ----------- | --------------- | ----------- | ---------- | -------- | ----------- |
| id          | unsigned bigint | ◯           |            | ◯        |             |
| user_id     | unsigned bigint |             |            | ◯        | users(id)   |
| name        | string          |             |            | ◯        |             |
| brand_name  | string          |             |            |          |             |
| description | text            |             |            | ◯        |             |
| price       | integer         |             |            | ◯        |             |
| condition   | string          |             |            | ◯        |             |
| created_at  | timestamp       |             |            |          |             |
| updated_at  | timestamp       |             |            |          |             |


### item_images テーブル

| カラム名    | 型               | primary key | unique key | not null | foreign key |
| ---------- | --------------- | ----------- | ---------- | -------- | ----------- |
| id         | unsigned bigint | ◯           |            | ◯        |             |
| item_id    | unsigned bigint |             |            | ◯        | items(id)   |
| image_path | string          |             |            | ◯        |             |
| created_at | timestamp       |             |            |          |             |
| updated_at | timestamp       |             |            |          |             |


### categories テーブル
|カラム名| 型             | primary key | unique key | not null | foreign key |
| ---- | --------------- | ----------- | ---------- | -------- | ----------- |
| id   | unsigned bigint | ◯           |            | ◯        |             |
| name | string          |             | ◯          | ◯        |             |


### category_item テーブル

| カラム名     | 型               | primary key | unique key | not null | foreign key    |
| ----------- | --------------- | ----------- | ---------- | -------- | -------------- |
| item_id     | unsigned bigint | ◯           |            | ◯        | items(id)      |
| category_id | unsigned bigint | ◯           |            | ◯        | categories(id) |


### likes テーブル

| カラム名    | 型               | primary key | unique key | not null | foreign key |
| ---------- | --------------- | ----------- | ---------- | -------- | ----------- |
| id         | unsigned bigint | ◯           |            | ◯        |             |
| user_id    | unsigned bigint |             |            | ◯        | users(id)   |
| item_id    | unsigned bigint |             |            | ◯        | items(id)   |
| created_at | timestamp       |             |            |          |             |


### comments テーブル

| カラム名    | 型               | primary key | unique key | not null | foreign key |
| ---------- | --------------- | ----------- | ---------- | -------- | ----------- |
| id         | unsigned bigint | ◯           |            | ◯        |             |
| user_id    | unsigned bigint |             |            | ◯        | users(id)   |
| item_id    | unsigned bigint |             |            | ◯        | items(id)   |
| content    | string          |             |            | ◯        |             |
| created_at | timestamp       |             |            |          |             |
| updated_at | timestamp       |             |            |          |             |

### purchases テーブル

| カラム名           | 型               | primary key | unique key | not null | foreign key |
| ----------------- | --------------- | ----------- | ---------- | -------- | ----------- |
| id                | unsigned bigint | ◯           |            | ◯        |             |
| user_id           | unsigned bigint |             |            | ◯        | users(id)   |
| item_id           | unsigned bigint |             | ◯          | ◯        | items(id)   |
| payment_method    | string          |             |            | ◯        |             |
| stripe_session_id | string          |             | ◯          |          |             |
| postal_code       | string          |             |            | ◯        |             |
| address           | string          |             |            | ◯        |             |
| building_name     | string          |             |            |          |             |
| created_at        | timestamp       |             |            |          |             |
| updated_at        | timestamp       |             |            |          |             |


## ER図
![ER図](image.png)

## ログイン情報（Seederによる作成）

### 一般ユーザー（認証済み）
* メールアドレス：yamada.tarou@example.com /password
* メールアドレス：yamada.hanako@example.com /password
### 一般ユーザー（未認証）
* メールアドレス：suzuki.ichiro@example.com /password
  * 初回ログイン時に手動送信によるメール認証が必要です
  * 認証メールは MailHog（http://localhost:8025/）で確認できます
## 使用技術

* PHP 7.4.9
* Laravel 8.83.8
* MySQL 8.0.26
* nginx 1.21.1
* MailHog latest

## URL

* 開発環境：http://localhost/
* Register：http://localhost/register
* Login：http://localhost/login
* phpMyAdmin：http://localhost:8080/
* MailHog：http://localhost:8025/

## ダミーデータ作成・画像
| 用途           | public パス               | storage パス                  |
| -------------- | ------------------------ | ---------------------------- |
| 商品一覧画像    | `public/images/items`    | `storage/app/public/items`   |
| プロフィール画像 | `public/images/profiles` | `storage/app/public/profiles`|

## Stripe決済設定
本アプリではオンライン決済に Stripe を使用しています。
ローカル環境で決済機能を利用するために、以下の設定を行ってください。

1. Stripeアカウント作成　　
* Stripe公式サイトでアカウントを作成してください。
* https://stripe.com/jp
* ダッシュボードから テスト用APIキー を取得します。

2. .env に API Key を設定
```
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxx
```
※ Stripe ダッシュボードの 開発者 → APIキー から取得できます。

3. テスト用カード番号
```
カード番号	4242 4242 4242 4242
有効期限	任意の未来日（例: 12/34）
CVC	任意（例: 123）
郵便番号	任意（例: 12345）
```
4. 決済テスト手順
* ユーザーでログイン
* 商品詳細ページから「購入」ボタンを押す
* 支払方法で カード決済 を選択
* Stripeのテストカード番号を入力
* 決済完了後、購入情報が purchases テーブルに保存される

## 注意事項
1. 支払方法はStripeを使用しているため、カード決済のみ購入完了となります。
（コンビニ支払いには対応していません）
2. テストケースでもカード決済のみでテストを行っています。
3. テストケースではユーザ登録後メール認証からプロフィールに入るのでテストの流れが異なります。
4. 自分の出品商品について
   自分の出品には「いいね」できない仕様としています。
   また、マイリストにも表示されないよう制御しています。
5. 売り切れ商品について
   売り切れ商品には、コメントおよび「いいね」ができない仕様としています。
6. 配送先はユーザープロフィールの住所を使用しています。
   購入時に未登録の場合は住所入力画面へ遷移し、常に最新の住所が使用される設計としています。