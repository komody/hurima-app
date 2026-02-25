<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7. 商品詳細情報取得
 */
class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    private function createItem(array $attributes = []): Item
    {
        $conditionId = $attributes['condition_id'] ?? Condition::create(['name' => '良好'])->id;
        $sellerId = $attributes['seller_id'] ?? User::create([
            'name' => '出品者',
            'email' => 'seller' . uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ])->id;

        $base = [
            'name' => 'テスト商品',
            'price' => 1000,
            'brand_name' => null,
            'description' => '説明文',
            'image_url' => '/storage/items/dummy.jpg',
            'condition_id' => $conditionId,
            'seller_id' => $sellerId,
            'buyer_id' => null,
            'sold_out' => false,
        ];

        return Item::create(array_merge($base, array_diff_key($attributes, array_flip(['condition_id', 'seller_id']))));
    }

    /**
     * 7.1 必要な情報が表示される
     */
    public function test_required_information_is_displayed_on_product_detail_page(): void
    {
        $condition = Condition::create(['name' => '良好']);
        $item = $this->createItem([
            'name' => 'テスト商品名',
            'brand_name' => 'テストブランド',
            'price' => 10000,
            'description' => '商品説明文',
            'image_url' => '/storage/items/test.jpg',
            'condition_id' => $condition->id,
        ]);

        $commentUser = User::create([
            'name' => 'コメント太郎',
            'email' => 'comment@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => 'いい商品ですね',
        ]);

        $likeUser = User::create([
            'name' => 'いいねユーザー',
            'email' => 'like@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Like::create([
            'item_id' => $item->id,
            'user_id' => $likeUser->id,
        ]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee($item->image_url);
        $response->assertSee('テスト商品名');
        $response->assertSee('テストブランド');
        $response->assertSee('10,000');
        $response->assertSee('1');
        $response->assertSee('商品説明文');
        $response->assertSee('良好');
        $response->assertSee('コメント太郎');
        $response->assertSee('いい商品ですね');
    }

    /**
     * 7.2 複数選択されたカテゴリが表示されているか
     */
    public function test_multiple_categories_are_displayed_on_product_detail_page(): void
    {
        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => 'メンズ']);

        $item = $this->createItem(['name' => '複数カテゴリ商品']);
        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
    }
}
