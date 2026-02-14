<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    /**
     * 商品出品画面を表示
     */
    public function create()
    {
        $categories = Category::orderBy('id')->get();
        $conditions = Condition::orderBy('id')->get();

        return view('sell.create', compact('categories', 'conditions'));
    }

    /**
     * 商品出品を保存
     */
    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image')->store('items', 'public');
        $imageUrl = '/storage/' . $path;

        $item = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_url' => $imageUrl,
            'condition_id' => $request->condition_id,
            'price' => $request->price,
            'brand_name' => $request->brand_name,
            'seller_id' => Auth::id(),
            'buyer_id' => null,
            'sold_out' => false,
        ]);

        $item->categories()->attach($request->category_ids);

        return redirect()->route('items.index')->with('message', '商品を出品しました');
    }
}
