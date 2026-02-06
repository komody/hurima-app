<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellController extends Controller
{
    /**
     * 商品出品画面を表示
     */
    public function create()
    {
        return view('sell.create');
    }
}
