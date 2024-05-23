document.addEventListener("DOMContentLoaded", init);

const BASE_URI = "http://localhost:8000/kahuna/api/";
let products = [];
let registeredProducts = [];

function init() {
  setInitialColourMode();
  checkAndRedirect("home", loadProducts);
}

function setInitialColourMode() {
  let colorMode = localStorage.getItem("kahuna_color");
  if (colorMode) {
    toggleColourMode(colorMode);
  } else {
    toggleColourMode(
      window.matchMedia("(prefers-color-scheme: dark)") ? "dark" : "light"
    );
  }
}

function toggleColourMode(mode) {
  document.documentElement.setAttribute("data-bs-theme", mode);
  const switcher = document.getElementById("color-switch-area");
  if (mode === "dark") {
    switcher.innerHTML = '<i class="bi-moon-stars-fill"></i>';
  } else {
    switcher.innerHTML = '<i class="bi-sun-fill"></i>';
  }
  localStorage.setItem("kahuna_color", mode);
}

function loadProducts() {
  fetch(`${BASE_URI}product`, { mode: "cors", method: "GET" })
    .then((res) => res.json())
    .then((res) => {
      products = res.data;
      displayProducts();
      bindHome();
    })
    .catch((err) => console.error(err));
}

function displayProducts() {
  let html = "";
  if (products.length === 0) {
    html = "<p>You have no products yet!</p>";
  } else {
    html = "<table><thead>";
    html += "<tr><th>Serial</th><th>Name</th><th>Warrannty Length</th></tr>";
    html += "</thead><body>";

    for (const product of products) {
      html += "<tr>";
      html += `<td>${product.serial}</td>`;
      html += `<td>${product.name}</td>`;
      html += `<td>${product.warrantyLength}</td>`;
      html += "</tr>";
    }
    html += "</tbody></table>";
  }
  document.getElementById("productList").innerHTML = html;
}

function bindAddProduct() {
  document.getElementById("productForm").addEventListener("submit", (evt) => {
    evt.preventDefault();
    productData = new FormData(document.getElementById("productForm"));
    fetch(`${BASE_URI}product`, {
      mode: "cors",
      method: "POST",
      body: productData,
    })
      .then(loadProducts())
      .catch((err) => console.error(err));
  });
}

async function showView(view) {
  if (view) {
    return fetch(`includes/${view}.html`)
      .then((res) => res.text())
      .then(
        (html) => (document.getElementById("mainContent").innerHTML = html)
      );
  }
  return null;
}

async function isValidToken(token, user, cb) {
  return fetch(`${BASE_URI}token`, {
    headers: {
      "x-api-key": token,
      "x-api-user": user,
    },
  })
    .then((res) => res.json())
    .then((res) => cb(res.data.valid));
}

function getFormData(object) {
  const formData = new FormData();
  Object.keys(object).forEach((key) => formData.append(key, object[key]));
  return formData;
}

function checkAndRedirect(redirect = null, cb = null) {
  let token = localStorage.getItem("kahuna_token");

  if (!token) {
    showView("login").then(() => bindLogin(redirect, cb));
  } else {
    let user = localStorage.getItem("kahuna_user");
    isValidToken(token, user, (valid) => {
      if (valid) {
        showView(redirect).then(cb);
      } else {
        showView("login").then(() => bindLogin(redirect, cb));
      }
    });
  }
}

function bindLogin(redirect, cb) {
  document.getElementById("loginForm").addEventListener("submit", (evt) => {
    evt.preventDefault();
    fetch(`${BASE_URI}login`, {
      mode: "cors",
      method: "POST",
      body: new FormData(document.getElementById("loginForm")),
    })
      .then((res) => res.json())
      .then((res) => {
        localStorage.setItem("kahuna_token", res.data.token);
        localStorage.setItem("kahuna_user", res.data.user);
        showView(redirect).then(cb);
      })
      .catch((err) => showMessage(err, "danger"));
  });
}

