let products = [];

async function renderProducts(filteredProducts = null) {
    const grid = document.querySelector('.products-grid');
    if (!grid) return;

    if (!filteredProducts) {
        try {
            const res = await apiRequest('products');
            products = res.products || [];
            filteredProducts = products;
        } catch (e) {
            grid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center;">Error loading products.</p>';
            return;
        }
    }

    grid.innerHTML = '';
    const basePath = window.location.pathname.includes('/pages/') ? '../' : './';
    
    if (filteredProducts.length === 0) {
        grid.innerHTML = '<p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem; color: #555;">No products found.</p>';
        return;
    }
    
    filteredProducts.forEach(product => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.style.cursor = 'pointer';
        card.onclick = () => {
            const dest = window.location.pathname.includes('/pages/') ? 'product_detail.html' : 'pages/product_detail.html';
            window.location.href = `${dest}?id=${product.id}`;
        };
        card.innerHTML = `
            <div class="product-image-container">
                <img src="${basePath}${product.image_url}" alt="${product.name}">
            </div>
            <h3>${product.name}</h3>
            <p>$${parseFloat(product.price).toFixed(2)}</p>
            <button class="add-to-cart-btn">Add to Cart</button>
        `;
        
        const btn = card.querySelector('.add-to-cart-btn');
        btn.onclick = async (e) => {
            e.stopPropagation();
            
            const currentUser = JSON.parse(localStorage.getItem('shoefy_user'));
            if (!currentUser) {
                alert("Please login to add items to your cart.");
                const pagesPath = window.location.pathname.includes('/pages/') ? './' : 'pages/';
                window.location.href = `${pagesPath}login.html`;
                return;
            }

            try {
                await apiRequest('cart', 'POST', { 
                    user_id: currentUser.id, 
                    product_id: product.id, 
                    quantity: 1 
                });
                
                if (window.updateCartCount) window.updateCartCount();
                
                const originalText = btn.innerText;
                btn.innerText = 'Added!';
                btn.style.backgroundColor = '#4CAF50';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.backgroundColor = '';
                }, 1000);
            } catch (err) {
                alert(err.message);
            }
        };
        
        grid.appendChild(card);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    renderProducts();

    const searchInput = document.getElementById('product-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const filtered = products.filter(p => p.name.toLowerCase().includes(term));
            renderProducts(filtered);
        });
    }
});
