@extends('layout')

@section('content')
<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
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

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .stat-card.sales::before {
        background: #4CAF50;
    }

    .stat-card.products::before {
        background: #2196F3;
    }

    .stat-card.items::before {
        background: #FF9800;
    }

    .stat-card.stock::before {
        background: #f44336;
    }

    .stat-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .stat-card.sales .stat-icon {
        color: #4CAF50;
    }

    .stat-card.products .stat-icon {
        color: #2196F3;
    }

    .stat-card.items .stat-icon {
        color: #FF9800;
    }

    .stat-card.stock .stat-icon {
        color: #f44336;
    }

    .stat-label {
        color: #666;
        font-size: 13px;
        text-transform: uppercase;
        margin: 0 0 8px 0;
        font-weight: 600;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        margin: 0;
    }

    .stat-card.sales .stat-value {
        color: #4CAF50;
    }

    .stat-card.products .stat-value {
        color: #2196F3;
    }

    .stat-card.items .stat-value {
        color: #FF9800;
    }

    .stat-card.stock .stat-value {
        color: #f44336;
    }

    /* Section Cards */
    .section-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #FFD700;
    }

    .section-header h3 {
        margin: 0;
        color: #800000;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .view-all-link {
        color: #800000;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: color 0.3s;
    }

    .view-all-link:hover {
        color: #a00000;
    }

    /* Modern Tables */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table thead tr {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .modern-table th {
        padding: 12px;
        text-align: left;
        color: #800000;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
    }

    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .amount {
        color: #4CAF50;
        font-weight: 600;
    }

    .date-time {
        color: #666;
        font-size: 13px;
    }

    .stock-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-low {
        background: #ffebee;
        color: #c62828;
    }

    .stock-medium {
        background: #fff3e0;
        color: #e65100;
    }

    .stock-good {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 25px;
    }

    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h2>📊 Dashboard</h2>
        <p>Overview of your business performance</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card sales">
            <div class="stat-icon">💰</div>
            <p class="stat-label">Total Sales</p>
            <p class="stat-value" id="stat-totalSales"><span class="skeleton skeleton-text-lg"></span></p>
        </div>

        <div class="stat-card products">
            <div class="stat-icon">📦</div>
            <p class="stat-label">Products</p>
            <p class="stat-value" id="stat-totalProducts"><span class="skeleton skeleton-text-lg"></span></p>
        </div>

        <div class="stat-card items">
            <div class="stat-icon">🛒</div>
            <p class="stat-label">Items Sold</p>
            <p class="stat-value" id="stat-totalItemsSold"><span class="skeleton skeleton-text-lg"></span></p>
        </div>

        <div class="stat-card stock">
            <div class="stat-icon">⚠️</div>
            <p class="stat-label">Low Stock</p>
            <p class="stat-value" id="stat-lowStockCount"><span class="skeleton skeleton-text-lg"></span></p>
        </div>
    </div>

    <!-- Recent Sales Section -->
    <div class="section-card">
        <div class="section-header">
            <h3>
                <i class="fas fa-receipt"></i>
                Recent Sales
            </h3>
            <a href="{{ route('sales.index') }}" class="view-all-link">View All →</a>
        </div>
        <div id="recentSalesContent">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td><div class="skeleton skeleton-text" style="width:70%"></div></td>
                        <td><div class="skeleton skeleton-text" style="width:30px"></div></td>
                        <td><div class="skeleton skeleton-text" style="width:80px"></div></td>
                        <td><div class="skeleton skeleton-text" style="width:120px"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid Layout for Low Stock and Best Sellers -->
    <div class="grid-2">
        <!-- Low Stock Section -->
        <div class="section-card">
            <div class="section-header">
                <h3>
                    <i class="fas fa-exclamation-triangle"></i>
                    Low Stock Items
                </h3>
                <a href="{{ route('products.index') }}" class="view-all-link">Manage →</a>
            </div>
            <div id="lowStockTableContent">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < 3; $i++)
                        <tr>
                            <td><div class="skeleton skeleton-text" style="width:70%"></div></td>
                            <td><div class="skeleton skeleton-text" style="width:60px"></div></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Best Sellers Section -->
        <div class="section-card">
            <div class="section-header">
                <h3>
                    <i class="fas fa-trophy"></i>
                    Top 5 Best Sellers
                </h3>
            </div>
            <div id="topProductsContent">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Product</th>
                            <th>Total Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < 5; $i++)
                        <tr>
                            <td><div class="skeleton skeleton-text" style="width:24px"></div></td>
                            <td><div class="skeleton skeleton-text" style="width:70%"></div></td>
                            <td><div class="skeleton skeleton-text" style="width:60px"></div></td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch all 5 data groups in parallel
        var statsP = lazyFetch('/api/dashboard/stats');
        var recentP = lazyFetch('/api/dashboard/recent-sales');
        var lowStockP = lazyFetch('/api/dashboard/low-stock');
        var topP = lazyFetch('/api/dashboard/top-products');
        var todayP = lazyFetch('/api/dashboard/today');

        // Stats cards
        statsP.then(function(data) {
            if (!data) return;
            document.getElementById('stat-totalSales').textContent =
                '\u20B1' + parseFloat(data.totalSales).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('stat-totalProducts').textContent = data.totalProducts;
            document.getElementById('stat-totalItemsSold').textContent = data.totalItemsSold;
        }).catch(function(err) {
            console.error('Stats load error:', err);
            ['stat-totalSales','stat-totalProducts','stat-totalItemsSold'].forEach(function(id) {
                document.getElementById(id).textContent = '--';
            });
        });

        // Low stock count + table
        lowStockP.then(function(data) {
            if (!data) return;
            document.getElementById('stat-lowStockCount').textContent = data.count;

            var container = document.getElementById('lowStockTableContent');
            if (data.count > 0) {
                var html = '<table class="modern-table"><thead><tr><th>Product</th><th>Stock Level</th></tr></thead><tbody>';
                data.products.forEach(function(p) {
                    html += '<tr><td class="product-name">' + p.name + '</td>' +
                        '<td><span class="stock-badge stock-low">' + p.stock + ' left</span></td></tr>';
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty-state">' +
                    '<i class="fas fa-check-circle"></i>' +
                    '<p>All products are well stocked! 🎉</p></div>';
            }
        }).catch(function(err) {
            console.error('Low stock load error:', err);
            document.getElementById('stat-lowStockCount').textContent = '--';
        });

        // Recent sales table
        recentP.then(function(data) {
            if (!data) return;
            var container = document.getElementById('recentSalesContent');
            if (data.sales.length > 0) {
                var html = '<table class="modern-table"><thead><tr><th>Product</th><th>Quantity</th><th>Total</th><th>Date</th></tr></thead><tbody>';
                data.sales.forEach(function(s) {
                    html += '<tr><td class="product-name">' + s.product_name + '</td>' +
                        '<td>' + s.quantity + '</td>' +
                        '<td class="amount">\u20B1' + s.total + '</td>' +
                        '<td class="date-time">' + s.date + '</td></tr>';
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty-state">' +
                    '<i class="fas fa-receipt"></i>' +
                    '<p>No sales recorded yet</p></div>';
            }
        }).catch(function(err) {
            console.error('Recent sales load error:', err);
        });

        // Top products table
        topP.then(function(data) {
            if (!data) return;
            var container = document.getElementById('topProductsContent');
            if (data.products.length > 0) {
                var medals = ['🥇','🥈','🥉'];
                var html = '<table class="modern-table"><thead><tr><th>Rank</th><th>Product</th><th>Total Sold</th></tr></thead><tbody>';
                data.products.forEach(function(p, i) {
                    var rank = i < 3 ? medals[i] : (i + 1);
                    html += '<tr><td>' + rank + '</td><td class="product-name">' + p.name + '</td>' +
                        '<td><strong>' + p.total_sold + '</strong> items</td></tr>';
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty-state">' +
                    '<i class="fas fa-chart-line"></i>' +
                    '<p>No sales data available yet</p></div>';
            }
        }).catch(function(err) {
            console.error('Top products load error:', err);
        });

        // Daily summary notification (uses today stats)
        todayP.then(function(data) {
            if (!data) return;
            var lastNotification = localStorage.getItem('lastDailySummary');
            var today = new Date().toDateString();
            if (lastNotification !== today) {
                var hour = new Date().getHours();
                if (hour >= 18 && data.todaySales > 0 && typeof showNotification === 'function') {
                    setTimeout(function() {
                        showNotification('📊 Daily Sales Summary', {
                            body: 'Today: ' + data.todaySales + ' sales totaling \u20B1' + parseFloat(data.todayRevenue).toFixed(2),
                            tag: 'daily-summary',
                            requireInteraction: true
                        });
                        localStorage.setItem('lastDailySummary', today);
                    }, 2000);
                }
            }
        }).catch(function(err) {
            console.error('Today stats load error:', err);
        });
    });
</script>
@endpush
