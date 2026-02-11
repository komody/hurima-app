<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * いいねを追加または削除
     */
    public function toggle(Request $request, $item_id)
    {
        // 認証チェック
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 商品の存在確認
        $product = Product::find($item_id);
        if (!$product) {
            return redirect()->route('items.index')
                ->with('error', '商品が見つかりませんでした。');
        }

        $userId = Auth::id();
        
        // 既にいいね済みか確認
        $existingLike = Like::where('user_id', $userId)
            ->where('product_id', $item_id)
            ->first();

        if ($existingLike) {
            // いいね解除
            $existingLike->delete();
        } else {
            // いいね追加
            Like::create([
                'user_id' => $userId,
                'product_id' => $item_id,
            ]);
        }

        return redirect()->route('items.show', ['item_id' => $item_id]);
    }
}
