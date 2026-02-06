<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

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

        return view('items.show', compact('product'));
    }
}
