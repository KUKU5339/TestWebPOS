@extends('layout')

@section('content')
<style>
    .quick-sale-container {
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 260px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .product-card {
        background: #fff;
        border: 2px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .product-card:hover {
        border-color: #FFD700;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .product-card.out-of-stock {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .product-card.out-of-stock:hover {
        transform: none;
        border-color: #ddd;
    }

    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #f0f0f0;
    }

    .product-name {
        font-weight: bold;
        color: #800000;
        margin: 8px 0;
        font-size: 16px;
    }

    .product-price {
        color: #008000;
        font-size: 18px;
        font-weight: bold;
    }

    .product-stock {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .cart-container {
        position: fixed;
        bottom: 0;
        left: 220px;
        right: 0;
        background: #fff;
        border-top: 3px solid #800000;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
        transition: left 0.3s ease-in-out;
    }

    .cart-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 15px 20px;
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .cart-items {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .cart-item {
        background: #f8f8f8;
        padding: 10px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 250px;
        border: 2px solid #ddd;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-weight: bold;
        color: #800000;
        font-size: 14px;
    }

    .cart-item-price {
        color: #008000;
        font-size: 13px;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-btn {
        background: #800000;
        color: #fff;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        background: #a00000;
    }

    .qty-display {
        width: 45px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 3px 0;
        appearance: textfield;
        -moz-appearance: textfield;
    }

    .qty-display::-webkit-outer-spin-button,
    .qty-display::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .remove-btn {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    }

    .remove-btn:hover {
        background: #c82333;
    }

    .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 2px solid #eee;
    }

    .cart-total {
        font-size: 24px;
        font-weight: bold;
        color: #800000;
    }

    .cart-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-clear {
        background: #6c757d;
        color: #fff;
    }

    .btn-clear:hover {
        background: #5a6268;
    }

    .btn-checkout {
        background: #FFD700;
        color: #800000;
    }

    .btn-checkout:hover {
        background: #e6c200;
        transform: scale(1.05);
    }

    .empty-cart {
        text-align: center;
        color: #999;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .cart-container {
            left: 0;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .product-image {
            height: 100px;
        }

        .cart-items {
            flex-direction: column;
        }

        .cart-item {
            min-width: 100%;
        }
    }
</style>

<div class="quick-sale-container">
    <div class="page-header">
        <div>
            <h2 style="color:#800000; margin:0;">⚡ Quick Sale Mode</h2>
            <p style="color:#666; margin:5px 0 0 0;">Tap products to add to cart</p>
        </div>
    </div>

    <div class="products-grid" id="productsGrid">
        <!-- Skeleton product cards while loading -->
        @for($i = 0; $i < 8; $i++)
        <div class="product-card" style="pointer-events:none;">
            <div class="product-image skeleton" style="height:120px;"></div>
            <div class="skeleton skeleton-text" style="margin:8px auto; width:70%;"></div>
            <div class="skeleton skeleton-text" style="margin:4px auto; width:50%;"></div>
            <div class="skeleton skeleton-text" style="margin:4px auto; width:40%;"></div>
        </div>
        @endfor
    </div>
</div>

<!-- Shopping Cart -->
<div class="cart-container">
    <div class="cart-content">
        <div class="cart-header">
            <h3 style="margin:0; color:#800000;">🛒 Current Order</h3>
            <span id="cart-count" style="color:#666;">0 items</span>
        </div>

        <div id="cart-items" class="cart-items">
            <div class="empty-cart">Cart is empty. Tap products above to add items.</div>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                Total: <span id="cart-total">₱0.00</span>
            </div>
            <div class="cart-actions">
                <button class="btn btn-clear" onclick="clearCart()">Clear Cart</button>
                <button class="btn btn-checkout" onclick="checkout()" id="checkout-btn" disabled>Complete Sale</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    let allProducts = [];

    function adjustContentBottomPadding() {
        var container = document.querySelector('.quick-sale-container');
        var cartEl = document.querySelector('.cart-container');
        if (!container || !cartEl) return;
        var h = cartEl.offsetHeight || 240;
        container.style.paddingBottom = (h + 20) + 'px';
    }

    function renderProductGrid(products) {
        allProducts = products;
        var grid = document.getElementById('productsGrid');

        if (!products || products.length === 0) {
            grid.innerHTML = '<div style="text-align:center; padding:40px; background:#fff; border-radius:10px; grid-column:1/-1;">' +
                '<i class="fas fa-box-open" style="font-size:48px; color:#ccc;"></i>' +
                '<p style="color:#999; margin-top:15px;">No products available. <a href="/products">Add some products first</a></p></div>';
            return;
        }

        var html = '';
        products.forEach(function(p) {
            var outOfStock = p.stock <= 0;
            var imgHtml = p.image_url
                ? '<img src="' + p.image_url + '" alt="' + p.name + '" class="product-image" loading="lazy" onerror="this.onerror=null; this.src=\'/icon-192.png\';">'
                : '<div class="product-image" style="display:flex;align-items:center;justify-content:center;background:#f0f0f0;">' +
                  '<i class="fas fa-utensils" style="font-size:40px;color:#ccc;"></i></div>';
            var stockHtml = outOfStock
                ? '<span style="color:red;">Out of Stock</span>'
                : 'Stock: ' + p.stock;
            var escapedName = p.name.replace(/'/g, "\\'");
            var onclick = outOfStock ? '' : 'addToCart(' + p.id + ', \'' + escapedName + '\', ' + p.price + ', ' + p.stock + ')';

            html += '<div class="product-card ' + (outOfStock ? 'out-of-stock' : '') + '" data-product-id="' + p.id + '"' +
                (onclick ? ' onclick="' + onclick + '"' : '') + '>' +
                imgHtml +
                '<div class="product-name">' + p.name + '</div>' +
                '<div class="product-price">\u20B1' + parseFloat(p.price).toFixed(2) + '</div>' +
                '<div class="product-stock">' + stockHtml + '</div></div>';
        });
        grid.innerHTML = html;
        adjustContentBottomPadding();
    }

    // Load products: IndexedDB first (instant), then server refresh
    async function loadProducts() {
        // Step 1: Try IndexedDB for instant display
        try {
            if (typeof offlineDB !== 'undefined') {
                if (!offlineDB.db) await offlineDB.init();
                var cached = await offlineDB.getProducts();
                if (cached && cached.length > 0) {
                    renderProductGrid(cached);
                    console.log('Products loaded from IndexedDB:', cached.length);
                }
            }
        } catch (err) {
            console.warn('IndexedDB load failed:', err);
        }

        // Step 2: Fetch fresh data from server (if online)
        if (navigator.onLine) {
            try {
                var data = await lazyFetch('/api/products/in-stock');
                if (data && data.products) {
                    renderProductGrid(data.products);
                    // Update IndexedDB cache
                    if (typeof offlineDB !== 'undefined') {
                        if (!offlineDB.db) await offlineDB.init();
                        await offlineDB.saveProducts(data.products);
                    }
                    console.log('Products refreshed from server:', data.products.length);
                }
            } catch (err) {
                console.warn('Server product fetch failed:', err);
            }
        }
    }

    window.addEventListener('load', loadProducts);
    window.addEventListener('load', adjustContentBottomPadding);
    window.addEventListener('resize', adjustContentBottomPadding);

    // Update UI when going offline/online
    window.addEventListener('offline', () => {
        console.log('📴 Gone offline - using cached data');
        showToast('📴 You are offline. Sales will be saved locally.', 'warning');
    });

    window.addEventListener('online', () => {
        console.log('🌐 Back online');
        showToast('🌐 Back online!', 'success');
    });

    function addToCart(id, name, price, maxStock) {
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            if (existingItem.quantity < maxStock) {
                existingItem.quantity++;
            } else {
                alert('Maximum stock reached for ' + name);
                return;
            }
        } else {
            cart.push({
                id,
                name,
                price,
                quantity: 1,
                maxStock
            });
        }

        updateCart();
        adjustContentBottomPadding();
    }

    function updateQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (!item) return;

        item.quantity += change;

        if (item.quantity <= 0) {
            removeFromCart(id);
        } else if (item.quantity > item.maxStock) {
            item.quantity = item.maxStock;
            alert('Maximum stock reached');
        }

        updateCart();
    }

    function setQuantity(id, value) {
        const item = cart.find(item => item.id === id);
        if (!item) return;

        let qty = parseInt(value);
        if (isNaN(qty) || qty <= 0) {
            removeFromCart(id);
            return;
        }
        if (qty > item.maxStock) {
            qty = item.maxStock;
            alert('Maximum stock reached');
        }

        item.quantity = qty;
        updateCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
    }

    function updateCart() {
        const cartItemsDiv = document.getElementById('cart-items');
        const cartCount = document.getElementById('cart-count');
        const cartTotal = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('checkout-btn');

        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<div class="empty-cart">Cart is empty. Tap products above to add items.</div>';
            cartCount.textContent = '0 items';
            cartTotal.textContent = '₱0.00';
            checkoutBtn.disabled = true;
            return;
        }

        let html = '';
        let total = 0;
        let itemCount = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            itemCount += item.quantity;

            html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">₱${item.price.toFixed(2)} × ${item.quantity} = ₱${subtotal.toFixed(2)}</div>
                </div>
                <div class="quantity-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <input type="number" class="qty-display" value="${item.quantity}" min="1" max="${item.maxStock}" onchange="setQuantity(${item.id}, this.value)" onkeydown="if(event.key==='Enter'){this.blur();}">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
                <button class="remove-btn" onclick="removeFromCart(${item.id})">✕</button>
            </div>
        `;
        });

        cartItemsDiv.innerHTML = html;
        cartCount.textContent = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
        cartTotal.textContent = `₱${total.toFixed(2)}`;
        checkoutBtn.disabled = false;
    }

    function clearCart() {
        if (cart.length === 0) return;

        if (confirm('Clear all items from cart?')) {
            cart = [];
            updateCart();
        }
    }

    // Use the global getFreshCsrfToken from layout.blade.php
    // Falls back to meta tag token if global function not available
    async function getToken() {
        if (typeof window.getFreshCsrfToken === 'function') {
            const token = await window.getFreshCsrfToken();
            if (token) return token;
        }
        return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    }

    // Helper to ensure offlineDB is ready
    async function ensureOfflineDB() {
        if (typeof offlineDB === 'undefined') {
            return false;
        }
        if (!offlineDB.db) {
            try {
                await offlineDB.init();
            } catch (err) {
                console.error('Failed to init offlineDB:', err);
                return false;
            }
        }
        return true;
    }

    async function isOnline() {
        if (typeof window.isActuallyOnline === 'function') {
            try { return await window.isActuallyOnline(); } catch {}
        }
        if (!navigator.onLine) return false;
        if (window._networkBackoffUntil && Date.now() < window._networkBackoffUntil) return false;
        try {
            const res = await fetch('/api/csrf-token', {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store'
            });
            return res.ok || res.status === 401;
        } catch {
            const now = Date.now();
            const prev = window._networkBackoffUntil && now < window._networkBackoffUntil ? (window._networkBackoffUntil - now) : 0;
            const next = Math.min(5 * 60 * 1000, Math.max(15000, prev ? prev * 2 : 30000));
            window._networkBackoffUntil = now + next;
            return false;
        }
    }

    function updateProductStockUI(productId, newStock) {
        const card = document.querySelector(`.product-card[data-product-id="${productId}"]`);
        if (!card) return;

        // Update stock text
        const stockDiv = card.querySelector('.product-stock');
        if (stockDiv) {
            if (newStock <= 0) {
                stockDiv.innerHTML = '<span style="color:red;">Out of Stock</span>';
                card.classList.add('out-of-stock');
                card.setAttribute('onclick', '');
            } else {
                stockDiv.textContent = 'Stock: ' + newStock;
                // Update onclick with new maxStock
                const name = card.querySelector('.product-name').textContent;
                const price = parseFloat(card.querySelector('.product-price').textContent.replace('₱', '').replace(',', ''));
                card.setAttribute('onclick', `addToCart(${productId}, '${name.replace(/'/g, "\\'")}', ${price}, ${newStock})`);
            }
        }
    }

    async function checkout() {
        if (cart.length === 0) return;

        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const cartSnapshot = cart.map(i => ({ id: i.id, name: i.name, price: i.price, quantity: i.quantity }));
        const totalSnapshot = cartSnapshot.reduce((sum, i) => sum + (i.price * i.quantity), 0);

        // Instant optimistic receipt render
        try {
            openCartReceiptModal(cartSnapshot, totalSnapshot, null);
            showToast('⏳ Processing sale...', 'info');
        } catch (e) {
            // ignore UI errors
        }

        // Clear cart immediately to be ready for next sale
        cart = [];
        updateCart();

        // Try ONLINE submission first (optimistic)
        console.log('🌐 Submitting sale (optimistic)');
        const csrfToken = await getToken();

        fetch('{{ route("sales.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cart: cartSnapshot
                })
            })
            .then(response => response.json()
                .then(data => ({
                    ok: response.ok,
                    data,
                    status: response.status,
                    statusText: response.statusText
                }))
                .catch(() => ({
                    ok: response.ok,
                    data: {},
                    status: response.status,
                    statusText: response.statusText
                }))
            )
            .then(async result => {
                if (!result.ok) {
                    // Attempt offline save only if really offline
                    const onlineNow = await isOnline();
                    if (onlineNow) {
                        alert('❌ Error: ' + (result.data.message || result.statusText));
                        showToast('❌ Sale failed. Not saved offline.', 'error');
                        return;
                    }
                    // Offline fallback below in catch
                    throw new Error(result.data.message || result.statusText || 'Sale failed');
                }
                if (result.data && result.data.success) {
                    // Update stock display for each sold item
                    for (const item of cartSnapshot) {
                        const card = document.querySelector(`.product-card[data-product-id="${item.id}"]`);
                        if (card) {
                            const newStock = item.maxStock - item.quantity;
                            updateProductStockUI(item.id, newStock);
                        }
                    }

                    // Show receipt modal
                    showToast('✅ Sale completed successfully!', 'success');
                    const ids = (result.data && Array.isArray(result.data.sale_ids) && result.data.sale_ids.length > 0)
                        ? result.data.sale_ids
                        : (result.data && result.data.sale_id ? [result.data.sale_id] : null);
                    openCartReceiptModal(cartSnapshot, totalSnapshot, ids);
                } else {
                    alert('❌ Error: ' + ((result.data && result.data.message) || 'Failed to complete sale'));
                    showToast('❌ Sale failed. Please try again.', 'error');
                }
            })
            .catch(async error => {
                console.error('Sale error:', error);

                const onlineNow = await isOnline();
                if (onlineNow) {
                    alert('❌ Error: ' + (error && error.message ? error.message : 'Sale request failed'));
                    showToast('❌ Sale failed. Not saved offline.', 'error');
                    return;
                }

                console.log('⚠️ Fetch failed and offline detected - saving as pending sale');

                // Ensure offlineDB is available and initialized
                const dbReady = await ensureOfflineDB();
                if (!dbReady) {
                    alert('❌ Connection failed and offline mode is not available.\n\nPlease check your internet connection and try again.');
                    return;
                }

                try {
                    const saleData = {
                        cart: cartSnapshot,
                        timestamp: new Date().toISOString(),
                        total: total
                    };

                    await offlineDB.addPendingSale(saleData);

                    // Update local product stock in IndexedDB and DOM
                    for (const item of cartSnapshot) {
                        const products = await offlineDB.getProducts();
                        const product = products.find(p => p.id === item.id);
                        if (product) {
                            const newStock = product.stock - item.quantity;
                            await offlineDB.updateProductStock(item.id, newStock);
                            updateProductStockUI(item.id, newStock);
                        }
                    }

                    alert('✅ Sale saved offline!\n\nTotal: ₱' + total.toFixed(2) + '\n\nThis sale will sync when you reconnect.');

                    showToast('📦 Sale saved offline. Will sync when online.', 'success');
                } catch (err) {
                    console.error('Failed to save offline sale:', err);
                    alert('❌ Failed to save sale. Please check your connection and try again.');
                }
            });
    }

