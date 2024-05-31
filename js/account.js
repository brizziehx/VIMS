'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
    (function () {
        let accountUserImage = document.getElementById('uploadedAvatar');
        const fileInput = document.querySelector('.file-input');

        if (accountUserImage) {
            fileInput.addEventListener('change', () => {
                if (fileInput.files[0]) {
                    accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
                }
            });
        }
    })();
});