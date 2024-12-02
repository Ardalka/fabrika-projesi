const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');
const showPassword = document.getElementById('showPassword');
const themeToggle = document.getElementById('themeToggle');
const body = document.body;

// Kayıt ve Giriş Paneli Geçişleri
signUpButton.addEventListener('click', () => container.classList.add('right-panel-active'));
signInButton.addEventListener('click', () => container.classList.remove('right-panel-active'));

// Şifre Göster/Gizle
showPassword.addEventListener('change', () => {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.type = showPassword.checked ? 'text' : 'password';
    });
});

// Tema Değiştirme
themeToggle.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
});
