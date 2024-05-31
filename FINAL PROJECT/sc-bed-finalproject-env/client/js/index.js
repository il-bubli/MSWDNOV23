document.addEventListener("DOMContentLoaded", init);

const BASE_URI = "http://localhost:8000/kahuna/api/";
let products = [];
let registeredProducts = [];
let ticket = null;
let role;
let tickets = null;

//This has been modified from ToDo Pal and with some help of chatGPT, applied for different functions.

function init() {
  checkAndRedirect("home");

  document
    .getElementById("addProductLink")
    .addEventListener("click", function () {
      checkAndRedirect("products", loadProducts);
    });

  document
    .getElementById("myProductsLink")
    .addEventListener("click", function () {
      checkAndRedirect("registerProduct", loadRegisteredProducts);
    });

  document.getElementById("ticketsLink").addEventListener("click", function () {
    checkAndRedirect("adminTickets", loadAllTickets);
  });

  document.getElementById("loginLink").addEventListener("click", function () {
    checkAndRedirect("login");
  });

  document.getElementById("logoutLink").addEventListener("click", function () {
    logout();
  });
}

function renderNavigationLinks(role) {
  // Get the navigation links elements
  const productsLink = document.getElementById("addProductLink");
  const myProductsLink = document.getElementById("myProductsLink");
  const ticketsLink = document.getElementById("ticketsLink");

  // Check if the user is an agent or a regular user
  if (role === "agent") {
    // Show the Products link for agents
    productsLink.style.display = "block";
    myProductsLink.style.display = "none"; // Hide the My Products link for agents
    ticketsLink.style.display = "block";
  } else {
    // Hide the Products link for regular users
    productsLink.style.display = "none";
    myProductsLink.style.display = "block"; // Show the My Products link for regular users
    ticketsLink.style.display = "none";
  }
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
        role = res.data.role; // Set the role here
        showView(redirect).then(cb);
        renderNavigationLinks(role); // Render navigation links after setting the role
      });
  });
}

function bindAddProduct() {
  document.getElementById("productForm").addEventListener(
    "submit",
    (evt) => {
      evt.preventDefault();
      productData = new FormData(document.getElementById("productForm"));
      fetch(`${BASE_URI}product`, {
        mode: "cors",
        method: "POST",
        body: productData,
      })
        .then((response) => {
          if (!response.ok) {
            return response.json().then((data) => {
              throw new Error(data["error"]);
            });
          }
          return response.json();
        })
        .then((data) => {
          showMessage("Product registered successfully", "success");
          loadProducts();
        })
        .catch((err) => showMessage(err.message, "danger"));
    },
    { once: true }
  );
}

function loadProducts() {
  fetch(`${BASE_URI}product`, {
    mode: "cors",
    method: "GET",
  })
    .then((res) => res.json())
    .then((res) => {
      products = res.data;
      displayProducts();
      bindAddProduct();
    })
    .catch((err) => console.error(err));
}

function displayProducts() {
  let html = "";
  if (products.length === 0) {
    html = "<p>You have no products yet!</p>";
  } else {
    html = "<table><thead>";
    html += "<tr><th>Serial</th><th>Name</th><th>Warranty Length</th></tr>";
    html += "</thead><tbody>";

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
      showView("home");
    })
    .catch((err) => showMessage(err, "danger"));
}

function registerUser() {
  showView("registerUser").then(() => {
    document
      .getElementById("registerForm")
      .addEventListener("submit", (evt) => {
        evt.preventDefault();
        fetch(`${BASE_URI}user`, {
          mode: "cors",
          method: "POST",
          body: new FormData(document.getElementById("registerForm")),
        })
          .then(showView("login").then(() => bindLogin("home")))
          .catch((err) => showMessage(err, "danger"));
      });
  });
}

