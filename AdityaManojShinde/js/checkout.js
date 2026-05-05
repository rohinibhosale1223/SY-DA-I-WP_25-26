document.addEventListener('DOMContentLoaded', async () => {
    let currentUser = null;
    try {
        currentUser = JSON.parse(localStorage.getItem('shoefy_user'));
    } catch (e) {}

    if (!currentUser) {
        alert("Please login to checkout.");
        window.location.href = './login.html';
        return;
    }

    const totalElement = document.getElementById('checkout-total');
    let cartItems = [];

    // Fetch cart to get total
    try {
        const res = await apiRequest('cart', 'GET', { user_id: currentUser.id });
        cartItems = res.cart || [];
        
        if (cartItems.length === 0) {
            alert("Your cart is empty. Please add items before checking out.");
            window.location.href = './product.html';
            return;
        }

        let total = 0;
        cartItems.forEach(item => {
            total += parseFloat(item.price) * parseInt(item.quantity);
        });

        totalElement.innerText = `$${total.toFixed(2)}`;
    } catch (e) {
        console.error(e);
        alert("Error loading cart details.");
        window.location.href = './cart.html';
        return;
    }

    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const payBtn = document.querySelector('.pay-btn');
        payBtn.innerText = 'Processing...';
        payBtn.disabled = true;

        try {
            // Delete all items from the cart to "empty" it
            for (const item of cartItems) {
                await apiRequest('cart', 'DELETE', { 
                    cart_id: item.cart_id, 
                    user_id: currentUser.id 
                });
            }

            // Update global cart count if function exists
            if (window.updateCartCount) window.updateCartCount();

            alert('Payment successful! Your order is on the way.');
            window.location.href = '../index.html';
        } catch (err) {
            alert('An error occurred while processing your payment. Please try again.');
            payBtn.innerText = 'Complete Payment';
            payBtn.disabled = false;
        }
    });
});
