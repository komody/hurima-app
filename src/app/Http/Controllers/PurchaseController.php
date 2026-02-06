<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面を表示
     */
    public function show($item_id)
    {
        return view('purchase.show', compact('item_id'));
    }

    /**
     * 送付先住所変更画面を表示
     */
    public function editAddress($item_id)
    {
        return view('purchase.address.edit', compact('item_id'));
    }
}