function bindRegisterProduct() {
  document.getElementById("registerProductForm").addEventListener(
    "submit",
    (evt) => {
      evt.preventDefault();
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
        .then((response) => {
          if (!response.ok) {
            return response.json().then((data) => {
              throw new Error(data["error"]);
            });
          }
          return response.json();
        })
        .then((data) => {
          showMessage("Product registered successfully", "success");
          loadRegisteredProducts();
        })
        .catch((err) => showMessage(err.message, "danger"));
    },
    { once: true }
  );
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
  const container = document.getElementById("registeredProducts");
  container.innerHTML = ""; // Clear any existing content

  registeredProducts.forEach((product, index) => {
    const warrantyLeft = calculateWarrantyTimeLeft(product.warrantyEndDate);
    const productElement = document.createElement("div");
    const warranty =
      warrantyLeft.years < 0 || warrantyLeft.months < 0 || warrantyLeft.days < 0
        ? "No"
        : "Yes";

    productElement.classList.add("card", "mb-3");
    productElement.style.width = "18rem"; // Fixed width
    productElement.style.margin = "0 auto"; // Center align the card

    productElement.innerHTML = `
      <div class="card-header text-center" id="heading${index}" style="cursor: pointer;">
        <h5 class="mb-0">
          <span>${product.name}</span>
        </h5>
      </div>
      <div id="collapse${index}" class="collapse" aria-labelledby="heading${index}" data-bs-parent="#registeredProducts">
        <div class="card-body">
          <p>Serial Number: ${product.serial}</p>
          <p>Warranty: ${warranty}</p>
          <p>Warranty Left: Years: ${warrantyLeft.years}, Months: ${warrantyLeft.months}, Days: ${warrantyLeft.days}</p>
          <button class="btn btn-primary btn-sm" id="ticketButton${index}">Create/View Ticket</button>
        </div>
      </div>
    `;

    productElement
      .querySelector(".card-header")
      .addEventListener("click", () => {
        const collapseElement = document.getElementById(`collapse${index}`);
        const isCollapsed = collapseElement.classList.contains("show");
        collapseElement.classList.toggle("show", !isCollapsed);
      });

    container.appendChild(productElement); // Add the product element to the container
    // Bind the ticket button event listener
    bindTicket(`#ticketButton${index}`, product.id);
  });
}

function bindTicket(buttonSelector, productId) {
  const button = document.querySelector(buttonSelector);
  if (button) {
    button.addEventListener("click", async () => {
      ticket = await loadTicket(productId);
      if (ticket) {
        checkAndRedirect("ticket", () => {
          displayTicket(ticket);
        });
      } else {
        checkAndRedirect("newTicket", () => {
          bindCreateTicket(productId);
        });
      }
    });
  } else {
    console.error(`Button with selector ${buttonSelector} not found.`);
  }
}

async function loadTicket(productId) {
  try {
    const response = await fetch(`${BASE_URI}ticket/${productId}`, {
      mode: "cors",
      method: "GET",
      headers: {
        "X-Api-Key": localStorage.getItem("kahuna_token"),
        "X-Api-User": localStorage.getItem("kahuna_user"),
      },
    });

    if (!response.ok) {
      throw new Error(`Error fetching ticket: ${response.statusText}`);
    }

    const res = await response.json();
    return res.data;
  } catch (err) {
    console.error("Error fetching ticket:", err);
    return null;
  }
}

function displayTicket(ticket) {
  const container = document.getElementById("ticketDetails");
  container.innerHTML = ""; // Clear any existing content

  const ticketDetail = document.createElement("div");
  ticketDetail.innerHTML = `
    <h3>Ticket Details</h3>
    <p><strong>Ticket ID:</strong> <span>${ticket.id}</span></p>
    <p><strong>User:</strong> <span>${ticket.email}</span></p>
    <p><strong>Product Name:</strong> <span>${ticket.productName}</span></p>
    <p><strong>Serial Number:</strong> <span>${ticket.serial}</span></p>
    <p><strong>Issue Description:</strong> <span>${ticket.issueDescription}</span></p>
    <p><strong>Status:</strong> <span>${ticket.status}</span></p>
    <p><strong>Submission Date:</strong> <span>${ticket.submissionDate}</span></p>
    `;
  // Iterate over ticket messages and get user email
  ticket.messages.forEach((message) => {
    getUserById(message.userId)
      .then((user) => {
        if (user) {
          ticketDetail.innerHTML += `<p><strong>Message:</strong> <span>${message.message} by: ${user.email}</span></p>`;
        }
      })
      .catch((error) => {
        console.error("Error fetching user:", error);
      });
  });
  container.appendChild(ticketDetail);
}

