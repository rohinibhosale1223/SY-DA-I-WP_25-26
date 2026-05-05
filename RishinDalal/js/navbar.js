document.addEventListener("DOMContentLoaded", () => {
    // Determine the base path based on whether we are in the pages folder or root
    const isPageDir = window.location.pathname.includes('/pages/');
    const homeLink = isPageDir ? '../index.html' : './index.html';
    const shopLink = isPageDir ? './shop.html' : './pages/shop.html';
    const aboutLink = isPageDir ? './about.html' : './pages/about.html';
    const contactLink = isPageDir ? './contact.html' : './pages/contact.html';
    const loginLink = isPageDir ? './login.html' : './pages/login.html';
    const cartLink = isPageDir ? './cart.html' : './pages/cart.html';
    
    const userName = localStorage.getItem('user_name');
    
    let userAuthHTML = `<a href="${loginLink}" class="btn login-btn" title="Login">Login</a>`;
    if (userName) {
        userAuthHTML = `
            <div class="user-menu" style="display:flex; align-items:center; gap:10px;">
                <span class="btn login-btn" style="cursor:default; background:transparent; border:1px solid var(--accent-color, #fca311);">${userName}</span>
                <a href="#" onclick="logoutUser(event)" style="color:var(--accent-color, #fca311); font-size:0.9rem; text-decoration:none; font-weight:600;">Logout</a>
            </div>
        `;
    }
    const navbarHTML = `
        <nav class="navbar">
            <div class="nav-brand">
                <a href="${homeLink}">The Jersey Adda</a>
            </div>
            <ul class="nav-links">
                <li><a href="${homeLink}">Home</a></li>
                <li><a href="${shopLink}">Shop</a></li>
                <li><a href="${aboutLink}">About</a></li>
                <li><a href="${contactLink}">Contact</a></li>
            </ul>
            <div class="nav-icons">
                ${userAuthHTML}
                <a href="${cartLink}" class="btn cart-btn" title="Cart">
                    🛒 <span class="cart-count">0</span>
                </a>
            </div>
        </nav>
    `;

    // Inject the navbar
    const header = document.querySelector('header');
    if (header && header.textContent.trim().toLowerCase() === 'navbar') {
        header.innerHTML = navbarHTML;
    } else {
        const navContainer = document.createElement('div');
        navContainer.innerHTML = navbarHTML;
        document.body.insertBefore(navContainer, document.body.firstChild);
    }
    
    // Global Logout Function
    window.logoutUser = function(e) {
        if(e) e.preventDefault();
        localStorage.removeItem('user_name');
        const apiPath = isPageDir ? '../backend/api.php?action=logout' : './backend/api.php?action=logout';
        
        fetch(apiPath, { method: 'POST' })
            .then(() => {
                window.location.reload();
            })
            .catch(() => {
                // If backend fails, still reload to clear UI
                window.location.reload();
            });
    };
    
    // Global Cart Count Update
    window.updateCartCount = function() {
        const countElements = document.querySelectorAll('.cart-count');
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        countElements.forEach(el => {
            el.textContent = totalItems;
        });
    };
    
    window.updateCartCount();
});
