<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 8. いいね機能
 */
class ItemLikeTest extends TestCase
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
     * 8.1 いいねを登録できる・いいね数が増える
     */
    public function test_user_can_like_item_and_count_increases(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'いいねテスト商品']);

        $response = $this->actingAs($user)->post(route('items.like.toggle', ['item_id' => $item->id]));

        $response->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertEquals(1, $item->fresh()->likes()->count());
    }

    /**
     * 8.2 いいね済みのときアイコンの色が変わる（liked_icon.svg が表示される）
     */
    public function test_like_icon_changes_when_item_is_liked(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'いいね済み商品']);
        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);

        $response = $this->actingAs($user)->get(route('items.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('liked_icon.svg');
    }

    /**
     * 8.3 いいねを解除できる・いいね数が減る
     */
    public function test_user_can_unlike_item_and_count_decreases(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'いいね解除テスト商品']);
        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);

        $response = $this->actingAs($user)->post(route('items.like.toggle', ['item_id' => $item->id]));

        $response->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertEquals(0, $item->fresh()->likes()->count());
    }
}
