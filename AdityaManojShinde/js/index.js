const navbar = document.getElementById("navbar");

// Check if the current page is inside the 'pages' directory
const isSubpage = window.location.pathname.includes('/pages/');
const basePath = isSubpage ? '../' : './';
const pagesPath = isSubpage ? './' : 'pages/';

let user = null;
try {
    user = JSON.parse(localStorage.getItem('shoefy_user'));
} catch (e) {
    console.error("Failed to parse user from localStorage", e);
    localStorage.removeItem('shoefy_user');
}

navbar.innerHTML = `
    <nav class="navbar">
        <div class="logo">Shoefy</div>
        <ul class="nav-links">
            <li><a href="${basePath}index.html">Home</a></li>
            <li><a href="${pagesPath}product.html">Products</a></li>
            <li><a href="${pagesPath}about.html">About</a></li>
            <li><a href="${pagesPath}contact.html">Contact</a></li>
        </ul>
        <div class="cta-btns">
            <button class="cart-btn" onclick="window.location.href='${pagesPath}cart.html'">Cart (<span id="cart-count">0</span>)</button>
            ${user ? 
                `<button class="login-btn" onclick="logout()">Logout ${(user.full_name ? '(' + user.full_name.split(' ')[0] + ')' : '')}</button>` : 
                `<button class="login-btn" onclick="window.location.href='${pagesPath}login.html'">Login</button>`
            }
        </div>
    </nav>
`;

window.logout = () => {
    localStorage.removeItem('shoefy_user');
    window.location.reload();
};

async function updateCartCount() {
    const countElement = document.getElementById('cart-count');
    if (!countElement) return;

    if (!user) {
        countElement.innerText = '0';
        return;
    }

    try {
        const result = await apiRequest('cart', 'GET', { user_id: user.id });
        const cart = result.cart || [];
        const totalItems = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0);
        countElement.innerText = totalItems;
    } catch (e) {
        countElement.innerText = '0';
    }
}

// Initialize count
updateCartCount();

// Expose globally so other scripts can update it
window.updateCartCount = updateCartCount;
