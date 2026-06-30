document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function () {
            navMenu.classList.toggle('open');
        });
    }

    const registerForm = document.querySelector('form[data-validate="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            const password = registerForm.querySelector('input[name="password"]').value;
            const confirm = registerForm.querySelector('input[name="confirm_password"]').value;
            if (password.length < 6) {
                alert('Password must be at least 6 characters.');
                event.preventDefault();
            } else if (password !== confirm) {
                alert('Passwords do not match.');
                event.preventDefault();
            }
        });
    }
});
