const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const panelLogin = document.getElementById('panelLogin');
const panelRegistro = document.getElementById('panelRegistro');

function mostrarRegistro() {
    loginForm.classList.add('hidden');
    registerForm.classList.remove('hidden');
    panelLogin.classList.add('hidden');
    panelRegistro.classList.remove('hidden');
}

function mostrarLogin() {
    registerForm.classList.add('hidden');
    loginForm.classList.remove('hidden');
    panelRegistro.classList.add('hidden');
    panelLogin.classList.remove('hidden');
}