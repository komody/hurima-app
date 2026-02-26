<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeCheckoutService
{
  /**
   * Checkout Session を作成
   *
   * @throws ApiErrorException
   */
  public function createSession(Item $item, int $itemId, array $delivery): \Stripe\Checkout\Session
  {
    Stripe::setApiKey(config('services.stripe.secret'));

    $successUrl = url("/purchase/{$itemId}/success") . '?session_id={CHECKOUT_SESSION_ID}';
    $cancelUrl = route('purchase.show', ['item_id' => $itemId]);

    return Session::create([
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
  }

  /**
   * セッションを取得
   *
   * @throws ApiErrorException
   */
  public function retrieveSession(string $sessionId)
  {
    Stripe::setApiKey(config('services.stripe.secret'));

    return Session::retrieve($sessionId);
  }

  /**
   * Stripe決済成功後の購入処理を実行
   */
  public function completePurchaseFromSession(Item $item, $session): void
  {
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
  }

  /**
   * Stripeセッションの検証
   */
  public function validateSession($session, Item $item, $itemId): ?\Illuminate\Http\RedirectResponse
  {
    if ($session->payment_status !== 'paid') {
      return redirect()
        ->route('purchase.show', ['item_id' => $itemId])
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

    return null;
  }
}
