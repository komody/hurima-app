<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 商品一覧画面を表示
     */
    public function index(Request $request)
    {
        $products = Product::with('condition')
            ->where('sold_out', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('items.index', compact('products'));
    }

    /**
     * 商品詳細画面を表示
     */
    public function show($item_id)
    {
        $product = Product::with(['condition', 'categories', 'comments.user'])
            ->findOrFail($item_id);

        // いいね数を集計
        $likesCount = $product->likes()->count();
        
        // ログインユーザーがいいね済みか判定
        $isLiked = false;
        if (Auth::check()) {
            $isLiked = $product->likes()
                ->where('user_id', Auth::id())
                ->exists();
        }

        return view('items.show', compact('product', 'likesCount', 'isLiked'));
    }
}
