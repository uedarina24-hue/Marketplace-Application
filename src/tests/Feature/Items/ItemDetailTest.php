<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ItemDetailTest extends TestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profiles/test_user_image.jpg',
        ]);
    }

    /** @test
     * 必要な情報が表示される、複数選択されたカテゴリが表示されていることを確認する
     */
    public function item_detail_displays_all_information_and_multiple_categories()
    {
        // 複数カテゴリ作成
        $categories = Category::factory()
            ->count(2)
            ->state(new Sequence(
                ['name' => 'カテゴリA'],
                ['name' => 'カテゴリB']
            ))
            ->create();

        // 商品作成（画像・コメント付き）
        $item = Item::factory()
            ->for($this->user, 'user')
            ->has(ItemImage::factory(), 'images')
            ->has(Comment::factory()->for($this->user), 'comments')
            ->create([
                'name' => 'テスト商品',
                'brand_name' => 'テストブランド',
                'price' => 12345,
                'description' => 'テスト商品説明',
                'condition' => '新品',
            ]);

        // 商品にカテゴリを紐付け
        $item->categories()->attach($categories->pluck('id'));

        // 商品詳細ページにアクセス
        $response = $this->get(route('items.show', $item));

        // =========================
        // 商品情報の検証
        // =========================
        $response->assertSee($item->name);
        $response->assertSee($item->brand_name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($item->condition);

        // 画像
        $response->assertSee($item->firstImage->image_url);

        // コメント情報
        $comment = $item->comments->first();
        $response->assertSee($comment->content);
        $response->assertSee($comment->user->name);
        if ($comment->user->profile_image) {
            $response->assertSee($comment->user->profile_image_url);
        }

        // いいね数・コメント数
        $response->assertSee((string) $item->likes_count);
        $response->assertSee((string) $item->comments_count);

        // 複数カテゴリ表示チェック
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }
}