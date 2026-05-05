document.addEventListener('DOMContentLoaded', () => {
    const cartContainer = document.getElementById('cart-items-container');
    const cartTotalElement = document.getElementById('cart-total-amount');
    
    async function renderCart() {
        const currentUser = JSON.parse(localStorage.getItem('shoefy_user'));
        cartContainer.innerHTML = '';

        if (!currentUser) {
            cartContainer.innerHTML = '<tr><td colspan="6"><p class="empty-cart-msg">Please login to view your cart.</p></td></tr>';
            cartTotalElement.innerText = '0.00';
            return;
        }

        try {
            const res = await apiRequest('cart', 'GET', { user_id: currentUser.id });
            const cart = res.cart || [];
            
            if (cart.length === 0) {
                cartContainer.innerHTML = '<tr><td colspan="6"><p class="empty-cart-msg">Your cart is empty.</p></td></tr>';
                cartTotalElement.innerText = '0.00';
                return;
            }

            let total = 0;
            
            cart.forEach((item, index) => {
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity);
                total += itemTotal;
                
                const cartItem = document.createElement('tr');
                cartItem.className = 'cart-item-row';
                cartItem.innerHTML = `
                    <td class="cart-td-image">
                        <img src="../${item.image_url}" alt="${item.name}">
                    </td>
                    <td class="cart-td-name">
                        <h3>${item.name}</h3>
                    </td>
                    <td class="cart-td-price">
                        $${parseFloat(item.price).toFixed(2)}
                    </td>
                    <td class="cart-td-qty">
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.cart_id}, ${parseInt(item.quantity) - 1})">-</button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.cart_id}, ${parseInt(item.quantity) + 1})">+</button>
                        </div>
                    </td>
                    <td class="cart-td-total">
                        <strong>$${itemTotal.toFixed(2)}</strong>
                    </td>
                    <td class="cart-td-action">
                        <button class="remove-btn" onclick="removeItem(${item.cart_id})">Remove</button>
                    </td>
                `;
                cartContainer.appendChild(cartItem);
            });
            
            cartTotalElement.innerText = total.toFixed(2);
        } catch (e) {
            cartContainer.innerHTML = '<tr><td colspan="6"><p class="empty-cart-msg">Error loading cart.</p></td></tr>';
            cartTotalElement.innerText = '0.00';
        }
    }

    window.updateQuantity = async (cart_id, new_quantity) => {
        try {
            const currentUser = JSON.parse(localStorage.getItem('shoefy_user'));
            await apiRequest('cart', 'PUT', { cart_id, quantity: new_quantity, user_id: currentUser.id });
            renderCart();
            if (window.updateCartCount) window.updateCartCount();
        } catch (e) {
            alert(e.message);
        }
    };

    window.removeItem = async (cart_id) => {
        try {
            const currentUser = JSON.parse(localStorage.getItem('shoefy_user'));
            await apiRequest('cart', 'DELETE', { cart_id, user_id: currentUser.id });
            renderCart();
            if (window.updateCartCount) window.updateCartCount();
        } catch (e) {
            alert(e.message);
        }
    };

    renderCart();
});