</script>
<script>
    function openCartReceiptModal(items, total, saleIds) {
        var modal = document.getElementById('receiptModal');
        var body = document.getElementById('receiptModalBody');
        var pdfLink = document.getElementById('receiptPdfLink');

        var receiptNumber = (Array.isArray(saleIds) && saleIds.length > 0) ? String(saleIds[0]).padStart(6, '0') : '—';
        var now = new Date();
        var dateStr = now.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        var timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        var cashier = (typeof window.currentUserName === 'string' && window.currentUserName) ? window.currentUserName : 'N/A';

        modal.classList.add('active');
        if (Array.isArray(saleIds) && saleIds.length > 1) {
            var query = encodeURIComponent(saleIds.join(','));
            pdfLink.href = '/sales/receipt/batch/pdf?sale_ids=' + query;
        } else if (Array.isArray(saleIds) && saleIds.length === 1) {
            pdfLink.href = '/sales/' + saleIds[0] + '/receipt/pdf';
        } else {
            pdfLink.href = '#';
        }

        var lines = items.map(function(i) {
            return '<div style=\"margin-bottom:10px; font-size:12px;\">' +
                '<div style=\"font-weight:bold; margin-bottom:3px;\">' + i.name + '</div>' +
                '<div style=\"display:flex; justify-content:space-between; color:#666; font-size:11px;\">' +
                    '<span>' + i.quantity + ' × ₱' + i.price.toFixed(2) + '</span>' +
                    '<span>₱' + (i.price * i.quantity).toFixed(2) + '</span>' +
                '</div>' +
            '</div>';
        }).join('');

        var html =
            '<div style="font-family: \'Courier New\', monospace; padding:20px;">' +
                '<div style=\"text-align:center; border-bottom:2px dashed #333; padding-bottom:15px; margin-bottom:15px;\">' +
                    '<div style=\"font-size:20px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;\">🍢 StreetPOS</div>' +
                    '<div style=\"font-size:11px; color:#666; margin-bottom:10px;\">Your Neighborhood Street Food</div>' +
                    '<div style=\"font-size:11px; color:#666; line-height:1.4;\">' +
                        '<div>Receipt #: ' + receiptNumber + '</div>' +
                        '<div>Date: ' + dateStr + '</div>' +
                        '<div>Time: ' + timeStr + '</div>' +
                        '<div>Cashier: ' + cashier + '</div>' +
                    '</div>' +
                '</div>' +
                '<div style=\"margin:15px 0;\">' +
                    '<div style=\"font-size:12px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:5px; margin-bottom:10px; text-transform:uppercase;\">Order Details</div>' +
                    lines +
                '</div>' +
                '<div style=\"border-top:2px dashed #333; padding-top:10px; margin-top:10px;\">' +
                    '<div style=\"display:flex; justify-content:space-between; margin-bottom:5px; font-size:12px;\">' +
                        '<span>Subtotal:</span>' +
                        '<span>₱' + total.toFixed(2) + '</span>' +
                    '</div>' +
                    '<div style=\"display:flex; justify-content:space-between; margin-bottom:5px; font-size:16px; font-weight:bold; border-top:1px solid #333; padding-top:8px; margin-top:8px;\">' +
                        '<span>TOTAL:</span>' +
                        '<span>₱' + total.toFixed(2) + '</span>' +
                    '</div>' +
                '</div>' +
            '</div>';

        body.innerHTML = html;
    }
</script>

@endsection