function bindHome() {
  document.getElementById("productForm").addEventListener("submit", (evt) => {
    evt.preventDefault();
    kahunaData = new FormData(document.getElementById("productForm"));
    checkAndRedirect("home", () => {
      fetch(`${BASE_URI}product`, {
        mode: "cors",
        method: "POST",
        headers: {
          "X-Api-Key": localStorage.getItem("kahuna_token"),
          "X-Api-User": localStorage.getItem("kahuna_user"),
        },
        body: kahunaData,
      })

        .then(loadProducts)
        .catch((err) => showMessage(err, "danger"));
    });
  });
}

function logout() {
  fetch(`${BASE_URI}logout`, {
    mode: "cors",
    method: "POST",
    headers: {
      "X-Api-Key": localStorage.getItem("kahuna_token"),
      "X-Api-User": localStorage.getItem("kahuna_user"),
    },
  })
    .then((res) => {
      localStorage.removeItem("kahuna_token");
      localStorage.removeItem("kahuna_user");
      checkAndRedirect("home", loadProducts);
    })
    .catch((err) => showMessage(err, "danger"));
}

function registerUser() {
  showView("register").then(() => {
    document
      .getElementById("registerForm")
      .addEventListener("submit", (evt) => {
        evt.preventDefault();
        fetch(`${BASE_URI}user`, {
          mode: "cors",
          method: "POST",
          body: new FormData(document.getElementById("registerForm")),
        })
          .then(showView("login").then(() => bindLogin("home", bindHome)))
          .catch((err) => showMessage(err, "danger"));
      });
  });
}

function registerProductPage() {
  checkAndRedirect("registerProduct", loadRegisteredProducts);
}

function bindRegisterProduct() {
  document.getElementById("registerProductForm").addEventListener("submit", (event) => {
    event.preventDefault();
        serial = new FormData(document.getElementById("registerProductForm"));
        fetch(`${BASE_URI}registerProduct`, {
          mode: "cors",
          method: "POST",
          headers: {
            "X-Api-Key": localStorage.getItem("kahuna_token"),
            "X-Api-User": localStorage.getItem("kahuna_user"),
          }, 
          body: serial,
        })
        .then((res) => res.json())
        .then((res) => {
          console.log(res.data);
      })
      .catch((err) => console.log(err));
});
}


function loadRegisteredProducts() {
  fetch(`${BASE_URI}registerProduct`, {
    mode: "cors",
    method: "GET",
    headers: {
      "X-Api-Key": localStorage.getItem("kahuna_token"),
      "X-Api-User": localStorage.getItem("kahuna_user"),
    },
  })
    .then((res) => res.json())
    .then((res) => {
      registeredProducts = res.data;
      displayRegisteredProducts();
      bindRegisterProduct();
    })
    .catch((err) => console.error(err));
}

function displayRegisteredProducts() {
  lethtml = "";
  if (registeredProducts.length === 0) {
    html = "<p>You have no products registered yet!</p>";
  } else {
    html = "<table><thead>";
    html +=
      "<tr><th>Serial</th><th>Name</th><th>Purchase Date</th><th>Warranty End Date</th></tr>";
    html += "</thead><body>";

    for (const registeredProduct of registeredProducts) {
      html += "<tr>";
      html += `<td>${registeredProduct.serial}</td>`;
      html += `<td>${registeredProduct.name}</td>`;
      html += `<td>${registeredProduct.purchaseDate}</td>`;
      html += `<td>${registeredProduct.warrantyEndDate}</td>`;
      html += "</tr>";
    }
    html += "</tbody></table>";
  }
  document.getElementById("registeredProducts").innerHTML = html;
}

function showMessage(message, type) {
  const messageDiv = document.getElementById('registerProductMessage');
  messageDiv.textContent = message;
  messageDiv.className = `alert alert-${type}`;
}