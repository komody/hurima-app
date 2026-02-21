<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面を表示
     */
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->sold_out) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', 'この商品は売り切れです。');
        }

        if ($item->seller_id === Auth::id()) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', '自分の商品は購入できません。');
        }

        $account = Auth::user()->account;
        $deliveryAddress = session('purchase_delivery') ?? [
            'postal_code' => $account?->postal_code ?? '',
            'address' => $account?->address ?? '',
            'building' => $account?->building ?? '',
        ];
        $paymentMethod = session('purchase_payment_method', '');

        return view('purchase.show', compact('item', 'deliveryAddress', 'paymentMethod'));
    }

    /**
     * 送付先住所変更画面を表示
     */
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        if ($item->sold_out) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', 'この商品は売り切れです。');
        }
        $account = Auth::user()->account;
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
    public function updateAddress(AddressRequest $request, $item_id)
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

    /**
     * コンビニ払い：Stripe を使わず購入を完了する
     */
    public function completeConveniencePurchase(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->sold_out) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', 'この商品は売り切れです。');
        }

        if ($item->seller_id === Auth::id()) {
            return redirect()
                ->route('items.show', ['item_id' => $item_id])
                ->with('error', '自分の商品は購入できません。');
        }

        $account = Auth::user()->account;
        $delivery = session('purchase_delivery') ?? [
            'postal_code' => $account?->postal_code ?? '',
            'address' => $account?->address ?? '',
            'building' => $account?->building ?? '',
        ];
        $paymentMethod = $request->validated()['payment_method'];

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

        session()->forget(['purchase_delivery', 'purchase_payment_method']);

        return redirect()
            ->route('items.index')
            ->with('message', '購入が完了しました。');
    }

    /**
     * Stripe Checkout Session を作成し、決済画面のURLを返す
     */
    public function createCheckoutSession(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->sold_out) {
            return response()->json(['error' => 'この商品は売り切れです。'], 400);
        }
        if ($item->seller_id === Auth::id()) {
            return response()->json(['error' => '自分の商品は購入できません。'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $account = Auth::user()->account;
        $delivery = session('purchase_delivery') ?? [
            'postal_code' => $account?->postal_code ?? '',
            'address' => $account?->address ?? '',
            'building' => $account?->building ?? '',
        ];

        $successUrl = url("/purchase/{$item_id}/success") . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('purchase.show', ['item_id' => $item_id]);

        try {
            $session = Session::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                            'images' => $item->image_url ? [url($item->image_url)] : [],
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'item_id' => (string) $item->id,
                    'user_id' => (string) Auth::id(),
                    'payment_method' => 'カード支払い',
                    'delivery_postal_code' => $delivery['postal_code'] ?? '',
                    'delivery_address' => $delivery['address'] ?? '',
                    'delivery_building' => $delivery['building'] ?? '',
                ],
                'locale' => 'ja',
            ]);

            return response()->json(['url' => $session->url]);
        } catch (ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Stripe 決済成功後のリダイレクト先
     */
    public function checkoutSuccess(Request $request, $item_id)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()
                ->route('items.index')
                ->with('error', 'セッションが無効です。');
        }

        $item = Item::findOrFail($item_id);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()
                    ->route('purchase.show', ['item_id' => $item_id])
                    ->with('error', '決済が完了していません。');
            }

            if ((string) $session->metadata->item_id !== (string) $item->id) {
                return redirect()
                    ->route('items.index')
                    ->with('error', '不正なリクエストです。');
            }

            if ((string) $session->metadata->user_id !== (string) Auth::id()) {
                return redirect()
                    ->route('items.index')
                    ->with('error', '不正なリクエストです。');
            }

            DB::transaction(function () use ($item, $session) {
                $item->update([
                    'buyer_id' => Auth::id(),
                    'sold_out' => true,
                ]);

                Order::create([
                    'user_id' => (int) $session->metadata->user_id,
                    'item_id' => (int) $session->metadata->item_id,
                    'payment_method' => $session->metadata->payment_method ?? 'カード支払い',
                    'delivery_postal_code' => $session->metadata->delivery_postal_code ?? '',
                    'delivery_address' => $session->metadata->delivery_address ?? '',
                    'delivery_building' => $session->metadata->delivery_building ?? '',
                ]);
            });

            session()->forget(['purchase_delivery', 'purchase_payment_method']);

            return redirect()
                ->route('items.index')
                ->with('message', '購入が完了しました。');
        } catch (ApiErrorException $e) {
            return redirect()
                ->route('purchase.show', ['item_id' => $item_id])
                ->with('error', '決済の確認に失敗しました。');
        }
    }
}
