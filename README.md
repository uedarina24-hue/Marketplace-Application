# Marketplace-Application

## 環境構築

Docker ビルド
1.git clone git@github.com:uedarina24-hue/Marketplace-Application.git
2.docker-compose up -d --build

Lavaral 環境構築
1.docker-compose exec php bash
2.composer install
3.cp .env.example .env
4..env ファイルの変更

```
　DB_HOSTをmysqlに変更
　DB_DATABASEをlaravel_dbに変更
　DB_USERNAMEをlaravel_userに変更
　DB_PASSをlaravel_passに変更
　MAIL_FROM_ADDRESSに送信元アドレスを設定
```

5.php artisan key:generate
6.php artisan migrate
7.php artisan db:seed
8.php artisan test

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
| content    | text            |             |            | ◯        |             |
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


## ER 図
![alt text](image.png)

### ログイン情報
## 一般ユーザー（認証済み）
* メールアドレス：yamada.tarou@example.com
* パスワード：password
* メールアドレス：yamada.hanako@example.com
* パスワード：password
## 一般ユーザー（未認証）
* メールアドレス：suzuki.ichiro@example.com
* パスワード：password

## 使用技術

・PHP 7.4.9
・Laravel 8.83.8
・MySQL 8.0.26
・nginx 1.21.1
・MailHog latest

## URL

・開発環境：http://localhost/
・phpMyAdmin：http://localhost:8080/
・MailHog：http://localhost:8025/