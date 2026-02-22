<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>送付先住所変更 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/purchase/address/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header', ['headerType' => 'login'])

    <main class="purchase-address-edit">
        <div class="purchase-address-edit-container">
            <h2 class="purchase-address-edit-title">住所の変更</h2>

            <form method="POST" action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" class="purchase-address-edit-form">
                @csrf
                @method('PUT')

                <div class="purchase-address-edit-field">
                    <label for="postal_code" class="purchase-address-edit-label">郵便番号</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $deliveryAddress['postal_code']) }}" class="purchase-address-edit-input @error('postal_code') is-invalid @enderror">
                    @error('postal_code')
                    <span class="purchase-address-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="purchase-address-edit-field">
                    <label for="address" class="purchase-address-edit-label">住所</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $deliveryAddress['address']) }}" class="purchase-address-edit-input @error('address') is-invalid @enderror">
                    @error('address')
                    <span class="purchase-address-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="purchase-address-edit-field">
                    <label for="building" class="purchase-address-edit-label">建物名</label>
                    <input type="text" id="building" name="building" value="{{ old('building', $deliveryAddress['building']) }}" class="purchase-address-edit-input @error('building') is-invalid @enderror">
                    @error('building')
                    <span class="purchase-address-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="purchase-address-edit-submit-btn">更新する</button>
            </form>
        </div>
    </main>
</body>

</html>