function getUserById(userId) {
  fetch(`${BASE_URI}user/${userId}`, {
    method: "GET",
    headers: {
      "X-Api-Key": localStorage.getItem("kahuna_token"),
      "X-Api-User": localStorage.getItem("kahuna_user"),
    },
  });

  if (!response.ok) {
    throw new Error(`Error fetching user: ${response.statusText}`);
  }

  (res) => res.json();
  (res) => {
    return res.data;
  };
}

function bindCreateTicket(productId) {
  checkAndRedirect("newTicket", () => {
    const submitButton = document.querySelector("#submitNewTicket");
    if (submitButton) {
      submitButton.addEventListener("click", (event) => {
        event.preventDefault();
        createTicket(productId);
      });
    } else {
      console.error("Submit button for new ticket not found.");
    }
  });
}

async function createTicket(productId) {
  try {
    const form = document.getElementById("createTicketForm");
    const newTicket = new FormData(form);
    newTicket.append("productId", productId);

    const response = await fetch(`${BASE_URI}ticket`, {
      mode: "cors",
      method: "POST",
      headers: {
        "X-Api-Key": localStorage.getItem("kahuna_token"),
        "X-Api-User": localStorage.getItem("kahuna_user"),
      },
      body: newTicket,
    });

    if (!response.ok) {
      throw new Error(`Error creating ticket: ${response.statusText}`);
    }

    const res = await response.json();
    alert("Ticket created successfully!");
  } catch (err) {
    console.error("Error creating ticket:", err);
    alert("Failed to create ticket.");
  }
}

async function loadAllTickets() {
  try {
    const response = await fetch(`${BASE_URI}allTickets`, {
      mode: "cors",
      method: "GET",
      headers: {
        "X-Api-Key": localStorage.getItem("kahuna_token"),
        "X-Api-User": localStorage.getItem("kahuna_user"),
      },
    });

    if (!response.ok) {
      throw new Error(`Error fetching tickets: ${response.statusText}`);
    }

    const res = await response.json();
    tickets = res.data;
    console.log("Tickets fetched successfully:", tickets); // Log the fetched tickets
    displayAllTickets(tickets); // Pass tickets to the display function
  } catch (error) {
    console.error("Error fetching tickets:", error);
  }
}

