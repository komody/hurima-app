<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 商品一覧画面を表示
     */
    public function index(Request $request)
    {
        $items = Item::with('condition')
            ->where('sold_out', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('items.index', compact('items'));
    }

    /**
     * 商品詳細画面を表示
     */
    public function show($item_id)
    {
        $item = Item::with(['condition', 'categories', 'comments.user'])
            ->withCount(['comments', 'likes'])
            ->findOrFail($item_id);

        // いいね数を集計
        $likesCount = $item->likes()->count();

        // ログインユーザーがいいね済みか判定
        $isLiked = false;
        if (Auth::check()) {
            $isLiked = $item->likes()
                ->where('user_id', Auth::id())
                ->exists();
        }

        return view('items.show', compact('item', 'likesCount', 'isLiked'));
    }
}
