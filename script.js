// ============================================
// PRODUCT DATA
// ============================================
const products = [
    { 
        id: 1, 
        name: "Vetkoek", 
        price: 10, 
        rating: 4.7, 
        reviews: 32, 
        location: "Soweto, Johannesburg", 
        seller: "Mama's Fresh Bakery", 
        sellerRating: 4.8, 
        sellerSales: 234, 
        description: "Freshly made daily. Traditional South African vetkoek filled with mince or jam, served with homemade koeksisters. Perfect for breakfast or lunch.", 
        deliveryInfo: "Pick up from Ncube Spaza - Soweto (2km from you). Free delivery to pickup point.",
        image: "images/vetkoek.jpg"
    },
    { 
        id: 2, 
        name: "Sneakers - Nike", 
        price: 450, 
        rating: 4.6, 
        reviews: 18, 
        location: "Sandton, Johannesburg", 
        seller: "SneakerHub SA", 
        sellerRating: 4.8, 
        sellerSales: 234, 
        description: "Brand new Nike Air Max. Size 42. Original box included. Free pickup from spaza shop.", 
        deliveryInfo: "Pick up from Ncube Spaza - Soweto (2km from you). Free delivery to pickup point.",
        image: "images/sneakers.jpg"
    },
    { 
        id: 3, 
        name: "Traditional Beaded Necklace", 
        price: 120, 
        rating: 4.9, 
        reviews: 50, 
        location: "Durban, KZN", 
        seller: "Nguni Crafts", 
        sellerRating: 4.9, 
        sellerSales: 156, 
        description: "Handmade authentic Zulu beaded necklace. Beautiful craftsmanship, perfect for cultural events or everyday wear. Each piece is unique.", 
        deliveryInfo: "Pick up from Dlamini Market - Durban (1km from you). Free delivery to pickup point.",
        image: "images/necklace.jpg"
    }
];

let cart = [];
const storageKey = 'safehubCart';

// ============================================
// CART FUNCTIONS
// ============================================
function loadCart() {
    const savedCart = localStorage.getItem(storageKey);
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
        } catch(e) {
            cart = [];
        }
    }
    updateCartCount();
}

function saveCart() {
    localStorage.setItem(storageKey, JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
    }
}

function addToCart(id, name, price, quantity = 1) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({ id, name, price, quantity: quantity });
    }
    
    saveCart();
    renderCartPage();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
    renderCartPage();
    updateCheckoutTotal();
}

function updateQuantity(id, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(id);
        return;
    }
    
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity = newQuantity;
        saveCart();
        renderCartPage();
        updateCheckoutTotal();
    }
}

function getTotal() {
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

function renderCartPage() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotalElement = document.getElementById('cartTotal');
    
    if (!cartItemsContainer) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p style="text-align:center; padding:40px;">Your cart is empty.<br>Start shopping!</p>';
        if (cartTotalElement) cartTotalElement.textContent = 'Total: R0';
        return;
    }
    
    let html = '';
    let total = 0;
    
    for (let i = 0; i < cart.length; i++) {
        const item = cart[i];
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        html += `
            <div class="cart-item">
                <div class="cart-item-title">${item.name}</div>
                <div>Price: R${item.price}</div>
                <div class="cart-quantity">
                    <button class="qty-btn" data-id="${item.id}" data-change="-1">-</button>
                    <span>${item.quantity}</span>
                    <button class="qty-btn" data-id="${item.id}" data-change="1">+</button>
                </div>
                <div class="cart-item-price">Subtotal: R${itemTotal}</div>
                <button class="remove-btn" data-id="${item.id}">Remove</button>
            </div>
        `;
    }
    
    cartItemsContainer.innerHTML = html;
    if (cartTotalElement) cartTotalElement.textContent = `Total: R${total}`;
    
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = parseInt(btn.dataset.id);
            const change = parseInt(btn.dataset.change);
            const item = cart.find(i => i.id === id);
            if (item) updateQuantity(id, item.quantity + change);
        });
    });
    
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = parseInt(btn.dataset.id);
            removeFromCart(id);
        });
    });
}

