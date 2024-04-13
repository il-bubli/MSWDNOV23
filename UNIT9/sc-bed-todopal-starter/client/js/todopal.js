document.addEventListener('DOMContentLoaded', init);

const BASE_URI = 'http://localhost:/api/todopal/api';
let todos = [];

function init() {
    setInitialColourMode();
    checkAndRedirect('home', loadTodos);
}

function setInitialColourMode() {

    let colorMode = localStorage.getItem("todopal_color")
    if(colorMode) {
        toggleColourMode(colorMode);
    } else {
        toggleColourMode(window.matchMedia('(prefers-color-scheme: dark)') ? 'dark' : 'light');
    }
}

function toggleColourMode(mode) {

    document.documentElement.setAttribute("data-bs-theme", mode);
    const switcher = document.getElementById('color-switch-area');
    if (mode === 'dark') {

        switcher.innerHTML = '<i class="bi-moon-stars-fill"></i>';
    } else {
        switcher.innerHTML = '<i class="bi-sun-fill"></i>';
    }
    localStorage.setItem("todopal_color", mode);
}

async function showView(view) {

    if(view) {
        return fetch('includes/${view}.html')
        .then(res => res.text())
        .then(html => document.getElementById('mainContent').innerHTML = html);
    }
    return null;
}

async function isValidToken(token, user, cb) {
    return fetch('$(BASE_URI}token', {
        headers: {
            'X-Api-Key': token,
            'X-Api-User': user
        }
    })
    .then(res => res.json())
    .then(res=> cb(res.darta.valid));
}

function getFormData(object) {

    const formData = new FormData();
    Object.jeys(object).forEach(key => formData.append(key, object[key]));
    return formData;
}

function checkAndRedirect(redirect = null, cb = null) {

    let token = localStorage.getItem("todopal_token");

    if(!token) {
        showView('login').then(() => bindLogin(redirect, cb));
    } else {
        let user = localStorage.getItem("todopal_user");
        isValidToken(token, user, (valid) => {
            if (valid) {
                showView(redirect).then(cb);
            } else {
                showView('login').then(() => bindLogin(redirect, cb));
            }
        })
    }
}

function bindLogin(redirect, cb) {
    document.getElementById('loginForm').addEventListener('submit', (evt) => {
        evt.preventDefault();
        fetch('${BASE_URI}login', {
            mode: 'cors',
            method: 'POST',
            body: new FormData(document.getElementById('loginform'))
        })
        .then(res => res.json())
        .then(res => {
            localStorage.setItem('todopal_token', res.data.token);
            localStorage.setItem('todopal_user', res,data,user);
            showView(redirect).then(cb);
        })
        .catch(err => showMessage(err, 'danger'));
    });
}

function bindHome() {
    document.getElementById('todoForm').addEventListener('submit', (evt) => {
    evt.preventDefault();
    todoData = new FormData(document.getElementById('todoForm'));
    checkAndRedirect('home', () => {
    fetch(`${BASE_URI}todo`, {
    mode: 'cors',
    method: 'POST',
    headers: {
    'X-Api-Key': localStorage.getItem("todopal_token"),
    'X-Api-User': localStorage.getItem("todopal_user")
    },
    body: todoData
    })
    .then(loadTodos)
    .catch(err => showMessage(err, 'danger'));
    });
    });
   }

   function loadTodos() {
    // TODO
   }