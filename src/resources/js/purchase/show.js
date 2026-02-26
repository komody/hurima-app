// 支払い方法の選択時に右側の表示を更新
document.getElementById('payment_method').addEventListener('change', function () {
  const displayEl = document.getElementById('payment_method_display');
  const selectedOption = this.options[this.selectedIndex];
  displayEl.textContent = selectedOption.value || '選択してください';
});

document.getElementById('buy-btn').addEventListener('click', async function () {
  const main = document.querySelector('.purchase-show');
  const checkoutUrl = main.dataset.checkoutUrl;
  const convenienceUrl = main.dataset.convenienceUrl;
  const csrfToken = main.dataset.csrf;

  const paymentMethod = document.getElementById('payment_method').value;
  const errorEl = document.getElementById('payment_method_client_error');
  const selectEl = document.getElementById('payment_method');

  // クライアント側バリデーション：エラー表示をリセット
  errorEl.style.display = 'none';
  errorEl.textContent = '';
  selectEl.classList.remove('purchase-show-select-error');

  if (!paymentMethod) {
    errorEl.textContent = '支払い方法を選択してください。';
    errorEl.style.display = 'block';
    selectEl.classList.add('purchase-show-select-error');
    return;
  }

  if (paymentMethod === 'コンビニ払い') {
    const btn = this;
    btn.disabled = true;
    btn.textContent = '処理中...';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = convenienceUrl;
    form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="payment_method" value="${paymentMethod}">
        `;
    document.body.appendChild(form);
    form.submit();
    return;
  }

  if (paymentMethod === 'カード支払い') {
    const btn = this;
    btn.disabled = true;
    btn.textContent = '処理中...';

    try {
      const res = await fetch(checkoutUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
        },
      });
      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'エラーが発生しました。');
      }

      if (data.url) {
        window.location.href = data.url;
      } else {
        throw new Error('決済画面のURLを取得できませんでした。');
      }
    } catch (err) {
      alert(err.message || 'エラーが発生しました。');
      btn.disabled = false;
      btn.textContent = '購入する';
    }
  }
});
