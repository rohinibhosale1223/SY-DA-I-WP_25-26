function addToCart(name, price) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.push({name, price});
  localStorage.setItem("cart", JSON.stringify(cart));
  alert("Added to cart");
}

// Register
function registerUser(e) {
  e.preventDefault();
   let email = document.getElementById("email").value;

  if (!email.includes("@")) {
    alert("Invalid Email");
    return;
  }


  let user = {
    name: document.getElementById("name").value,
    email: document.getElementById("email").value,
    password: document.getElementById("password").value
  };

  localStorage.setItem("user", JSON.stringify(user));
  alert("Registered Successfully");
  return false;
}

// Login
function loginUser(e) {
  e.preventDefault();

  let email = document.getElementById("loginEmail").value;
  let password = document.getElementById("loginPassword").value;

  let user = JSON.parse(localStorage.getItem("user"));

  if(user && user.email === email && user.password === password){
    alert("Login Successful");
    window.location.href = "index.html";
  } else {
    alert("Invalid Credentials");
  }
}