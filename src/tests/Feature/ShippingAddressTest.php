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
 * 12. 配送先変更機能
 */
class ShippingAddressTest extends TestCase
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
            'postal_code' => '100-0001',
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
     * 12.1 送付先住所変更画面で登録した住所が商品購入画面に反映される
     */
    public function test_registered_address_is_reflected_on_purchase_screen(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => '住所変更テスト商品']);

        $response = $this->actingAs($user)->put(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '150-0001',
            'address' => '東京都渋谷区神宮前1-1-1',
            'building' => 'テストビル101',
        ]);

        $response->assertRedirect(route('purchase.show', ['item_id' => $item->id]));
        $response->assertSessionHas('message', '配送先を更新しました。');

        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('150-0001');
        $response->assertSee('東京都渋谷区神宮前1-1-1');
        $response->assertSee('テストビル101');
    }

    /**
     * 12.2 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_shipping_address_is_associated_with_purchased_item(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => '住所紐づけテスト商品']);

        $this->actingAs($user)->put(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '160-0001',
            'address' => '東京都新宿区西新宿1-1-1',
            'building' => '',
        ]);

        $this->actingAs($user)->post(route('purchase.complete-convenience', ['item_id' => $item->id]), [
            'payment_method' => 'コンビニ支払い',
        ]);

        /** @var \App\Models\Order|null $order */
        $order = \App\Models\Order::where('item_id', $item->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('160-0001', $order->delivery_postal_code);
        $this->assertEquals('東京都新宿区西新宿1-1-1', $order->delivery_address);
    }
}
