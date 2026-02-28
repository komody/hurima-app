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
 * 9. コメント送信機能
 */
class CommentTest extends TestCase
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
     * 9.1 ログインユーザーはコメントを送信できる・コメント数が増える
     */
    public function test_logged_in_user_can_send_comment_and_count_increases(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'コメントテスト商品']);

        $response = $this->actingAs($user)->post(route('items.comment.store', ['item_id' => $item->id]), [
            'content' => 'いい商品ですね',
        ]);

        $response->assertRedirect(route('items.show', ['item_id' => $item->id]));
        $response->assertSessionHas('message', 'コメントを送信しました');
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'いい商品ですね',
        ]);
        $this->assertEquals(1, $item->fresh()->comments()->count());
    }

    /**
     * 9.2 未ログインユーザーはコメントを送信できない
     */
    public function test_guest_cannot_send_comment(): void
    {
        $item = $this->createItem(['name' => 'コメント不可テスト商品']);

        $response = $this->post(route('items.comment.store', ['item_id' => $item->id]), [
            'content' => 'コメント内容',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertEquals(0, $item->fresh()->comments()->count());
    }

    /**
     * 9.3 コメント未入力でバリデーションエラー
     */
    public function test_validation_error_when_comment_is_empty(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => 'バリデーションテスト商品']);

        $response = $this->actingAs($user)->post(route('items.comment.store', ['item_id' => $item->id]), [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertStringContainsString('コメントを入力してください', $response->getSession()->get('errors')->get('content')[0] ?? '');
    }

    /**
     * 9.4 255文字以上のコメントでバリデーションエラー
     */
    public function test_validation_error_when_comment_exceeds_255_characters(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => '文字数テスト商品']);

        $response = $this->actingAs($user)->post(route('items.comment.store', ['item_id' => $item->id]), [
            'content' => str_repeat('あ', 256),
        ]);

        $response->assertSessionHasErrors('content');
        $this->assertStringContainsString('255文字以内', $response->getSession()->get('errors')->get('content')[0] ?? '');
    }
}
