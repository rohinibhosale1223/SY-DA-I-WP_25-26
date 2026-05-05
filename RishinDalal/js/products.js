// Global product store
window.products = [];

// Store selected sizes
window.selectedSizes = {};

window.selectSize = function(productId, size, event) {
    window.selectedSizes[productId] = size;
    
    // Update UI buttons
    const container = document.getElementById(`sizes-${productId}`);
    if (container) {
        const buttons = container.querySelectorAll('.size-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }
};

// Global addToCart function
window.addToCart = function(productId) {
    const product = window.products.find(p => p.id == productId);
    if (!product) return;
    
    const size = window.selectedSizes[productId] || 'S'; // Default to S
    const cartItemId = `${productId}-${size}`; // Unique ID for product+size
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    const existingItem = cart.find(item => item.cartItemId === cartItemId || (item.id === productId && !item.cartItemId));
    
    if (existingItem) {
        existingItem.quantity += 1;
        // Upgrade old items without cartItemId
        if (!existingItem.cartItemId) {
            existingItem.cartItemId = cartItemId;
            existingItem.name = `${product.name} (Size: ${size})`;
        }
    } else {
        cart.push({
            id: product.id,
            cartItemId: cartItemId,
            name: `${product.name} (Size: ${size})`,
            price: parseFloat(product.price.replace('$', '')),
            image: product.image,
            quantity: 1,
            size: size
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    
    if (window.updateCartCount) window.updateCartCount();
    alert(`${product.name} (Size ${size}) added to cart!`);
};

document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById("product-grid");
    
    if (grid) {
        const basePath = window.location.pathname.includes('/pages/') ? '../images/' : './images/';
        
        const renderProducts = (items) => {
            grid.innerHTML = '';
            
            if (items.length === 0) {
                grid.innerHTML = '<p class="no-results">No jerseys found matching your search.</p>';
                return;
            }
            
            items.forEach(product => {
                const card = document.createElement("div");
                card.className = "product-card";
                
                let tagHTML = '';
                if (product.tag) {
                    tagHTML = `<span class="product-tag">${product.tag}</span>`;
                }
                
                const detailLink = window.location.pathname.includes('/pages/') ? `product_detail.html?id=${product.id}` : `pages/product_detail.html?id=${product.id}`;
                
                card.innerHTML = `
                    <div class="product-image-container">
                        ${tagHTML}
                        <a href="${detailLink}">
                            <img src="${basePath}${product.image}" alt="${product.name}" class="product-image">
                        </a>
                    </div>
                    <div class="product-info">
                        <a href="${detailLink}" style="text-decoration:none; color:inherit;">
                            <h3 class="product-name">${product.name}</h3>
                        </a>
                        <p class="product-price">${product.price}</p>
                        
                        <div class="product-sizes" id="sizes-${product.id}">
                            <button class="size-btn active" onclick="selectSize(${product.id}, 'S', event)">S</button>
                            <button class="size-btn" onclick="selectSize(${product.id}, 'M', event)">M</button>
                            <button class="size-btn" onclick="selectSize(${product.id}, 'L', event)">L</button>
                            <button class="size-btn" onclick="selectSize(${product.id}, 'XL', event)">XL</button>
                        </div>
                        
                        <button class="add-to-cart-btn" onclick="addToCart(${product.id})">Add to Cart</button>
                    </div>
                `;
                
                grid.appendChild(card);
            });
        };

        const apiPath = window.location.pathname.includes('/pages/') ? '../backend/api.php' : './backend/api.php';
        
        // Fetch products from API
        fetch(`${apiPath}?action=get_products`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.products = data.products;
                    renderProducts(window.products);
                } else {
                    grid.innerHTML = '<p class="no-results">Error loading products.</p>';
                }
            })
            .catch(err => {
                console.error(err);
                grid.innerHTML = '<p class="no-results">Failed to connect to backend database.</p>';
            });
        
        // Search functionality
        const searchInput = document.getElementById("shop-search");
        if (searchInput) {
            searchInput.addEventListener("input", (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const filteredProducts = window.products.filter(p => 
                    p.name.toLowerCase().includes(searchTerm) || 
                    (p.tag && p.tag.toLowerCase().includes(searchTerm))
                );
                renderProducts(filteredProducts);
            });
        }
    }
});
