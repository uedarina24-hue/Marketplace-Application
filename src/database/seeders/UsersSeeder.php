<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UsersSeeder extends Seeder
{
    public function run()
    {

        $users = [
            // 認証済ユーザー
            [
                'name' => '山田 太郎',
                'email' => 'yamada.tarou@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'profile_image' => 'images/profiles/men1.png',
                'postal_code' => '100-0001',
                'address' => '東京都千代田区千代田1-1',
                'building_name' => '皇居マンション101',
            ],

            [
                'name' => '山田 花子',
                'email' => 'yamada.hanako@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'profile_image' => 'images/profiles/woman1.png',
                'postal_code' => '150-0001',
                'address' => '東京都渋谷区神宮前1-1',
                'building_name' => '渋谷ビル202',
            ],

            // 未認証ユーザー
            [
                'name' => '鈴木 一郎',
                'email' => 'suzuki.ichiro@example.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'profile_image' => 'images/profiles/men2.png',
                'postal_code' => '530-0001',
                'address' => '大阪府大阪市北区梅田1-1',
                'building_name' => '梅田タワー303',
            ],
        ];

        foreach ($users as $data) {

            // ========= ① ファイル準備 =========
            $sourcePath = public_path($data['profile_image']);
            $fileName = basename($data['profile_image']);
            $storagePath = 'public/profiles/' . $fileName;

            // ========= ② storageへコピー =========
            if (File::exists($sourcePath)) {
                Storage::put($storagePath, file_get_contents($sourcePath));
            } else {
                // 保険（ダミー）
                Storage::put($storagePath, str_repeat('0', 100));
            }

            // ========= ③ ユーザー保存 =========
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'email_verified_at' => $data['email_verified_at'],
                'password' => $data['password'],
                'profile_image' => 'profiles/' . $fileName,
                'postal_code' => $data['postal_code'],
                'address' => $data['address'],
                'building_name' => $data['building_name'],
                'remember_token' => Str::random(10),
            ]);
        }
    }

}
