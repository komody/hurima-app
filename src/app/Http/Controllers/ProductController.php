<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * 商品一覧を表示
     */
    public function index()
    {
        $products = Product::with('condition')
            ->where('sold_out', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('products.index', compact('products'));
    }
}
