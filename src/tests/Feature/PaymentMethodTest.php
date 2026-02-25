<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 11. 支払い方法選択機能
 */
class PaymentMethodTest extends TestCase
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

        return Item::create(array_merge($base, array_diff_key($attributes, array_flip(['condition_id', 'seller_id']))));
    }

    /**
     * 11.1 支払い方法を選択すると小計画面に反映される
     */
    public function test_selected_payment_method_is_reflected_on_subtotal_screen(): void
    {
        $user = $this->createVerifiedUser();
        $item = $this->createItem(['name' => '支払い方法テスト商品']);

        $response = $this->actingAs($user)->get(route('purchase.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('支払い方法');
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード支払い');
        $response->assertSee('選択してください');

        $response = $this->actingAs($user)->withSession(['purchase_payment_method' => 'コンビニ払い'])
            ->get(route('purchase.show', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('コンビニ払い', false);
    }
}
