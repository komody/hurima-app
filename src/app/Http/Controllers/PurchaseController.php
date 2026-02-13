<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseAddressRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面を表示
     */
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 売り切れの場合は商品詳細へリダイレクト
        if ($item->sold_out) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', 'この商品は売り切れです。');
        }

        // 自分の商品の場合は商品詳細へリダイレクト
        if ($item->seller_id === Auth::id()) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', '自分の商品は購入できません。');
        }

        $account = Auth::user()->account;

        // 配送先: セッションがあればそれを使用、なければAccountの住所
        $deliveryAddress = session('purchase_delivery') ?? [
            'postal_code' => $account?->postal_code ?? '',
            'address' => $account?->address ?? '',
            'building' => $account?->building ?? '',
        ];

        // 支払い方法: セッションがあればそれを使用
        $paymentMethod = session('purchase_payment_method', '');

        return view('purchase.show', compact('item', 'deliveryAddress', 'paymentMethod'));
    }

    /**
     * 送付先住所変更画面を表示
     */
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 売り切れの場合は商品詳細へリダイレクト
        if ($item->sold_out) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', 'この商品は売り切れです。');
        }

        $account = Auth::user()->account;

        // フォームの初期値: セッション > old > Account
        $deliveryAddress = session('purchase_delivery') ?? [
            'postal_code' => old('postal_code', $account?->postal_code ?? ''),
            'address' => old('address', $account?->address ?? ''),
            'building' => old('building', $account?->building ?? ''),
        ];

        return view('purchase.address.edit', compact('item_id', 'deliveryAddress'));
    }

    /**
     * 送付先住所を更新（セッションに保存）
     */
    public function updateAddress(PurchaseAddressRequest $request, $item_id)
    {
        $validated = $request->validated();

        session([
            'purchase_delivery' => [
                'postal_code' => $validated['postal_code'],
                'address' => $validated['address'],
                'building' => $validated['building'] ?? '',
            ],
        ]);

        return redirect()
            ->route('purchase.show', ['item_id' => $item_id])
            ->with('message', '配送先を更新しました。');
    }
}
