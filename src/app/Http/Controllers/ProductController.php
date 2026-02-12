<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ProductController extends Controller
{
    /**
     * 商品一覧を表示
     */
    public function index()
    {
        $items = Item::with('condition')
            ->withCount(['comments', 'likes'])
            ->where('sold_out', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('products.index', compact('items'));
    }
}
