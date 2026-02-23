/******/ (() => { // webpackBootstrap
/*!*********************************************!*\
  !*** ./resources/js/mypage/profile/edit.js ***!
  \*********************************************/
document.querySelector('.profile-edit-image-button').addEventListener('click', function (e) {
  e.preventDefault();
  document.getElementById('profile_image').click();
});
document.getElementById('profile_image').addEventListener('change', function (e) {
  var file = e.target.files[0];
  if (file) {
    var reader = new FileReader();
    reader.onload = function (e) {
      var imageWrapper = document.querySelector('.profile-edit-image-wrapper');
      imageWrapper.innerHTML = '<img src="' + e.target.result + '" alt="プロフィール画像" class="profile-edit-image">';
    };
    reader.readAsDataURL(file);
  }
});
/******/ })()
;