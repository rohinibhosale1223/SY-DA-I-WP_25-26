document.addEventListener("DOMContentLoaded", () => {
    const cartTableBody = document.getElementById("cart-table-body");
    const cartTotalElement = document.getElementById("cart-total");
    const cartFinalElement = document.getElementById("cart-final-total");
    
    const basePath = window.location.pathname.includes('/pages/') ? '../images/' : './images/';

    window.renderCart = function() {
        if (!cartTableBody || !cartTotalElement) return;
        
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cartTableBody.innerHTML = '';
        
        if (cart.length === 0) {
            cartTableBody.innerHTML = '<tr><td colspan="4" class="empty-cart">Your cart is empty. Time to gear up!</td></tr>';
            cartTotalElement.textContent = '$0.00';
            if (cartFinalElement) cartFinalElement.textContent = '$0.00';
            return;
        }
        
        let total = 0;
        
        cart.forEach((item) => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            const itemIdToPass = item.cartItemId ? `'${item.cartItemId}'` : item.id;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="cart-product-info">
                        <img src="${basePath}${item.image}" alt="${item.name}">
                        <span>${item.name}</span>
                    </div>
                </td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(${itemIdToPass}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateQuantity(${itemIdToPass}, 1)">+</button>
                    </div>
                </td>
                <td>$${subtotal.toFixed(2)}</td>
            `;
            cartTableBody.appendChild(tr);
        });
        
        cartTotalElement.textContent = `$${total.toFixed(2)}`;
        if (cartFinalElement) cartFinalElement.textContent = `$${total.toFixed(2)}`;
    };
    
    window.updateQuantity = function(itemId, change) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const item = cart.find(i => i.cartItemId === itemId || i.id === itemId);
        
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.cartItemId !== itemId && i.id !== itemId);
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
            if (window.updateCartCount) window.updateCartCount();
        }
    };
    
    renderCart();
});
