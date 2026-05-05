document.addEventListener("DOMContentLoaded", () => {
    const checkoutForm = document.getElementById("checkout-form");
    
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", (e) => {
            e.preventDefault();
            
            // Gather shipping details
            const shipping = {
                full_name: document.getElementById("fullName").value,
                address: document.getElementById("address").value,
                city: document.getElementById("city").value,
                state: document.getElementById("state").value,
                zip: document.getElementById("zip").value,
                country: document.getElementById("country").value
            };
            
            // Gather cart from local storage
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            if (cart.length === 0) {
                alert("Your cart is empty!");
                return;
            }
            
            // Call Backend API
            fetch('../backend/api.php?action=create_order', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ shipping, cart })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Empty the cart
                    localStorage.setItem('cart', JSON.stringify([]));
                    
                    // Update the global cart count in the navbar
                    if (window.updateCartCount) {
                        window.updateCartCount();
                    }
                    
                    // Show success message and redirect
                    alert(`Payment successful! Your order (ID: #${data.order_id}) has been placed. Thank you for shopping at The Jersey Adda.`);
                    window.location.href = "../index.html";
                } else {
                    alert(`Order failed: ${data.error}`);
                }
            })
            .catch(err => {
                alert("Error connecting to server to process order.");
            });
        });
    }
});
