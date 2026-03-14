<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MylistTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test
     * いいねした商品だけが表示される
     */
    public function only_liked_items_are_displayed()
    {
        $this->actingAs($this->user);

        $likedItem = Item::factory()->create();
        $this->user->likes()->create(['item_id' => $likedItem->id]);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertSee($likedItem->name);
    }

    /** @test
     * 購入済みの商品には「Sold」ラベルが表示される
     */
    public function purchased_items_show_sold_label()
    {
        $this->actingAs($this->user);

        $item = Item::factory()->create();

        // マイリストに追加
        $this->user->likes()->create(['item_id' => $item->id]);

        $item->purchase()->create([
            'user_id' => $this->user->id,
            'payment_method' => 'card',
            'stripe_session_id' => 'sess_test',
            'postal_code' => '123-4567',
            'address' => 'Tokyo',
        ]);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertSee('Sold');
    }

    /** @test
     * 未認証の場合は何も表示されない
     */
    public function guest_user_sees_empty_list()
    {
        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertSee('商品が見つかりませんでした');
    }
}