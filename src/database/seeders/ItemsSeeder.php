<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ItemsSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();

        try {

            $items = [

                [
                    'name' => '腕時計',
                    'price' => 15000,
                    'brand_name' => 'Rolax',
                    'description' => 'スタイリッシュなデザインのメンズ腕時計',
                    'condition' => '良好',
                    'image' => 'images/items/watch.jpg',
                    'categories' => ['ファッション', 'メンズ'],
                ],

                [
                    'name' => 'HDD',
                    'price' => 5000,
                    'brand_name' => '西芝',
                    'description' => '高速で信頼性の高いハードディスク',
                    'condition' => '目立った傷や汚れなし',
                    'image' => 'images/items/hdd.jpg',
                    'categories' => ['家電','PC'],
                ],

                [
                    'name' => '玉ねぎ3束',
                    'price' => 300,
                    'brand_name' => null,
                    'description' => '新鮮な玉ねぎ3束のセット',
                    'condition' => 'やや傷や汚れあり',
                    'image' => 'images/items/onion.jpg',
                    'categories' => ['食品'],
                ],

                [
                    'name' => '革靴',
                    'price' => 4000,
                    'brand_name' => null,
                    'description' => 'クラシックなデザインの革靴',
                    'condition' => '状態が悪い',
                    'image' => 'images/items/shoes.jpg',
                    'categories' => ['ファッション', 'メンズ'],
                ],

                [
                    'name' => 'ノートPC',
                    'price' => 45000,
                    'brand_name' => null,
                    'description' => '高性能なノートパソコン',
                    'condition' => '良好',
                    'image' => 'images/items/laptop.jpg',
                    'categories' => ['家電','PC'],
                ],

                [
                    'name' => 'マイク',
                    'price' => 8000,
                    'brand_name' => null,
                    'description' => '高音質のレコーディング用マイク',
                    'condition' => '目立った傷や汚れなし',
                    'image' => 'images/items/mic.jpg',
                    'categories' => ['家電'],
                ],

                [
                    'name' => 'ショルダーバッグ',
                    'price' => 3500,
                    'brand_name' => null,
                    'description' => 'おしゃれなショルダーバッグ',
                    'condition' => 'やや傷や汚れあり',
                    'image' => 'images/items/bag.jpg',
                    'categories' => ['ファッション', 'レディース'],
                ],

                [
                    'name' => 'タンブラー',
                    'price' => 500,
                    'brand_name' => null,
                    'description' => '使いやすいタンブラー',
                    'condition' => '状態が悪い',
                    'image' => 'images/items/tumbler.jpg',
                    'categories' => ['キッチン'],
                ],

                [
                    'name' => 'コーヒーミル',
                    'price' => 4000,
                    'brand_name' => 'Starbacks',
                    'description' => '手動のコーヒーミル',
                    'condition' => '良好',
                    'image' => 'images/items/mill.jpg',
                    'categories' => ['キッチン'],
                ],

                [
                    'name' => 'メイクセット',
                    'price' => 2500,
                    'brand_name' => null,
                    'description' => '便利なメイクアップセット',
                    'condition' => '目立った傷や汚れなし',
                    'image' => 'images/items/makeup.jpg',
                    'categories' => ['コスメ'],
                ],

            ];

            foreach ($items as $data) {

                // ========= ① ファイル準備 =========
                $sourcePath = public_path($data['image']);
                $fileName = basename($data['image']);
                $storagePath = 'public/items/' . $fileName;

                // ========= ② storageへコピー =========
                if (File::exists($sourcePath)) {
                    Storage::put($storagePath, File::get($sourcePath));
                } else {
                    // 万一の保険（ダミー）
                    Storage::put($storagePath, str_repeat('0', 100));
                }

                // ========= ③ items保存 =========
                $item = Item::create([
                    'user_id' => 1,
                    'name' => $data['name'],
                    'brand_name' => $data['brand_name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'condition' => $data['condition'],
                ]);

                // ========= ④ 画像保存 =========
                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => 'items/' . $fileName,
                ]);

                // ========= ⑤ カテゴリ =========
                $categoryIds = Category::whereIn('name', $data['categories'])->pluck('id');
                $item->categories()->attach($categoryIds);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}