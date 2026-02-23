/******/ (() => { // webpackBootstrap
/*!*************************************!*\
  !*** ./resources/js/sell/create.js ***!
  \*************************************/
document.addEventListener('DOMContentLoaded', function () {
  var dropdown = document.getElementById('condition-dropdown');
  var trigger = dropdown.querySelector('.custom-dropdown-trigger');
  var valueDisplay = dropdown.querySelector('.custom-dropdown-value');
  var hiddenInput = dropdown.querySelector('input[name="condition_id"]');
  var list = dropdown.querySelector('.custom-dropdown-list');
  var options = dropdown.querySelectorAll('.custom-dropdown-option');

  // 初期表示（old値がある場合）
  var selectedOption = dropdown.querySelector('.custom-dropdown-option[data-selected]');
  if (selectedOption) {
    valueDisplay.textContent = selectedOption.textContent.trim();
    valueDisplay.classList.add('has-value');
    selectedOption.classList.add('selected');
  }

  // 開閉
  trigger.addEventListener('click', function (e) {
    e.stopPropagation();
    dropdown.classList.toggle('open');
  });

  // オプション選択
  options.forEach(function (option) {
    option.addEventListener('click', function (e) {
      e.stopPropagation();
      options.forEach(function (o) {
        return o.classList.remove('selected');
      });
      option.classList.add('selected');
      hiddenInput.value = option.dataset.value;
      valueDisplay.textContent = option.textContent.trim();
      valueDisplay.classList.add('has-value');
      dropdown.classList.remove('open');
    });
  });

  // 外側クリックで閉じる
  document.addEventListener('click', function () {
    dropdown.classList.remove('open');
  });
});
/******/ })()
;