<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ItemExhibitionTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test
     * 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
     */
    public function user_can_create_item_with_all_required_fields_using_factory()
    {
        $this->actingAs($this->user);

        // カテゴリ作成
        $category = Category::factory()->create();

        //商品等の作成
        $item = Item::factory()->for($this->user)->create([
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト商品の説明です',
            'price' => 5000,
            'condition' => '新品・未使用',
        ]);

        ItemImage::factory()->for($item)->create([
            'image_path' => 'items/dummy.jpg',
        ]);

        $item->categories()->sync([$category->id]);

        // DB に正しく保存されていることを確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'description' => 'テスト商品の説明です',
            'price' => 5000,
            'condition' => '新品・未使用',
            'user_id' => $this->user->id,
        ]);


        $this->assertTrue($item->categories->contains($category));

        // 画像のパス確認
        $this->assertDatabaseHas('item_images', [
            'item_id' => $item->id,
            'image_path' => 'items/dummy.jpg',
        ]);
    }
}