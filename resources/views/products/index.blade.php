@extends('layout')

@section('content')
<style>
    .products-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h2 {
        color: #800000;
        margin: 0 0 5px 0;
        font-size: 28px;
    }

    .page-header p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #800000;
        color: #fff;
    }

    .btn-primary:hover {
        background: #a00000;
    }

    .btn-add {
        background: #FFD700;
        color: #800000;
    }

    .btn-add:hover {
        background: #e6c200;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .product-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #f5f5f5;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
        color: #999;
        font-size: 48px;
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.95);
    }

    .stock-low {
        color: #c62828;
        border: 2px solid #c62828;
    }

    .stock-medium {
        color: #e65100;
        border: 2px solid #e65100;
    }

    .stock-good {
        color: #2e7d32;
        border: 2px solid #2e7d32;
    }

    .product-info {
        padding: 20px;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0 0 10px 0;
    }

    .product-price {
        font-size: 22px;
        font-weight: bold;
        color: #4CAF50;
        margin-bottom: 15px;
    }

    .product-actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit {
        flex: 1;
        background: #FFD700;
        color: #800000;
        padding: 10px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #e6c200;
    }

    .btn-delete {
        background: #dc3545;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    .empty-state {
        background: #fff;
        padding: 60px 20px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
</style>

<div class="products-container">
    <div class="page-header">
        <h2>📦 Products Management</h2>
        <p>Control your menu, stock, and pricing</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error">
        <strong>Validation Errors:</strong>
        <ul style="margin: 5px 0 0 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    @endif

    <div class="toolbar">
        <form method="GET" action="{{ route('products.index') }}" class="search-box" id="searchForm">
            <input type="text" name="search" placeholder="🔍 Search products..."
                value="{{ request('search') }}" class="search-input" id="searchInput">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <button onclick="openAddSidebar()" class="btn btn-add">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>

    <div id="productsContent">
        <div class="products-grid">
            @for($i = 0; $i < 6; $i++)
            <div class="product-card">
                <div class="product-image-container skeleton" style="height:200px;"></div>
                <div class="product-info">
                    <div class="skeleton skeleton-text" style="width:70%; height:18px; margin-bottom:10px;"></div>
                    <div class="skeleton skeleton-text" style="width:40%; height:22px; margin-bottom:15px;"></div>
                    <div style="display:flex; gap:8px;">
                        <div class="skeleton" style="flex:1; height:36px; border-radius:6px;"></div>
                        <div class="skeleton" style="width:50px; height:36px; border-radius:6px;"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <div id="paginationContent" style="margin-top: 25px; display: flex; justify-content: center;"></div>
</div>

<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()"></div>

<!-- Add Product Dialog (Centered) -->
<div id="addSidebar" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); width:95%; max-width:520px; max-height:90vh; overflow-y:auto; background:#fff; border:4px solid #800000; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.25); padding:20px; display:none; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">➕ Add Product</h3>
    <form id="addProductForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:12px;">
            <label><b>Street Food Item:</b></label>
            <select name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; background:#fff;">
                <option value="">-- Select Street Food --</option>
                <optgroup label="Grilled/BBQ">
                    <option value="Isaw (Chicken Intestine)">Isaw (Chicken Intestine)</option>
                    <option value="Betamax (Blood Cake)">Betamax (Blood Cake)</option>
                    <option value="Chicken Barbecue">Chicken Barbecue</option>
                    <option value="Pork Barbecue">Pork Barbecue</option>
                    <option value="Adidas (Chicken Feet)">Adidas (Chicken Feet)</option>
                    <option value="Helmet (Chicken Head)">Helmet (Chicken Head)</option>
                    <option value="IUD (Chicken Butt)">IUD (Chicken Butt)</option>
                </optgroup>
                <optgroup label="Fried">
                    <option value="Kwek-Kwek (Orange Quail Eggs)">Kwek-Kwek (Orange Quail Eggs)</option>
                    <option value="Tokneneng (Orange Chicken Eggs)">Tokneneng (Orange Chicken Eggs)</option>
                    <option value="Fish Ball">Fish Ball</option>
                    <option value="Squid Ball">Squid Ball</option>
                    <option value="Kikiam">Kikiam</option>
                    <option value="Chicken Skin">Chicken Skin</option>
                    <option value="Banana Cue">Banana Cue</option>
                    <option value="Camote Cue">Camote Cue</option>
                    <option value="Turon">Turon</option>
                </optgroup>
                <optgroup label="Noodles & Rice">
                    <option value="Pancit Canton">Pancit Canton</option>
                    <option value="Palabok">Palabok</option>
                    <option value="Siomai">Siomai</option>
                    <option value="Lugaw">Lugaw</option>
                    <option value="Goto">Goto</option>
                </optgroup>
                <optgroup label="Snacks">
                    <option value="Taho">Taho</option>
                    <option value="Dirty Ice Cream">Dirty Ice Cream</option>
                    <option value="Mais (Corn)">Mais (Corn)</option>
                    <option value="Balut">Balut</option>
                    <option value="Penoy">Penoy</option>
                </optgroup>
                <optgroup label="Drinks">
                    <option value="Sago't Gulaman">Sago't Gulaman</option>
                    <option value="Buko Juice">Buko Juice</option>
                    <option value="Melon Juice">Melon Juice</option>
                </optgroup>
                <optgroup label="Other">
                    <option value="Custom Item">Custom Item (Type your own)</option>
                </optgroup>
            </select>
        </div>
        <div id="customNameField" style="margin-bottom:12px; display:none;">
            <label><b>Custom Item Name:</b></label>
            <input type="text" id="customNameInput" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Price:</b></label>
            <input type="number" step="0.01" name="price" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Stock:</b></label>
            <input type="number" name="stock" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Image:</b></label>
            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#800000; color:#fff; border:none; border-radius:5px; cursor:pointer;">Save</button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Cancel</button>
    </form>
