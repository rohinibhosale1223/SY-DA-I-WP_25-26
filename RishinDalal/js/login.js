document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    const signupForm = document.getElementById("signup-form");
    const showSignupBtn = document.getElementById("show-signup");
    const showLoginBtn = document.getElementById("show-login");

    if (showSignupBtn && showLoginBtn) {
        showSignupBtn.addEventListener("click", (e) => {
            e.preventDefault();
            loginForm.classList.add("hidden");
            signupForm.classList.remove("hidden");
        });

        showLoginBtn.addEventListener("click", (e) => {
            e.preventDefault();
            signupForm.classList.add("hidden");
            loginForm.classList.remove("hidden");
        });
    }

    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const email = document.getElementById("login-email").value;
            const password = document.getElementById("login-password").value;

            fetch('../backend/api.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem('user_name', data.user.name);
                    alert(`Welcome back, ${data.user.name}!`);
                    window.location.href = '../index.html';
                } else {
                    alert(`Login failed: ${data.error}`);
                }
            })
            .catch(err => alert("Error connecting to server."));
        });
    }

    if (signupForm) {
        signupForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const full_name = document.getElementById("signup-name").value;
            const email = document.getElementById("signup-email").value;
            const password = document.getElementById("signup-password").value;

            fetch('../backend/api.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ full_name, email, password })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Account created successfully! Please log in.');
                    signupForm.classList.add("hidden");
                    loginForm.classList.remove("hidden");
                } else {
                    alert(`Signup failed: ${data.error}`);
                }
            })
            .catch(err => alert("Error connecting to server."));
        });
    }
});
