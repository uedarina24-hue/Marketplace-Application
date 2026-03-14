<?php

namespace Tests\Feature\Items;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
     * 「商品名」で部分一致検索ができることを確認する
     */
    public function test_search_returns_matching_items()
    {
        $user = User::factory()->create();
        $item = Item::factory()->withName('Test Item')->create(['user_id' => $user->id]);

        $response = $this->get('/?keyword=Test');

        $response->assertStatus(200);
        $response->assertSee('Test Item');
    }

    /** @test
     * 検索状態がマイリストでも保持されていることを確認する
     */
    public function test_search_keyword_is_retained_in_mylist_tab()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $likedItem = Item::factory()->withName('Test MyItem')->create();
        $likedItem->likes()->create(['user_id' => $user->id]);

        $response = $this->get('/?tab=mylist&keyword=Test');

        $response->assertStatus(200);
        $response->assertSee('Test MyItem');
    }

}