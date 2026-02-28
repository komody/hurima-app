<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * 購入可否を検証（リダイレクト用）
     */
    public function validatePurchaseable(Item $item, $itemId): ?RedirectResponse
    {
        if ($item->sold_out) {
            return redirect()
                ->route('items.show', ['item_id' => $itemId])
                ->with('error', 'この商品は売り切れです。');
        }

        if ($item->seller_id === Auth::id()) {
            return redirect()
                ->route('items.show', ['item_id' => $itemId])
                ->with('error', '自分の商品は購入できません。');
        }

        return null;
    }

    /**
     * 購入可否を検証（JSON API用）
     */
    public function validatePurchaseableForApi(Item $item, $itemId): ?JsonResponse
    {
        if ($item->sold_out) {
            return response()->json(['error' => 'この商品は売り切れです。'], 400);
        }

        if ($item->seller_id === Auth::id()) {
            return response()->json(['error' => '自分の商品は購入できません。'], 400);
        }

        return null;
    }

    /**
     * セッションから配送先を取得
     */
    public function getDeliveryAddress(): array
    {
        $account = Auth::user()->account;

        return session('purchase_delivery') ?? [
            'postal_code' => $account?->postal_code ?? '',
            'address' => $account?->address ?? '',
            'building' => $account?->building ?? '',
        ];
    }

    /**
     * 編集画面用の配送先を取得（old値対応）
     */
    public function getDeliveryAddressForEdit(): array
    {
        $account = Auth::user()->account;

        return session('purchase_delivery') ?? [
            'postal_code' => old('postal_code', $account?->postal_code ?? ''),
            'address' => old('address', $account?->address ?? ''),
            'building' => old('building', $account?->building ?? ''),
        ];
    }

    /**
     * 購入処理を実行（コンビニ払い用）
     */
    public function completePurchase(Item $item, array $delivery, string $paymentMethod): void
    {
        DB::transaction(function () use ($item, $delivery, $paymentMethod) {
            $item->update([
                'buyer_id' => Auth::id(),
                'sold_out' => true,
            ]);

            Order::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'payment_method' => $paymentMethod,
                'delivery_postal_code' => $delivery['postal_code'] ?? '',
                'delivery_address' => $delivery['address'] ?? '',
                'delivery_building' => $delivery['building'] ?? '',
            ]);
        });
    }
}