</div>

<!-- Edit Product Dialog (Centered) -->
<div id="editSidebar" style="position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); width:95%; max-width:520px; max-height:90vh; overflow-y:auto; background:#fff; border:4px solid #FFD700; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.25); padding:20px; display:none; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">✏ Edit Product</h3>
    <form id="editForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div style="margin-bottom:12px;">
            <label><b>Name:</b></label>
            <input type="text" id="editName" name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Price:</b></label>
            <input type="number" step="0.01" id="editPrice" name="price" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Stock:</b></label>
            <input type="number" id="editStock" name="stock" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Current Image:</b></label><br>
            <img id="editImagePreview" src="" alt="No image" style="width:100px; height:100px; object-fit:cover; margin-bottom:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Change Image:</b></label>
            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#FFD700; color:#800000; border:none; border-radius:5px; cursor:pointer;">Update</button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Cancel</button>
    </form>
</div>

<script>
    function openAddSidebar() {
        document.getElementById("addSidebar").style.display = "block";
        document.getElementById("overlay").style.display = "block";
    }

    function openEditSidebar(id, name, price, stock, imageUrl) {
        document.getElementById("editSidebar").style.display = "block";
        document.getElementById("overlay").style.display = "block";

        document.getElementById("editName").value = name;
        document.getElementById("editPrice").value = price;
        document.getElementById("editStock").value = stock;
        document.getElementById("editForm").action = "/products/" + id;

        var preview = document.getElementById("editImagePreview");
        if (imageUrl) {
            preview.src = imageUrl;
        } else {
            preview.src = "";
            preview.alt = "No image";
        }
    }

    function closeSidebars() {
        document.getElementById("addSidebar").style.display = "none";
        document.getElementById("editSidebar").style.display = "none";
        document.getElementById("overlay").style.display = "none";
    }

    // Render product grid from JSON data
    function renderProducts(data) {
        var container = document.getElementById('productsContent');
        var search = new URLSearchParams(window.location.search).get('search') || '';

        if (!data.products || data.products.length === 0) {
            var msg = search ? 'No results for "' + search + '"' : 'Start by adding your first product';
            container.innerHTML = '<div class="empty-state"><i class="fas fa-box-open"></i>' +
                '<h3>No Products Found</h3><p>' + msg + '</p></div>';
            document.getElementById('paginationContent').innerHTML = '';
            return;
        }

        var html = '<div class="products-grid">';
        data.products.forEach(function(p) {
            var stockClass = p.stock <= 5 ? 'stock-low' : (p.stock <= 15 ? 'stock-medium' : 'stock-good');
            var imgHtml = p.image_url
                ? '<img src="' + p.image_url + '" alt="' + p.name + '" class="product-image" loading="lazy" onerror="this.parentElement.innerHTML=\'<div class=product-image-placeholder><i class=fas\\ fa-utensils></i></div>\'">'
                : '<div class="product-image-placeholder"><i class="fas fa-utensils"></i></div>';
            var escapedName = p.name.replace(/\\/g, '\\\\').replace(/'/g, "\\'");

            html += '<div class="product-card">' +
                '<div class="product-image-container">' + imgHtml +
                '<span class="stock-badge ' + stockClass + '">' + p.stock + ' in stock</span></div>' +
                '<div class="product-info">' +
                '<h3 class="product-name">' + p.name + '</h3>' +
                '<div class="product-price">\u20B1' + parseFloat(p.price).toFixed(2) + '</div>' +
                '<div class="product-actions">' +
                '<button onclick="openEditSidebar(' + p.id + ', \'' + escapedName + '\', ' + p.price + ', ' + p.stock + ', \'' + (p.image_url || '') + '\')" class="btn-edit"><i class="fas fa-edit"></i> Edit</button>' +
                '<form action="/products/' + p.id + '" method="POST" style="display:inline;">' +
                '<input type="hidden" name="_token" value="' + document.querySelector('meta[name=csrf-token]').content + '">' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<button type="submit" class="btn-delete" onclick="return confirm(\'Delete ' + escapedName + '?\')"><i class="fas fa-trash"></i></button>' +
                '</form></div></div></div>';
        });
        html += '</div>';
        container.innerHTML = html;

        // Render pagination
        var pag = data.pagination;
        var pagHtml = '';
        if (pag.has_pages) {
            var params = new URLSearchParams(window.location.search);
            for (var i = 1; i <= pag.last_page; i++) {
                params.set('page', i);
                var active = i === pag.current_page ? 'background:#800000; color:#fff;' : 'background:#fff; color:#800000;';
                pagHtml += '<a href="?' + params.toString() + '" style="' + active + ' padding:8px 14px; border:1px solid #800000; border-radius:5px; text-decoration:none; font-weight:600; margin:0 3px;">' + i + '</a>';
            }
        }
        document.getElementById('paginationContent').innerHTML = pagHtml;
    }

    // Load products from API
    function loadProducts() {
        var params = window.location.search || '';
        lazyFetch('/api/products' + params).then(function(data) {
            if (data) renderProducts(data);
        }).catch(function(err) {
            console.error('Products load error:', err);
            document.getElementById('productsContent').innerHTML =
                '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Failed to load products</h3><p>Please refresh the page</p></div>';
        });
    }

    // Intercept search form to use API
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var search = document.getElementById('searchInput').value;
        var params = new URLSearchParams();
        if (search) params.set('search', search);
        window.history.pushState({}, '', '?' + params.toString());
        loadProducts();
    });

    // Load on page ready
    document.addEventListener('DOMContentLoaded', loadProducts);

    // Show banner for pending products
    function showPendingProductsBanner(count) {
        var existingBanner = document.getElementById('pendingProductsBanner');
        if (existingBanner) existingBanner.remove();

        var banner = document.createElement('div');
        banner.id = 'pendingProductsBanner';
        banner.style.cssText = 'position:fixed; top:70px; left:50%; transform:translateX(-50%); background:linear-gradient(135deg, #ff9800, #f57c00); color:white; padding:15px 30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.3); z-index:9998; font-weight:600; animation:slideDown 0.3s; cursor:pointer; text-align:center; max-width:90%;';
        banner.innerHTML = '<i class="fas fa-clock"></i> ' + count + ' product' + (count > 1 ? 's' : '') + ' pending sync<br><small style="font-size:12px; font-weight:normal;">Will sync when you\'re back online</small>';
        banner.onclick = function() {
            alert('You have ' + count + ' product(s) waiting to sync.\n\nThey will automatically sync when you connect to the internet.');
        };
        document.body.appendChild(banner);
    }

    // Check for pending products on page load
    function checkPendingProducts() {
        try {
            var pendingProducts = JSON.parse(localStorage.getItem('pendingProducts') || '[]');
            if (pendingProducts.length > 0 && !navigator.onLine) {
                showPendingProductsBanner(pendingProducts.length);
            }
        } catch (err) {
            console.error('Failed to check pending products:', err);
        }
    }
    checkPendingProducts();

    // Handle custom item selection
    var nameSelect = document.querySelector('select[name="name"]');
    var customNameField = document.getElementById('customNameField');
    var customNameInput = document.getElementById('customNameInput');

    if (nameSelect) {
        nameSelect.addEventListener('change', function() {
            if (this.value === 'Custom Item') {
                customNameField.style.display = 'block';
                customNameInput.required = true;
            } else {
                customNameField.style.display = 'none';
                customNameInput.required = false;
            }
        });
    }

    // Handle offline product creation
    function initializeFormHandlers() {
        var addForm = document.getElementById('addProductForm');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var formData = new FormData(this);
                var name = formData.get('name');
                var price = parseFloat(formData.get('price'));
                var stock = parseInt(formData.get('stock'));

                if (name === 'Custom Item') {
                    var customName = document.getElementById('customNameInput').value.trim();
                    if (!customName) { alert('Please enter a custom item name'); return false; }
                    name = customName;
                    var hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden'; hiddenInput.name = 'name'; hiddenInput.value = name;
                    this.appendChild(hiddenInput);
                    this.querySelector('select[name="name"]').removeAttribute('name');
                }

                if (!name || !price || !stock || isNaN(price) || isNaN(stock)) {
                    alert('Please fill all required fields correctly');
                    return false;
                }

                if (!navigator.onLine) {
                    try {
                        var pendingProducts = JSON.parse(localStorage.getItem('pendingProducts') || '[]');
                        pendingProducts.push({ name: name, price: price, stock: stock, timestamp: new Date().toISOString() });
                        localStorage.setItem('pendingProducts', JSON.stringify(pendingProducts));
                        alert('Product saved offline!\n\nProduct: ' + name + '\nPrice: \u20B1' + price.toFixed(2) + '\nStock: ' + stock);
                        closeSidebars();
                        this.reset();
                        showPendingProductsBanner(pendingProducts.length);
                    } catch (err) {
                        alert('Failed to save product offline.');
                    }
                    return false;
                }

                HTMLFormElement.prototype.submit.call(this);
            });
        }

        var editForm = document.querySelector('#editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                if (!navigator.onLine) {
                    e.preventDefault();
                    alert('Cannot edit products while offline.\n\nPlease connect to the internet and try again.');
                    return false;
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeFormHandlers);
    } else {
        initializeFormHandlers();
    }
</script>

@endsection
