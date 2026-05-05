document.addEventListener("DOMContentLoaded", () => {
    const detailContainer = document.getElementById("product-detail-container");
    
    if (detailContainer) {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        
        if (!productId) {
            detailContainer.innerHTML = '<p class="error-msg">Product not found.</p>';
            return;
        }

        const apiPath = window.location.pathname.includes('/pages/') ? '../backend/api.php' : './backend/api.php';
        
        fetch(`${apiPath}?action=get_products`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const product = data.products.find(p => p.id == productId);
                    if (product) {
                        renderProductDetail(product);
                    } else {
                        detailContainer.innerHTML = '<p class="error-msg">Product not found.</p>';
                    }
                } else {
                    detailContainer.innerHTML = '<p class="error-msg">Error loading product.</p>';
                }
            })
            .catch(err => {
                detailContainer.innerHTML = '<p class="error-msg">Failed to connect to backend.</p>';
            });

        function renderProductDetail(product) {
            const basePath = window.location.pathname.includes('/pages/') ? '../images/' : './images/';
            
            // Add global selectedSize logic for this specific page
            window.selectedDetailSize = 'S';
            window.selectDetailSize = function(size, event) {
                window.selectedDetailSize = size;
                const buttons = document.querySelectorAll('.detail-size-btn');
                buttons.forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
            };

            window.addDetailToCart = function() {
                const size = window.selectedDetailSize;
                const cartItemId = `${product.id}-${size}`;
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                const existingItem = cart.find(item => item.cartItemId === cartItemId || (item.id === product.id && !item.cartItemId));
                
                if (existingItem) {
                    existingItem.quantity += 1;
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

            let tagHTML = '';
            if (product.tag) {
                tagHTML = `<span class="detail-tag">${product.tag}</span>`;
            }

            // Description logic (fallback if none exists)
            const description = product.description || `Experience premium comfort and athletic performance with the official ${product.name}. Designed with breathable, sweat-wicking fabric to keep you cool on and off the pitch.`;

            detailContainer.innerHTML = `
                <div class="detail-image-wrapper">
                    ${tagHTML}
                    <img src="${basePath}${product.image}" alt="${product.name}" class="detail-image">
                </div>
                <div class="detail-info-wrapper">
                    <h1 class="detail-title">${product.name}</h1>
                    <p class="detail-price">${product.price}</p>
                    <p class="detail-desc">${description}</p>
                    
                    <div class="detail-sizes-section">
                        <h3>Select Size:</h3>
                        <div class="detail-sizes">
                            <button class="detail-size-btn active" onclick="selectDetailSize('S', event)">S</button>
                            <button class="detail-size-btn" onclick="selectDetailSize('M', event)">M</button>
                            <button class="detail-size-btn" onclick="selectDetailSize('L', event)">L</button>
                            <button class="detail-size-btn" onclick="selectDetailSize('XL', event)">XL</button>
                        </div>
                    </div>
                    
                    <button class="btn detail-cart-btn" onclick="addDetailToCart()">Add to Cart</button>
                </div>
            `;
        }
    }
});
