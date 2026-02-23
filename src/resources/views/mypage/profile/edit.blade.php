<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール設定 - Hurima</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mypage/profile/edit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/header.css') }}">
</head>

<body>
    @include('layouts.header', ['headerType' => 'login'])

    <main class="profile-edit">
        <div class="profile-edit-container">
            <h1 class="profile-edit-title">プロフィール設定</h1>

            <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data" class="profile-edit-form">
                @csrf
                @method('PUT')

                <div class="profile-edit-image-section">
                    <div class="profile-edit-image-wrapper">
                        @if(auth()->user()->account?->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->account->profile_image) }}" alt="プロフィール画像" class="profile-edit-image">
                        @else
                        <div class="profile-edit-image-placeholder"></div>
                        @endif
                    </div>
                    <div class="profile-edit-image-button-wrapper">
                        <label for="profile_image" class="profile-edit-image-button">画像を選択する</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-edit-image-input" style="display: none;">
                        @error('profile_image')
                        <span class="profile-edit-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="profile-edit-field">
                    <label for="name" class="profile-edit-label">ユーザー名</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" class="profile-edit-input @error('name') is-invalid @enderror">
                    @error('name')
                    <span class="profile-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-edit-field">
                    <label for="postal_code" class="profile-edit-label">郵便番号</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', auth()->user()->account?->postal_code) }}" class="profile-edit-input @error('postal_code') is-invalid @enderror">
                    @error('postal_code')
                    <span class="profile-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-edit-field">
                    <label for="address" class="profile-edit-label">住所</label>
                    <input type="text" id="address" name="address" value="{{ old('address', auth()->user()->account?->address) }}" class="profile-edit-input @error('address') is-invalid @enderror">
                    @error('address')
                    <span class="profile-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-edit-field">
                    <label for="building" class="profile-edit-label">建物名</label>
                    <input type="text" id="building" name="building" value="{{ old('building', auth()->user()->account?->building) }}" class="profile-edit-input @error('building') is-invalid @enderror">
                    @error('building')
                    <span class="profile-edit-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="profile-edit-submit-btn">更新する</button>
            </form>
        </div>
    </main>

    <script src="{{ asset('js/mypage/profile/edit.js') }}"></script>
</body>

</html>