function displayAllTickets(tickets) {
  const container = document.getElementById("allTickets");
  container.innerHTML = ""; // Clear any existing content

  if (!tickets || tickets.length === 0) {
    container.innerHTML = "<p>No tickets available.</p>";
    return;
  }

  tickets.forEach((ticket) => {
    const ticketElement = document.createElement("div");
    ticketElement.classList.add("card", "mb-3"); // Add Bootstrap card classes for styling
    ticketElement.style.width = "100%";

    ticketElement.innerHTML = `
      <div class="card-header">
        <h5><strong>Ticket ID:</strong> ${ticket.id}</h5>
      </div>
      <div class="card-body">
        <p><strong>User:</strong> ${ticket.email}</p>
        <p><strong>Product Name:</strong> ${ticket.productName}</p>
        <p><strong>Serial Number:</strong> ${ticket.serial}</p>
        <p><strong>Status:</strong> ${ticket.status}</p>
        <p><strong>Issue Description:</strong> ${ticket.issueDescription}</p>
        <p><strong>Submission Date:</strong> ${ticket.submissionDate}</p>
        <div id="messages-${ticket.id}"></div>
        ${ticket.status === 'open' ? `
          <form id="replyForm-${ticket.id}" class="reply-form">
            <div class="mb-3">
              <textarea class="form-control" id="replyMessage-${ticket.id}" rows="3" placeholder="Type your reply here"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Reply</button>
          </form>
        ` : ''}
      </div>
    `;

    // Append ticket messages
    ticket.messages.forEach(async (message) => {
      try {
        const user = await getUserById(message.userId);
        if (user) {
          const messageElement = document.createElement("p");
          messageElement.innerHTML = `<strong>Message:</strong> ${message.message} by: ${user.email}`;
          const messagesContainer = ticketElement.querySelector(`#messages-${ticket.id}`);
          messagesContainer.appendChild(messageElement);
        }
      } catch (error) {
        console.error("Error fetching user for message:", error);
      }
    });

    if (ticket.status === 'open') {
      // Bind form submission event for replying to the ticket
      const replyForm = ticketElement.querySelector(`#replyForm-${ticket.id}`);
      replyForm.addEventListener("submit", async (event) => {
        event.preventDefault();
        const messageContent = ticketElement.querySelector(`#replyMessage-${ticket.id}`).value;
        if (messageContent) {
          try {
            await postReply(ticket.id, messageContent);
            const user = await getUserById(localStorage.getItem("kahuna_user"));
            if (user) {
              const messageElement = document.createElement("p");
              messageElement.innerHTML = `<strong>Message:</strong> ${messageContent} by: ${user.email}`;
              const messagesContainer = ticketElement.querySelector(`#messages-${ticket.id}`);
              messagesContainer.appendChild(messageElement);
            }
          } catch (error) {
            console.error("Error posting reply:", error);
          }
        }
      });
    }

    container.appendChild(ticketElement);
  });
}

async function postReply(ticketId, messageContent) {
  try {
    const response = await fetch(`${BASE_URI}message/${ticketId}`, {
      mode: "cors",
      method: "POST",
      headers: {
        "X-Api-Key": localStorage.getItem("kahuna_token"),
        "X-Api-User": localStorage.getItem("kahuna_user"),
        "Content-Type": "application/json"
      },
      body: messageContent
    });

    if (!response.ok) {
      throw new Error(`Error posting reply: ${response.statusText}`);
    }

    const res = await response.json();
    return res.data;
  } catch (error) {
    console.error("Error posting reply:", error);
    throw error;
  }
}

function isLeapYear(year) {
  return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
}

function calculateWarrantyTimeLeft(warrantyEndDate) {
  const now = new Date();
  const endDate = new Date(Date.parse(warrantyEndDate));

  // Calculate the difference in years, months, and days
  let years = endDate.getFullYear() - now.getFullYear();
  let months = endDate.getMonth() - now.getMonth();
  let days = endDate.getDate() - now.getDate();

  // Adjust months and years if days are negative
  if (days < 0) {
    months -= 1;
    days += new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate(); // Days in the current month
  }

  // Adjust years if months are negative
  if (months < 0) {
    years -= 1;
    months += 12;
  }

  // Adjust years if the current year and the end year are leap years
  if (isLeapYear(now.getFullYear()) && isLeapYear(endDate.getFullYear())) {
    if (
      now.getMonth() < endDate.getMonth() ||
      (now.getMonth() === endDate.getMonth() &&
        now.getDate() <= endDate.getDate())
    ) {
      years += 1;
    }
  }

  return { years, months, days };
}

async function getUserById(userId) {
  try {
    const response = await fetch(`${BASE_URI}user/${userId}`, {
      method: "GET",
      headers: {
        "X-Api-Key": localStorage.getItem("kahuna_token"),
        "X-Api-User": localStorage.getItem("kahuna_user"),
      },
    });

    if (!response.ok) {
      throw new Error(`Error fetching user: ${response.statusText}`);
    }

    const userData = await response.json();
    return userData.data; // Assuming the user data is returned as JSON containing the user object
  } catch (error) {
    console.error("Error fetching user:", error);
    return null;
  }
}

function showMessage(message, type) {
  const messageDiv = document.getElementById("alertMessage");
  messageDiv.textContent = message;
  messageDiv.className = `alert alert-${type}`;
}
