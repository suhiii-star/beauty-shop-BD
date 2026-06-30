function money(amount) {
    return '৳' + Number(amount).toLocaleString('en-BD');
}

function toggleMenu() {
    const nav = document.getElementById('navMenu');
    if (nav) nav.classList.toggle('open');
}

function productCard(product) {
    const stockClass = product.stock > 0 ? 'in' : 'out';
    const stockText = product.stock > 0 ? 'In Stock' : 'Out of Stock';
    return `
        <article class="product-card">
            <a class="product-image-wrap" href="product.html?id=${product.id}">
                <img src="${product.image}" alt="${product.name}">
            </a>
            <div class="product-info">
                <span class="tag">${product.category}</span>
                <h3><a href="product.html?id=${product.id}">${product.name}</a></h3>
                <p class="muted">${product.suitable_for}</p>
                <div class="card-bottom">
                    <strong>${money(product.price)}</strong>
                    <span class="stock ${stockClass}">${stockText}</span>
                </div>
                <button class="btn btn-small" onclick="addToCart(${product.id}, 1)" ${product.stock <= 0 ? 'disabled' : ''}>Add to Cart</button>
            </div>
        </article>`;
}

function renderFeaturedProducts() {
    const box = document.getElementById('featuredProducts');
    if (!box) return;
    box.innerHTML = PRODUCTS.slice(0, 4).map(productCard).join('');
}

function renderProductsPage() {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    const params = new URLSearchParams(window.location.search);
    const categoryFromUrl = params.get('category') || '';
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const noProducts = document.getElementById('noProducts');

    if (categoryFromUrl && categoryFilter) categoryFilter.value = categoryFromUrl;

    function applyFilters() {
        const search = searchInput.value.trim().toLowerCase();
        const category = categoryFilter.value;
        const sort = sortFilter.value;
        let filtered = PRODUCTS.filter(product => {
            const matchesSearch = product.name.toLowerCase().includes(search) || product.description.toLowerCase().includes(search);
            const matchesCategory = !category || product.category === category;
            return matchesSearch && matchesCategory;
        });
        if (sort === 'low') filtered.sort((a, b) => a.price - b.price);
        if (sort === 'high') filtered.sort((a, b) => b.price - a.price);
        grid.innerHTML = filtered.map(productCard).join('');
        noProducts.style.display = filtered.length ? 'none' : 'block';
    }

    [searchInput, categoryFilter, sortFilter].forEach(el => el.addEventListener('input', applyFilters));
    document.getElementById('clearFilters').addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = '';
        sortFilter.value = 'default';
        applyFilters();
    });
    applyFilters();
}

function renderProductDetail() {
    const box = document.getElementById('productDetail');
    if (!box) return;
    const id = Number(new URLSearchParams(window.location.search).get('id')) || 1;
    const product = PRODUCTS.find(item => item.id === id);
    if (!product) {
        box.innerHTML = '<div class="empty-state">Product not found. <a class="link" href="products.html">Back to products</a></div>';
        return;
    }
    box.innerHTML = `
        <div class="product-detail">
            <div class="detail-image"><img src="${product.image}" alt="${product.name}"></div>
            <div class="detail-info">
                <span class="tag">${product.category}</span>
                <h1>${product.name}</h1>
                <div class="price-big">${money(product.price)}</div>
                <div class="detail-list">
                    <p><strong>Stock:</strong> ${product.stock} available</p>
                    <p><strong>Suitable For:</strong> ${product.suitable_for}</p>
                </div>
                <p>${product.description}</p>
                <div class="add-cart-box">
                    <label for="qty">Quantity</label>
                    <input id="qty" type="number" value="1" min="1" max="${product.stock}">
                    <button class="btn" onclick="addToCart(${product.id}, document.getElementById('qty').value)">Add to Cart</button>
                </div>
            </div>
        </div>`;
}

function renderCartPage() {
    const box = document.getElementById('cartPage');
    if (!box) return;
    const items = getCartDetails();
    if (!items.length) {
        box.innerHTML = '<div class="empty-state"><h2>Your cart is empty.</h2><p>Add products before checkout.</p><a class="btn" href="products.html">Shop Now</a></div>';
        return;
    }
    const rows = items.map(item => `
        <tr>
            <td><div class="product-mini"><img src="${item.image}" alt="${item.name}"><div><strong>${item.name}</strong><br><span class="muted">${item.category}</span></div></div></td>
            <td>${money(item.price)}</td>
            <td><input class="qty-input" type="number" min="1" value="${item.quantity}" onchange="updateCartItem(${item.id}, this.value)"></td>
            <td><strong>${money(item.subtotal)}</strong></td>
            <td><button class="link-button" onclick="removeFromCart(${item.id})">Remove</button></td>
        </tr>`).join('');
    box.innerHTML = `
        <div class="table-card">
            <table>
                <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
            <div class="cart-actions">
                <a class="btn btn-outline" href="products.html">Continue Shopping</a>
                <div><strong>Total: ${money(getCartTotal())}</strong> <a class="btn" href="checkout.html">Checkout</a></div>
            </div>
        </div>`;
}

