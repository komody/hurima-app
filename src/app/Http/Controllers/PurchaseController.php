<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Services\PurchaseService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService,
        private StripeCheckoutService $stripeCheckoutService
    ) {}

    /**
     * 商品購入画面を表示
     */
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        $redirect = $this->purchaseService->validatePurchaseable($item, $item_id);
        if ($redirect) {
            return $redirect;
        }

        $deliveryAddress = $this->purchaseService->getDeliveryAddress();
        $paymentMethod = session('purchase_payment_method', '');

        return view('purchase.show', compact('item', 'deliveryAddress', 'paymentMethod'));
    }

    /**
     * 送付先住所変更画面を表示
     */
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);

        $redirect = $this->purchaseService->validatePurchaseable($item, $item_id);
        if ($redirect) {
            return $redirect;
        }

        $deliveryAddress = $this->purchaseService->getDeliveryAddressForEdit();

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

        $redirect = $this->purchaseService->validatePurchaseable($item, $item_id);
        if ($redirect) {
            return $redirect;
        }

        $delivery = $this->purchaseService->getDeliveryAddress();
        $paymentMethod = $request->validated()['payment_method'];

        $this->purchaseService->completePurchase($item, $delivery, $paymentMethod);
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

        $response = $this->purchaseService->validatePurchaseableForApi($item, $item_id);
        if ($response) {
            return $response;
        }

        $delivery = $this->purchaseService->getDeliveryAddress();

        try {
            $session = $this->stripeCheckoutService->createSession(
                $item,
                (int) $item_id,
                $delivery
            );

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

        try {
            $session = $this->stripeCheckoutService->retrieveSession($sessionId);

            $redirect = $this->stripeCheckoutService->validateSession($session, $item, $item_id);
            if ($redirect) {
                return $redirect;
            }

            $this->stripeCheckoutService->completePurchaseFromSession($item, $session);
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
