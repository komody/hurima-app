<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 6. 商品検索機能
 */
class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedUser(): User
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Account::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'postal_code' => '1000001',
            'address' => '東京都千代田区',
        ]);
        return $user;
    }

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

        $item = Item::create(array_merge($base, array_diff_key($attributes, array_flip(['condition_id', 'seller_id', 'category_ids']))));
        $categoryIds = $attributes['category_ids'] ?? null;
        if ($categoryIds !== null) {
            $item->categories()->attach($categoryIds);
        } else {
            $category = Category::firstOrCreate(['name' => 'テストカテゴリ']);
            $item->categories()->attach($category->id);
        }
        return $item;
    }

    /**
     * 6.1 「商品名」で部分一致検索ができる
     */
    public function test_partial_match_search_by_product_name(): void
    {
        $this->createItem(['name' => '腕時計 メンズ']);
        $this->createItem(['name' => 'デジタル腕時計']);
        $this->createItem(['name' => 'ノートPC']);

        $response = $this->get(route('items.index', ['search' => '腕時計']));

        $response->assertStatus(200);
        $response->assertSee('腕時計 メンズ');
        $response->assertSee('デジタル腕時計');
        $response->assertDontSee('ノートPC');
    }

    /**
     * 6.2 検索状態がマイリストでも保持されている
     */
    public function test_search_state_is_maintained_in_my_list(): void
    {
        $user = $this->createVerifiedUser();
        $keyword = 'テスト検索キーワード';

        $response = $this->actingAs($user)
            ->get(route('items.index', ['search' => $keyword, 'tab' => 'mylist']));

        $response->assertStatus(200);
        $response->assertSee($keyword, false);
        $response->assertSee('value="' . $keyword . '"', false);
    }
}
