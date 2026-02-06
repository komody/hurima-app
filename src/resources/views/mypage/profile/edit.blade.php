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

            <form class="profile-edit-form">
                <div class="profile-edit-image-section">
                    <div class="profile-edit-image-wrapper">
                        <div class="profile-edit-image-placeholder"></div>
                    </div>
                    <label for="profile_image" class="profile-edit-image-button">画像を選択する</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="profile-edit-image-input" style="display: none;">
                </div>

                <div class="profile-edit-field">
                    <label for="name" class="profile-edit-label">ユーザー名</label>
                    <input type="text" id="name" name="name" class="profile-edit-input">
                </div>

                <div class="profile-edit-field">
                    <label for="postal_code" class="profile-edit-label">郵便番号</label>
                    <input type="text" id="postal_code" name="postal_code" class="profile-edit-input">
                </div>

                <div class="profile-edit-field">
                    <label for="address" class="profile-edit-label">住所</label>
                    <input type="text" id="address" name="address" class="profile-edit-input">
                </div>

                <div class="profile-edit-field">
                    <label for="building" class="profile-edit-label">建物名</label>
                    <input type="text" id="building" name="building" class="profile-edit-input">
                </div>

                <button type="submit" class="profile-edit-submit-btn">更新する</button>
            </form>
        </div>
    </main>

    <script>
        // 画像選択ボタンの処理
        document.querySelector('.profile-edit-image-button').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('profile_image').click();
        });

        // 画像プレビュー
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageWrapper = document.querySelector('.profile-edit-image-wrapper');
                    imageWrapper.innerHTML = '<img src="' + e.target.result + '" alt="プロフィール画像" class="profile-edit-image">';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>