function updateCheckoutTotal() {
    const checkoutTotal = document.getElementById('checkoutTotal');
    if (checkoutTotal) checkoutTotal.textContent = `Total: R${getTotal()}`;
}

// ============================================
// PAGE DISPLAY FUNCTIONS
// ============================================
function showMainPage() {
    document.getElementById('mainPage').style.display = 'block';
    document.getElementById('productDetailsPage').style.display = 'none';
    document.getElementById('cartPage').style.display = 'none';
    document.getElementById('checkoutPage').style.display = 'none';
    renderProductGrid();
}

function showProductDetailsPage(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    document.getElementById('mainPage').style.display = 'none';
    document.getElementById('productDetailsPage').style.display = 'block';
    document.getElementById('cartPage').style.display = 'none';
    document.getElementById('checkoutPage').style.display = 'none';
    
    renderProductDetails(product);
}

function showCartPage() {
    document.getElementById('mainPage').style.display = 'none';
    document.getElementById('productDetailsPage').style.display = 'none';
    document.getElementById('cartPage').style.display = 'block';
    document.getElementById('checkoutPage').style.display = 'none';
    renderCartPage();
}

function showCheckoutPage() {
    if (cart.length === 0) {
        alert('Your cart is empty. Add items before checkout.');
        return;
    }
    document.getElementById('mainPage').style.display = 'none';
    document.getElementById('productDetailsPage').style.display = 'none';
    document.getElementById('cartPage').style.display = 'none';
    document.getElementById('checkoutPage').style.display = 'block';
    updateCheckoutTotal();
}

// ============================================
// RENDER FUNCTIONS
// ============================================
function renderProductGrid() {
    const grid = document.getElementById('productGrid');
    if (!grid) return;
    
    let html = '';
    for (let i = 0; i < products.length; i++) {
        const p = products[i];
        html += `
            <div class="product-card" data-id="${p.id}">
                <img class="product-image" src="${p.image}" alt="${p.name}" onerror="this.src='https://placehold.co/100x100?text=No+Image'">
                <div class="product-info">
                    <div class="product-title">${p.name}</div>
                    <div class="rating">★ ${p.rating} (${p.reviews} reviews)</div>
                    <div class="location">📍 ${p.location}</div>
                    <div class="verified">✅ Verified seller</div>
                    <div class="price">R${p.price}</div>
                    <button class="buy-now-btn view-details-btn" data-id="${p.id}">Buy Now</button>
                </div>
            </div>
        `;
    }
    grid.innerHTML = html;
    
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = parseInt(btn.dataset.id);
            showProductDetailsPage(id);
        });
    });
}