function renderCheckoutSummary() {
    const box = document.getElementById('checkoutSummary');
    if (!box) return;
    const items = getCartDetails();
    if (!items.length) {
        box.innerHTML = '<h2>Order Summary</h2><p class="muted">Your cart is empty.</p><a class="btn" href="products.html">Shop Now</a>';
        const form = document.getElementById('checkoutForm');
        if (form) form.style.display = 'none';
        return;
    }
    box.innerHTML = `<h2>Order Summary</h2>` + items.map(item => `
        <div class="summary-row"><span>${item.name} × ${item.quantity}</span><strong>${money(item.subtotal)}</strong></div>
    `).join('') + `<hr><div class="summary-row total"><strong>Total</strong><strong>${money(getCartTotal())}</strong></div>`;
}

function handleCheckout() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const items = getCartDetails();
        if (!items.length) return alert('Your cart is empty.');
        const order = {
            id: 'BS-' + Date.now(),
            date: new Date().toLocaleString(),
            name: document.getElementById('customerName').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            email: document.getElementById('email').value.trim(),
            address: document.getElementById('address').value.trim(),
            total: getCartTotal(),
            status: 'Pending',
            items
        };
        const orders = getOrders();
        orders.unshift(order);
        saveOrders(orders);
        clearCart();
        window.location.href = 'order_success.html?order=' + encodeURIComponent(order.id);
    });
}

function renderOrderSuccess() {
    const el = document.getElementById('orderCode');
    if (!el) return;
    const code = new URLSearchParams(window.location.search).get('order');
    el.textContent = code ? 'Order ID: ' + code : '';
}

function handleDemoAuth() {
    const login = document.getElementById('loginForm');
    if (login) login.addEventListener('submit', e => {
        e.preventDefault();
        localStorage.setItem('beautyShopUser', document.getElementById('loginEmail').value);
        alert('Demo login successful.');
        window.location.href = 'products.html';
    });
    const register = document.getElementById('registerForm');
    if (register) register.addEventListener('submit', e => {
        e.preventDefault();
        localStorage.setItem('beautyShopUser', document.getElementById('regEmail').value);
        alert('Demo registration successful.');
        window.location.href = 'products.html';
    });
}

function renderAdminDemo() {
    const productCount = document.getElementById('adminProductCount');
    if (!productCount) return;
    const orders = getOrders();
    const cartCount = getCart().reduce((sum, item) => sum + item.quantity, 0);
    productCount.textContent = PRODUCTS.length;
    document.getElementById('adminOrderCount').textContent = orders.length;
    document.getElementById('adminCartCount').textContent = cartCount;
    document.getElementById('adminProducts').innerHTML = `
        <table><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th></tr></thead><tbody>
        ${PRODUCTS.map(p => `<tr><td><div class="product-mini"><img src="${p.image}" alt="${p.name}"><strong>${p.name}</strong></div></td><td>${p.category}</td><td>${money(p.price)}</td><td>${p.stock}</td></tr>`).join('')}
        </tbody></table>`;
    const orderBox = document.getElementById('adminOrders');
    if (!orders.length) {
        orderBox.innerHTML = '<div class="empty-state">No demo orders yet. Place an order from checkout first.</div>';
    } else {
        orderBox.innerHTML = `
            <table><thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th></tr></thead><tbody>
            ${orders.map(o => `<tr><td><strong>${o.id}</strong><br><span class="muted">${o.date}</span></td><td>${o.name}<br><span class="muted">${o.email}</span></td><td>${o.phone}</td><td>${money(o.total)}</td><td><span class="status-pill">${o.status}</span></td></tr>`).join('')}
            </tbody></table>`;
    }
    document.getElementById('clearOrdersBtn').addEventListener('click', () => {
        if (confirm('Clear all demo orders from this browser?')) {
            saveOrders([]);
            renderAdminDemo();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    renderFeaturedProducts();
    renderProductsPage();
    renderProductDetail();
    renderCartPage();
    renderCheckoutSummary();
    handleCheckout();
    renderOrderSuccess();
    handleDemoAuth();
    renderAdminDemo();
});
