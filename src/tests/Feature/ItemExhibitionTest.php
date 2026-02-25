<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 15. 出品商品情報登録
 */
class ItemExhibitionTest extends TestCase
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

    /**
     * 15.1 必要な情報が保存できる（カテゴリ、商品の状態、商品名、ブランド名、説明、販売価格）
     */
    public function test_required_item_info_can_be_saved(): void
    {
        Storage::fake('public');
        $user = $this->createVerifiedUser();
        $category1 = Category::create(['name' => 'ファッション']);
        $category2 = Category::create(['name' => 'メンズ']);
        $condition = Condition::create(['name' => '良好']);

        $imagePath = base_path('tests/fixtures/item_test.png');
        $image = new UploadedFile($imagePath, 'item_test.png', 'image/png', null, true);

        $response = $this->actingAs($user)->post(route('sell.store'), [
            'name' => '出品テスト商品',
            'description' => '商品の説明文です',
            'image' => $image,
            'category_ids' => [$category1->id, $category2->id],
            'condition_id' => $condition->id,
            'price' => 5000,
            'brand_name' => 'テストブランド',
        ]);

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('message', '商品を出品しました');

        $item = Item::where('name', '出品テスト商品')->first();
        $this->assertNotNull($item);
        $this->assertEquals('出品テスト商品', $item->name);
        $this->assertEquals('商品の説明文です', $item->description);
        $this->assertEquals($condition->id, $item->condition_id);
        $this->assertEquals(5000, $item->price);
        $this->assertEquals('テストブランド', $item->brand_name);
        $this->assertEquals($user->id, $item->seller_id);

        $item->load('categories');
        $this->assertTrue($item->categories->contains($category1));
        $this->assertTrue($item->categories->contains($category2));
    }
}