function renderProductDetails(product) {
    const container = document.getElementById('productDetailsContent');
    let quantity = 1;
    
    const html = `
        <div class="product-details-grid">
            <img class="product-details-image" src="${product.image}" alt="${product.name}" onerror="this.src='https://placehold.co/300x300?text=No+Image'">
            <div class="product-details-info">
                <h1>${product.name}</h1>
                <div class="rating">★ ${product.rating} (${product.reviews} reviews)</div>
                <div class="product-details-price">R${product.price}</div>
                
                <div class="quantity-section">
                    <span class="quantity-label">Quantity:</span>
                    <div class="quantity-selector">
                        <button class="quantity-btn" id="decrementQty">-</button>
                        <span class="quantity-value" id="qtyValue">1</span>
                        <button class="quantity-btn" id="incrementQty">+</button>
                    </div>
                </div>
                
                <div class="details-buttons">
                    <button class="details-cart-btn" id="addToCartDetails">Add to Cart</button>
                    <button class="details-buy-btn" id="buyNowDetails">Buy Now</button>
                </div>
                
                <div class="seller-card">
                    <strong>Seller: ${product.seller}</strong><br>
                    ✅ Verified seller<br>
                    ⭐ ${product.sellerRating} seller rating (${product.sellerSales} sales)
                </div>
                
                <div class="delivery-card">
                    <strong>📦 Delivery</strong><br>
                    ${product.deliveryInfo}
                </div>
                
                <div class="escrow-note">
                    🔒 Your payment will be held in escrow until you confirm delivery
                </div>
                
                <div>
                    <strong>Description:</strong><br>
                    ${product.description}
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    document.getElementById('decrementQty').addEventListener('click', () => {
        if (quantity > 1) {
            quantity--;
            document.getElementById('qtyValue').textContent = quantity;
        }
    });
    
    document.getElementById('incrementQty').addEventListener('click', () => {
        quantity++;
        document.getElementById('qtyValue').textContent = quantity;
    });
    
    document.getElementById('addToCartDetails').addEventListener('click', () => {
        addToCart(product.id, product.name, product.price, quantity);
        alert(`${quantity} x ${product.name} added to cart!`);
    });
    
    document.getElementById('buyNowDetails').addEventListener('click', () => {
        addToCart(product.id, product.name, product.price, quantity);
        showCartPage();
    });
}

// ============================================
// EVENT LISTENERS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    
    loadCart();
    renderProductGrid();
    
    // Navigation
    document.getElementById('logoHome').addEventListener('click', () => showMainPage());
    document.getElementById('navHome').addEventListener('click', () => showMainPage());
    document.getElementById('navCart').addEventListener('click', () => showCartPage());
    document.getElementById('cartIconBtn').addEventListener('click', (e) => { e.preventDefault(); showCartPage(); });
    document.getElementById('backToShopFromCartBtn').addEventListener('click', () => showMainPage());
    document.getElementById('backToHomeFromDetails').addEventListener('click', () => showMainPage());
    document.getElementById('checkoutFromCartBtn').addEventListener('click', () => showCheckoutPage());
    document.getElementById('backToCartFromCheckoutBtn').addEventListener('click', () => showCartPage());
    document.getElementById('navAdmin').addEventListener('click', (e) => { 
    e.preventDefault(); 
    window.location.href = 'admin-login.html'; 
});
    
    // "See all" links
    document.getElementById('seeAllCategories').addEventListener('click', () => {
        alert('All categories page coming soon. Browse through Clothing, Food, Electronics, Furniture, Beauty, and Other.');
    });
    document.getElementById('seeAllProducts').addEventListener('click', () => {
        alert('All products page coming soon. Check back for more great items from local sellers.');
    });
    
    // Scroll buttons
    document.getElementById('shopNowBtn').addEventListener('click', () => {
        document.querySelector('.product-grid').scrollIntoView({ behavior: 'smooth' });
    });
    
    document.getElementById('sellNowBtn').addEventListener('click', () => {
        alert('To sell on SafeHub, please register as a seller and complete identity verification.');
    });
    
    // Placeholder nav
    document.getElementById('navOrders').addEventListener('click', () => { alert('Order history will appear here once you complete a purchase.'); });
    document.getElementById('navProfile').addEventListener('click', () => { alert('Profile page coming soon. Your account settings will be available here.'); });
    
    // Confirm order
    document.getElementById('confirmOrderBtn').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Your cart is empty');
            return;
        }
        const name = document.getElementById('fullName').value;
        if (!name) {
            alert('Please enter your name');
            return;
        }
        alert(`Order confirmed!\nTotal: R${getTotal()}\nPickup at selected spaza shop.\nPayment held in escrow until you confirm delivery.`);
        cart = [];
        saveCart();
        renderCartPage();
        showMainPage();
        document.getElementById('fullName').value = '';
        document.getElementById('phone').value = '';
    });
    
    showMainPage();
});