const PRODUCT_IMAGES = {
    cleanser: 'assets/images/cleanser.svg',
    lipstick: 'assets/images/lipstick.svg',
    shampoo: 'assets/images/shampoo.svg',
    perfume: 'assets/images/perfume.svg',
    sunscreen: 'assets/images/sunscreen.svg',
    toner: 'assets/images/toner.svg',
    foundation: 'assets/images/foundation.svg',
    lotion: 'assets/images/lotion.svg'
};

const PRODUCTS = [
    { id: 1, name: 'Gentle Glow Cleanser', category: 'Skincare', price: 650, stock: 18, image: PRODUCT_IMAGES.cleanser, suitable_for: 'Normal to oily skin', description: 'A mild daily cleanser that removes dirt and excess oil without making the skin feel dry.' },
    { id: 2, name: 'Rose Matte Lipstick', category: 'Makeup', price: 450, stock: 24, image: PRODUCT_IMAGES.lipstick, suitable_for: 'All skin tones', description: 'A soft rose shade lipstick with a comfortable matte finish for everyday use.' },
    { id: 3, name: 'Herbal Repair Shampoo', category: 'Haircare', price: 720, stock: 14, image: PRODUCT_IMAGES.shampoo, suitable_for: 'Dry and damaged hair', description: 'A nourishing shampoo with herbal extracts for smoother and cleaner hair.' },
    { id: 4, name: 'Fresh Bloom Perfume', category: 'Perfume', price: 1250, stock: 10, image: PRODUCT_IMAGES.perfume, suitable_for: 'Daily fragrance', description: 'A light floral perfume suitable for regular use and small gatherings.' },
    { id: 5, name: 'SPF 50 Daily Sunscreen', category: 'Skincare', price: 890, stock: 16, image: PRODUCT_IMAGES.sunscreen, suitable_for: 'Outdoor use', description: 'A lightweight sunscreen that helps protect the skin from sun exposure.' },
    { id: 6, name: 'Hydrating Face Toner', category: 'Skincare', price: 560, stock: 20, image: PRODUCT_IMAGES.toner, suitable_for: 'Combination skin', description: 'A refreshing toner that helps prepare the skin before moisturizer.' },
    { id: 7, name: 'Soft Finish Foundation', category: 'Makeup', price: 980, stock: 12, image: PRODUCT_IMAGES.foundation, suitable_for: 'Medium coverage', description: 'A smooth foundation designed for a natural finish and comfortable wear.' },
    { id: 8, name: 'Aloe Body Lotion', category: 'Personal Care', price: 520, stock: 22, image: PRODUCT_IMAGES.lotion, suitable_for: 'Dry skin', description: 'A gentle body lotion for everyday moisturizing and soft skin feel.' }
];

function getCart() {
    return JSON.parse(localStorage.getItem('beautyShopCart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('beautyShopCart', JSON.stringify(cart));
    updateCartCount();
}

function addToCart(productId, quantity = 1) {
    const product = PRODUCTS.find(item => item.id === Number(productId));
    if (!product) return;
    const cart = getCart();
    const existing = cart.find(item => item.id === product.id);
    if (existing) {
        existing.quantity += Number(quantity);
    } else {
        cart.push({ id: product.id, quantity: Number(quantity) });
    }
    saveCart(cart);
    alert(product.name + ' added to cart.');
}

function updateCartItem(productId, quantity) {
    let cart = getCart();
    quantity = Number(quantity);
    if (quantity <= 0) {
        cart = cart.filter(item => item.id !== Number(productId));
    } else {
        const item = cart.find(entry => entry.id === Number(productId));
        if (item) item.quantity = quantity;
    }
    saveCart(cart);
    renderCartPage();
}

function removeFromCart(productId) {
    const cart = getCart().filter(item => item.id !== Number(productId));
    saveCart(cart);
    renderCartPage();
}

function clearCart() {
    saveCart([]);
}

function getCartDetails() {
    return getCart().map(item => {
        const product = PRODUCTS.find(product => product.id === item.id);
        if (!product) return null;
        return { ...product, quantity: item.quantity, subtotal: product.price * item.quantity };
    }).filter(Boolean);
}

function getCartTotal() {
    return getCartDetails().reduce((sum, item) => sum + item.subtotal, 0);
}

function updateCartCount() {
    const count = getCart().reduce((sum, item) => sum + item.quantity, 0);
    document.querySelectorAll('#cartCount').forEach(el => el.textContent = count);
}

function getOrders() {
    return JSON.parse(localStorage.getItem('beautyShopOrders') || '[]');
}

function saveOrders(orders) {
    localStorage.setItem('beautyShopOrders', JSON.stringify(orders));
}
