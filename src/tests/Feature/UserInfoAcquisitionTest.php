<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 13. ユーザー情報取得
 */
class UserInfoAcquisitionTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedUser(array $accountAttributes = []): User
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'first_login_email_verified_at' => now(),
        ]);
        Account::create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'postal_code' => '100-0001',
            'address' => '東京都千代田区',
        ], $accountAttributes));
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
     * 13.1 必要な情報が取得できる（プロフィール画像、ユーザー名、出品一覧、購入一覧）
     */
    public function test_required_user_info_is_displayed_on_profile_page(): void
    {
        Storage::fake('public');
        $profileImagePath = 'profile_images/test_profile.png';
        Storage::disk('public')->put($profileImagePath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));

        $user = $this->createVerifiedUser(['profile_image' => $profileImagePath]);
        $soldItem = $this->createItem(['name' => '出品した商品', 'seller_id' => $user->id]);
        $purchasedItem = $this->createItem(['name' => '購入した商品']);
        $purchasedItem->update(['buyer_id' => $user->id, 'sold_out' => true]);

        $response = $this->actingAs($user)->get(route('mypage.index'));

        $response->assertStatus(200);
        $response->assertSee('profile_images/test_profile.png');
        $response->assertSee('テストユーザー');
        $response->assertSee('出品した商品');
        $response->assertSee('購入した商品');
    }
}
