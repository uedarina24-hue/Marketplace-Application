<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | 認証済ユーザー
        |--------------------------------------------------------------------------
        */

        User::create([
            'name' => '山田 太郎',
            'email' => 'yamada.tarou@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => 'profiles/men1.png',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区千代田1-1',
            'building_name' => '皇居マンション101',
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => '山田 花子',
            'email' => 'yamada.hanako@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => 'profiles/woman1.png',
            'postal_code' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1',
            'building_name' => '渋谷ビル202',
            'remember_token' => Str::random(10),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 未認証ユーザー
        |--------------------------------------------------------------------------
        */

        User::create([
            'name' => '鈴木 一郎',
            'email' => 'suzuki.ichiro@example.com',
            'email_verified_at' => null,
            'password' => Hash::make('password'),
            'profile_image' => 'profiles/men2.png',
            'postal_code' => '530-0001',
            'address' => '大阪府大阪市北区梅田1-1',
            'building_name' => '梅田タワー303',
            'remember_token' => Str::random(10),
        ]);
    }

}
