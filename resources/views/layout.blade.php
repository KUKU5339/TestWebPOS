<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'StreetPOS') }}</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFD700">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="StreetPOS">

    <!-- Defer Font Awesome loading (non-blocking) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <!-- Defer Offline Database loading -->
    <script src="/offline-db.js" defer></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
        }

        /* Top Header */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(135deg, #800000, #a00000);
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .hamburger {
            font-size: 24px;
            cursor: pointer;
            display: none;
            color: #FFD700;
            transition: transform 0.3s;
        }

        .hamburger:hover {
            transform: scale(1.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #FFD700;
        }

        .logo i {
            font-size: 28px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #FFD700;
            font-weight: 600;
        }

        .user-name i {
            font-size: 18px;
        }

        .icon-btn {
            background: none;
            border: none;
            color: #FFD700;
            font-size: 20px;
            cursor: pointer;
            position: relative;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .icon-btn:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #dc3545;
            color: #fff;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            min-width: 18px;
            text-align: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Dropdown */
        .dropdown-box {
            display: none;
            position: absolute;
            top: 55px;
            right: 0;
            background: #fff;
            color: #333;
            min-width: 280px;
            max-width: 320px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 2000;
            animation: slideDown 0.3s;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-box.active {
            display: block;
        }

        .dropdown-header {
            padding: 15px;
            background: #800000;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-content {
            max-height: 300px;
            overflow-y: auto;
        }

        .dropdown-content::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dropdown-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
            transition: background 0.2s;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item.critical {
            background: #fff5f5;
            border-left: 3px solid #dc3545;
        }

        .notification-item.warning {
            background: #fffbf0;
            border-left: 3px solid #ffc107;
        }

        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #999;
        }

        .notification-empty i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 10px;
        }

        .dropdown-footer {
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
        }

        .dropdown-footer a {
            color: #800000;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: block;
            text-align: center;
        }

        .dropdown-footer a:hover {
            color: #a00000;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 999;
        }

        .sidebar a {
            padding: 14px 20px;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover {
            background: #f8f9fa;
            border-left-color: #FFD700;
            color: #800000;
        }

        .sidebar a.active {
            background: #fff3cd;
            border-left-color: #800000;
            color: #800000;
            font-weight: 600;
        }

        .sidebar a i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .sidebar .logout {
            margin-top: auto;
            border-top: 1px solid #eee;
            color: #dc3545;
        }

        .sidebar .logout:hover {
            background: #fff5f5;
            border-left-color: #dc3545;
        }

        /* Content */
        .content {
            margin-left: 240px;
            margin-top: 60px;
            padding: 30px;
            min-height: calc(100vh - 60px);
            transition: margin-left 0.3s ease;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .hamburger {
                display: block;
            }

            .content {
                margin-left: 0;
                padding: 20px 15px;
            }

            .user-name span {
                display: none;
            }

            .header-right {
                gap: 10px;
            }

            .logo span {
                display: none;
            }

            .dropdown-box {
                right: -10px;
                min-width: 260px;
            }
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }

        .sidebar-overlay.active {
            display: block;
        }

        /* Receipt Modal */
        .receipt-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5000;
            justify-content: center;
            align-items: center;
        }

        .receipt-modal.active {
            display: flex;
        }

        .receipt-modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        .receipt-modal-dialog {
            position: relative;
            background: #fff;
            border-radius: 12px;
            width: 90%;
            max-width: 380px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s;
            transition: max-width 0.3s, max-height 0.3s;
        }

        .receipt-modal-dialog.expanded {
            max-width: 95vw;
            max-height: 95vh;
            border-radius: 8px;
        }

        .receipt-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: linear-gradient(135deg, #800000, #a00000);
            color: #FFD700;
            border-radius: 12px 12px 0 0;
            flex-shrink: 0;
        }

        .receipt-modal-dialog.expanded .receipt-modal-header {
            border-radius: 8px 8px 0 0;
        }

        .receipt-modal-title {
            font-weight: 600;
            font-size: 16px;
        }

        .receipt-modal-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .receipt-modal-btn {
            background: rgba(255, 215, 0, 0.15);
            border: none;
            color: #FFD700;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: background 0.2s;
            text-decoration: none;
        }

        .receipt-modal-btn:hover {
            background: rgba(255, 215, 0, 0.3);
        }

        .receipt-modal-close:hover {
            background: rgba(220, 53, 69, 0.6);
            color: #fff;
        }

        .receipt-modal-body {
            overflow-y: auto;
            padding: 0;
            flex: 1;
        }

        .receipt-modal-loading {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        @media (max-width: 768px) {
            .receipt-modal-dialog {
                width: 95%;
                max-height: 85vh;
            }
        }

        @media print {
            body > *:not(.receipt-modal) {
                display: none !important;
            }
            .receipt-modal {
                position: static;
                display: block !important;
            }
            .receipt-modal-backdrop {
                display: none;
            }
            .receipt-modal-dialog {
                box-shadow: none;
                max-width: 100%;
                max-height: none;
                border-radius: 0;
            }
            .receipt-modal-header {
                display: none;
            }
            .receipt-modal-body {
                overflow: visible;
            }
        }

        /* Skeleton loading animation */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s ease-in-out infinite;
            border-radius: 4px;
            display: inline-block;
        }
        @keyframes skeleton-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .skeleton-text { height: 14px; width: 80%; }
        .skeleton-text-lg { height: 28px; width: 120px; }
    </style>
</head>

<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <i class="hamburger fas fa-bars" onclick="toggleSidebar()"></i>
            <div class="logo">
                <i class="fas fa-store"></i>
                <span>StreetPOS</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-name">
                <i class="fa fa-user-circle"></i>
                <span>{{ Auth::user()->name ?? 'Guest' }}</span>
            </div>

            <!-- Notifications (loaded async) -->
                <div class="notification-wrapper" style="position: relative;">
                    <button class="icon-btn" title="Low Stock Notifications" onclick="toggleNotifications()">
                        <i class="fa fa-bell"></i>
                        <span class="notification-badge" id="lowStockBadge" style="display:none;"></span>
                    </button>

                    <div class="dropdown-box" id="notificationDropdown">
                        <div class="dropdown-header">
                            <i class="fas fa-bell"></i>
                            Low Stock Alerts
                        </div>
                        <div class="dropdown-content" id="lowStockContent">
                            <div class="skeleton-row" style="padding:12px;"><div class="skeleton skeleton-text"></div></div>
                            <div class="skeleton-row" style="padding:12px;"><div class="skeleton skeleton-text"></div></div>
                            <div class="skeleton-row" style="padding:12px;"><div class="skeleton skeleton-text"></div></div>
                        </div>
                        <div class="dropdown-footer" id="lowStockFooter" style="display:none;">
                            <a href="{{ route('stock-alerts.index') }}">View All Alerts &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="icon-btn" title="Settings">
                    <i class="fa fa-cog"></i>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="icon-btn" title="Logout">
                        <i class="fa fa-sign-out-alt"></i>
                    </button>
                </form>
        </div>
    </header>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="{{ route('sales.quick') }}" class="{{ request()->routeIs('sales.quick') ? 'active' : '' }}">
            <i class="fas fa-bolt"></i> Quick Sale
        </a>
        <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Sales History
        </a>
        <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="fas fa-calculator"></i> Profit Calculator
        </a>
        <a href="{{ route('stock-alerts.index') }}" class="{{ request()->routeIs('stock-alerts.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Stock Alerts
        </a>
        <a href="{{ route('reports.daily-sales') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Sales Report
        </a>
    </nav>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <script>
        (function(){
            var debug = localStorage.getItem('streetpos_debug') === 'true';
            if (!debug) {
                try {
                    var _ce = console.error, _cw = console.warn;
                    console.error = function(){};
                    console.warn = function(){};
                } catch (e) {}
            }
        })();
        window.currentUserName = "{{ auth()->user()->name ?? 'N/A' }}";
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function toggleNotifications() {
            const notifDropdown = document.getElementById('notificationDropdown');
            notifDropdown.classList.toggle('active');
        }

        // Close dropdowns when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.notification-wrapper')) {
                document.getElementById('notificationDropdown').classList.remove('active');
            }
        }

        // Close sidebar when clicking on a link (mobile)
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    toggleSidebar();
                });
            });
        }

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('✅ ServiceWorker registered:', registration.scope);
                    })
                    .catch(err => {
                        console.log('❌ ServiceWorker registration failed:', err);
                    });
            });
        }

        // Global lazy-load fetch helper (quiet when offline)
        window.lazyFetch = async function(url) {
            try {
                // Fast offline short-circuit
                if (!navigator.onLine) return null;

                // Respect recent connectivity check to avoid noisy logs
                var now = Date.now();
                if (window._lastOnlineCheckTime && window._lastOnlineCheckResult === false && (now - window._lastOnlineCheckTime) < 8000) {
                    return null;
                }

                // Throttled connectivity verification (only if last check is stale)
                if (!window._lastOnlineCheckTime || (now - window._lastOnlineCheckTime) >= 5000) {
                    try {
                        var ok = await isActuallyOnline();
                        if (!ok) return null;
                    } catch (e) {
                        // If connectivity check fails, proceed optimistically
                    }
                }

                // Fetch with timeout and silent abort
                var controller = new AbortController();
                var timer = setTimeout(function(){ try{ controller.abort(); }catch(e){} }, 3500);
                var response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' },
                    signal: controller.signal
                });
                clearTimeout(timer);

                if (response.status === 401) {
                    showToast('Session expired. Please log in again.', 'error');
                    setTimeout(function() { window.location.href = '/login'; }, 2000);
                    return null;
                }
                if (!response.ok) return null;
                return response.json();
            } catch (e) {
                // Swallow network noise, return null for callers
                return null;
            }
        };

        // Lazy load low stock data for header notification
        function loadLowStockHeader() {
            lazyFetch('/api/low-stock').then(function(data) {
                if (!data) return;
                var badge = document.getElementById('lowStockBadge');
                var content = document.getElementById('lowStockContent');
                var footer = document.getElementById('lowStockFooter');

                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = '';
                    var html = '';
                    data.products.forEach(function(p) {
                        var cls = p.stock === 0 ? 'critical' : 'warning';
                        var icon = p.stock === 0 ? '🚨 Out of stock' : '⚠️ Only ' + p.stock + ' left';
                        html += '<div class="notification-item ' + cls + '">' +
                            '<strong>' + p.name + '</strong><br>' +
                            '<small style="color:#666;">' + icon + '</small></div>';
                    });
                    content.innerHTML = html;
                    footer.style.display = '';
                } else {
                    badge.style.display = 'none';
                    content.innerHTML = '<div class="notification-empty">' +
                        '<i class="fas fa-check-circle" style="color:#28a745;"></i>' +
                        '<p>All stocked up! 🎉</p></div>';
                    footer.style.display = 'none';
                }
            }).catch(function(err) {
                console.error('Failed to load low stock:', err);
                document.getElementById('lowStockContent').innerHTML =
                    '<div class="notification-empty"><p style="color:#999;">Could not load alerts</p></div>';
            });
        }

        // Fire after first paint
        if ('requestIdleCallback' in window) {
            requestIdleCallback(loadLowStockHeader, { timeout: 2000 });
        } else {
            setTimeout(loadLowStockHeader, 300);
        }

        // Online/Offline Detection
        const offlineIndicator = document.getElementById('offline-indicator');
        // Toast guards to prevent spammy notifications
        window._offlineToastShown = false;
        window._pendingToastLast = 0;

        window.addEventListener('online', async () => {
            offlineIndicator.style.display = 'none';
            console.log('Back online - verifying connectivity...');
            const online = await isActuallyOnline();
            if (online) {
                showToast('✅ Back online! Syncing data...', 'success');
                sessionStorage.setItem('syncFailCount', '0');
                // reset offline toast guard
                window._offlineToastShown = false;
                setTimeout(() => {
                    syncPendingData();
                }, 1000);
            }
        });

        window.addEventListener('offline', () => {
            offlineIndicator.style.display = 'block';
            console.log('📴 Gone offline!');
            if (!window._offlineToastShown) {
                showToast('📴 You are now offline', 'warning');
                window._offlineToastShown = true;
                // Show system notification if allowed
                try {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        showNotification('📴 You are offline', {
                            body: 'Sales and products will save locally and sync later.',
                            requireInteraction: false
                        });
                    }
                } catch (e) {}
            }
        });

        // Check initial status
        if (!navigator.onLine) {
            offlineIndicator.style.display = 'block';
        }

        // PWA Install Prompt
        let deferredPrompt;
        const installPrompt = document.getElementById('install-prompt');
        const installButton = document.getElementById('install-button');
        const dismissButton = document.getElementById('dismiss-button');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            if (!localStorage.getItem('pwa-dismissed')) {
                setTimeout(() => {
                    installPrompt.style.display = 'block';
                }, 3000);
            }
        });

        if (installButton) {
            installButton.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const {
                        outcome
                    } = await deferredPrompt.userChoice;
                    console.log(`User response: ${outcome}`);
                    deferredPrompt = null;
                    installPrompt.style.display = 'none';
                }
            });
        }

        if (dismissButton) {
            dismissButton.addEventListener('click', () => {
                installPrompt.style.display = 'none';
                localStorage.setItem('pwa-dismissed', 'true');
            });
        }

        // Fetch a fresh CSRF token from the server
        async function getFreshCsrfToken() {
            if (!navigator.onLine) {
                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            }
            if (window._networkBackoffUntil && Date.now() < window._networkBackoffUntil) {
                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            }
            try {
                const response = await fetch('/api/csrf-token', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json' }
                });

                if (response.status === 401) {
                    showToast('Session expired. Please log in again.', 'error');
                    setTimeout(() => window.location.href = '/login', 2000);
                    return null;
                }

                if (response.ok) {
                    const data = await response.json();
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag && data.csrf_token) {
                        metaTag.setAttribute('content', data.csrf_token);
                    }
                    return data.csrf_token;
                }

                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            } catch (err) {
                const now = Date.now();
                const prev = window._networkBackoffUntil && now < window._networkBackoffUntil ? (window._networkBackoffUntil - now) : 0;
                const next = Math.min(5 * 60 * 1000, Math.max(15000, prev ? prev * 2 : 30000));
                window._networkBackoffUntil = now + next;
                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            }
        }
        // Make it globally available for other pages
        window.getFreshCsrfToken = getFreshCsrfToken;
        // Prefetch CSRF on load to reduce checkout latency
        window.addEventListener('load', () => {
            getFreshCsrfToken().catch(() => {});
        });
        // Language support (basic i18n)
        const I18N = {
            en: {
                'nav.dashboard': 'Dashboard',
                'nav.products': 'Products',
                'nav.quick_sale': 'Quick Sale',
                'nav.sales_history': 'Sales History',
                'nav.expenses': 'Profit Calculator',
                'nav.stock_alerts': 'Stock Alerts',
                'nav.reports': 'Sales Report',
                'nav.settings': 'Settings'
            },
            tl: {
                'nav.dashboard': 'Dashboard',
                'nav.products': 'Mga Produkto',
                'nav.quick_sale': 'Mabilis na Benta',
                'nav.sales_history': 'Kasaysayan ng Benta',
                'nav.expenses': 'Kalkulahin ang Kita',
                'nav.stock_alerts': 'Mga Alerto sa Stock',
                'nav.reports': 'Ulat ng Benta',
                'nav.settings': 'Mga Setting'
            }
        };
        window.applyLanguage = function(lang) {
            const dict = I18N[lang] || I18N.en;
            // Sidebar labels
            const links = document.querySelectorAll('.sidebar a');
            links.forEach(a => {
                if (a.href.includes('/dashboard')) a.lastChild.textContent = ' ' + dict['nav.dashboard'];
                else if (a.href.includes('/products')) a.lastChild.textContent = ' ' + dict['nav.products'];
                else if (a.href.includes('/quick-sale')) a.lastChild.textContent = ' ' + dict['nav.quick_sale'];
                else if (a.href.includes('/sales')) a.lastChild.textContent = ' ' + dict['nav.sales_history'];
                else if (a.href.includes('/expenses')) a.lastChild.textContent = ' ' + dict['nav.expenses'];
                else if (a.href.includes('/stock-alerts')) a.lastChild.textContent = ' ' + dict['nav.stock_alerts'];
                else if (a.href.includes('/reports')) a.lastChild.textContent = ' ' + dict['nav.reports'];
                else if (a.href.includes('/settings')) a.lastChild.textContent = ' ' + dict['nav.settings'];
            });
        };
        const savedLang = localStorage.getItem('streetpos_lang') || 'en';
        window.applyLanguage(savedLang);

        // Sync single sale with retry on 419
        async function syncSaleWithRetry(sale, token, retryCount = 0) {
            const response = await fetch('/api/sync-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(sale)
            });

            if (response.status === 419 && retryCount < 1) {
                // Token expired - get fresh and retry once
                console.log('419 error - retrying with fresh token...');
                const freshToken = await getFreshCsrfToken();
                if (freshToken) {
                    return syncSaleWithRetry(sale, freshToken, retryCount + 1);
                }
            }

            return response.json();
        }

        // Check real connectivity (throttled, no abort to avoid noisy logs)
        async function isActuallyOnline() {
            const now = Date.now();
            if (!navigator.onLine) {
                window._lastOnlineCheckTime = now;
                window._lastOnlineCheckResult = false;
                return false;
            }
            if (window._networkBackoffUntil && now < window._networkBackoffUntil) {
                window._lastOnlineCheckTime = now;
                window._lastOnlineCheckResult = false;
                return false;
            }
            if (window._lastOnlineCheckTime && (now - window._lastOnlineCheckTime) < 5000) {
                return !!window._lastOnlineCheckResult;
            }
            try {
                const response = await fetch('/api/csrf-token', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store'
                });
                const ok = (response.ok || response.status === 401);
                window._lastOnlineCheckTime = Date.now();
                window._lastOnlineCheckResult = ok;
                return ok;
            } catch {
                const now2 = Date.now();
                window._lastOnlineCheckTime = now2;
                window._lastOnlineCheckResult = false;
                const prev = window._networkBackoffUntil && now2 < window._networkBackoffUntil ? (window._networkBackoffUntil - now2) : 0;
                const next = Math.min(5 * 60 * 1000, Math.max(15000, prev ? prev * 2 : 30000));
                window._networkBackoffUntil = now2 + next;
                return false;
            }
        }

        // Sync pending data when back online
        async function syncPendingData() {
            try {
                // Get a fresh CSRF token before syncing
                const freshToken = await getFreshCsrfToken();

                // Sync pending products from localStorage
                const pendingProducts = JSON.parse(localStorage.getItem('pendingProducts') || '[]');
                let productSyncedCount = 0;
                let productFailedCount = 0;

                if (pendingProducts.length > 0) {
                    console.log('📤 Syncing', pendingProducts.length, 'pending products...');
                    showToast('🔄 Syncing ' + pendingProducts.length + ' offline products...', 'info');

                    for (const product of pendingProducts) {
                        try {
                            const response = await fetch('/api/sync-product', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': freshToken
                                },
                                body: JSON.stringify(product)
                            });

                            const data = await response.json();

                            if (data.success) {
                                productSyncedCount++;
                            } else {
                                console.error('Product sync failed:', data.error);
                                productFailedCount++;
                            }
                        } catch (err) {
                            console.error('Product sync failed:', err);
                            productFailedCount++;
                        }
                    }

                    // Clear synced products from localStorage
                    if (productSyncedCount > 0) {
                        localStorage.removeItem('pendingProducts');
                        showToast('✅ ' + productSyncedCount + ' products synced successfully!', 'success');
                    }

                    if (productFailedCount > 0) {
                        showToast('⚠️ ' + productFailedCount + ' products failed to sync', 'error');
                    }
                }

                // Get pending sales from IndexedDB
                let pendingSales = await offlineDB.getPendingSales();

                // Discard stale pending sales older than 10 minutes to prevent repeated notices
                const now = Date.now();
                const staleSales = [];
                const freshSales = [];
                for (const s of pendingSales) {
                    const ts = Date.parse(s.timestamp || '');
                    if (isNaN(ts) || (now - ts) > (10 * 60 * 1000)) {
                        staleSales.push(s);
                    } else {
                        freshSales.push(s);
                    }
                }
                if (staleSales.length > 0) {
                    for (const s of staleSales) {
                        try {
                            await offlineDB.removePendingSale(s.id);
                        } catch (e) {
                            console.error('Failed to discard stale sale', s.id, e);
                        }
                    }
                    showToast('🗑️ Discarded ' + staleSales.length + ' stale offline sale(s)', 'warning');
                }
                pendingSales = freshSales;

                if (pendingSales.length === 0 && pendingProducts.length === 0) {
                    console.log('No pending data to sync');
                    return;
                }

                if (pendingSales.length > 0) {
                    console.log('📤 Syncing', pendingSales.length, 'pending sales...');
                    showToast('🔄 Syncing ' + pendingSales.length + ' offline sales...', 'info');
                }

                let saleSyncedCount = 0;
                let saleFailedCount = 0;

                // Sync sales SEQUENTIALLY to avoid token race conditions
                for (const sale of pendingSales) {
                    try {
                        const data = await syncSaleWithRetry(sale, freshToken);
                        if (data.success) {
                            await offlineDB.removePendingSale(sale.id);
                            saleSyncedCount++;
                        } else {
                            console.error('Sync failed:', data.message || data.error);
                            // Remove failed sale to avoid repeated notifications
                            try {
                                await offlineDB.removePendingSale(sale.id);
                            } catch (e) {
                                console.error('Failed to remove failed sale', sale.id, e);
                            }
                            saleFailedCount++;
                        }
                    } catch (err) {
                        console.error('Sale sync error:', err);
                        // Remove failed sale to avoid repeated notifications
                        try {
                            await offlineDB.removePendingSale(sale.id);
                        } catch (e) {
                            console.error('Failed to remove failed sale', sale.id, e);
                        }
                        saleFailedCount++;
                    }
                }

                if (saleSyncedCount > 0) {
                    showToast('✅ ' + saleSyncedCount + ' sales synced successfully!', 'success');
                }

                if (saleFailedCount > 0) {
                    showToast('⚠️ ' + saleFailedCount + ' sales failed to sync', 'error');
                }

                // Reload page if anything was synced (with cache-bust to bypass SW)
                if (productSyncedCount > 0 || saleSyncedCount > 0) {
                    sessionStorage.setItem('syncFailCount', '0');
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?_fresh=1';
                    }, 2000);
                }
            } catch (err) {
                console.error('Error syncing pending data:', err);
                const fails = parseInt(sessionStorage.getItem('syncFailCount') || '0') + 1;
                sessionStorage.setItem('syncFailCount', fails.toString());
                if (fails >= 3) {
                    showToast('Sync paused after repeated failures. Refresh to retry.', 'warning');
                } else {
                    showToast('❌ Failed to sync pending data', 'error');
                }
            }
        }

        // Toast notification helper
        function showToast(message, type = 'info') {
            const colors = {
                info: '#17a2b8',
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107'
            };

            // Remove existing toast with the same message to prevent spam
            document.querySelectorAll('.streetpos-toast').forEach(existing => {
                if (existing.dataset.message === message) existing.remove();
            });

            const toast = document.createElement('div');
            toast.className = 'streetpos-toast';
            toast.dataset.message = message;
            toast.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 15px 35px 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 10000;
                animation: slideInRight 0.3s;
                max-width: 300px;
            `;
            toast.textContent = message;

            const closeBtn = document.createElement('span');
            closeBtn.textContent = '\u00D7';
            closeBtn.style.cssText = 'position:absolute; top:8px; right:10px; cursor:pointer; font-size:18px; font-weight:bold; line-height:1;';
            closeBtn.onclick = () => toast.remove();
            toast.style.position = 'fixed';
            toast.appendChild(closeBtn);

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.animation = 'slideOutRight 0.3s';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 3000);
        }

        // Receipt Modal Functions
        function openReceiptModal(saleId) {
            var modal = document.getElementById('receiptModal');
            var body = document.getElementById('receiptModalBody');
            var pdfLink = document.getElementById('receiptPdfLink');

            body.innerHTML = '<div class="receipt-modal-loading"><i class="fas fa-spinner fa-spin" style="font-size:24px; color:#800000;"></i><p>Loading receipt...</p></div>';
            modal.classList.add('active');
            pdfLink.href = '/sales/' + saleId + '/receipt/pdf';

            fetch('/sales/' + saleId + '/receipt/json', {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(response) {
                if (!response.ok) throw new Error('Failed to load receipt');
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    body.innerHTML = buildReceiptHTML(data.receipt);
                } else {
                    body.innerHTML = '<div class="receipt-modal-loading"><i class="fas fa-exclamation-circle" style="font-size:24px; color:#dc3545;"></i><p>' + (data.message || 'Failed to load receipt') + '</p></div>';
                }
            })
            .catch(function(error) {
                console.error('Receipt load error:', error);
                body.innerHTML = '<div class="receipt-modal-loading"><i class="fas fa-exclamation-circle" style="font-size:24px; color:#dc3545;"></i><p>Failed to load receipt. Please try again.</p></div>';
            });
        }

        function buildReceiptHTML(r) {
            return '<div style="font-family:\'Courier New\',monospace; padding:20px;">' +
                '<div style="text-align:center; border-bottom:2px dashed #333; padding-bottom:15px; margin-bottom:15px;">' +
                    '<div style="font-size:20px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">&#127842; StreetPOS</div>' +
                    '<div style="font-size:11px; color:#666; margin-bottom:10px;">Your Neighborhood Street Food</div>' +
                    '<div style="font-size:11px; color:#666; line-height:1.4;">' +
                        '<div>Receipt #: ' + r.receipt_number + '</div>' +
                        '<div>Date: ' + r.date + '</div>' +
                        '<div>Time: ' + r.time + '</div>' +
                        '<div>Cashier: ' + r.cashier + '</div>' +
                    '</div>' +
                '</div>' +
                '<div style="margin:15px 0;">' +
                    '<div style="font-size:12px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:5px; margin-bottom:10px; text-transform:uppercase;">Order Details</div>' +
                    '<div style="margin-bottom:10px; font-size:12px;">' +
                        '<div style="font-weight:bold; margin-bottom:3px;">' + r.product_name + '</div>' +
                        '<div style="display:flex; justify-content:space-between; color:#666; font-size:11px;">' +
                            '<span>' + r.quantity + ' &times; &#8369;' + r.unit_price + '</span>' +
                            '<span>&#8369;' + r.total + '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div style="border-top:2px dashed #333; padding-top:10px; margin-top:10px;">' +
                    '<div style="display:flex; justify-content:space-between; margin-bottom:5px; font-size:12px;">' +
                        '<span>Subtotal:</span>' +
                        '<span>&#8369;' + r.total + '</span>' +
                    '</div>' +
                    '<div style="display:flex; justify-content:space-between; font-size:16px; font-weight:bold; border-top:1px solid #333; padding-top:8px; margin-top:8px;">' +
                        '<span>TOTAL:</span>' +
                        '<span>&#8369;' + r.total + '</span>' +
                    '</div>' +
                '</div>' +
                '<div style="text-align:center; border-top:2px dashed #333; padding-top:15px; margin-top:15px; font-size:11px;">' +
                    '<div style="font-weight:bold; margin-bottom:5px; font-size:13px;">SALAMAT PO! &#128591;</div>' +
                    '<div style="color:#666; line-height:1.4;">Thank you for your purchase!<br>Come again soon! &#128522;</div>' +
                '</div>' +
            '</div>';
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.remove('active');
            document.getElementById('receiptModalDialog').classList.remove('expanded');
            document.getElementById('receiptExpandBtn').innerHTML = '<i class="fas fa-expand"></i>';
        }

        function toggleReceiptExpand() {
            var dialog = document.getElementById('receiptModalDialog');
            var btn = document.getElementById('receiptExpandBtn');
            dialog.classList.toggle('expanded');
            btn.innerHTML = dialog.classList.contains('expanded')
                ? '<i class="fas fa-compress"></i>'
                : '<i class="fas fa-expand"></i>';
        }

        function printReceipt() {
            window.print();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('receiptModal').classList.contains('active')) {
                closeReceiptModal();
            }
        });

        // Request notification permission
        async function requestNotificationPermission() {
            if ('Notification' in window && 'serviceWorker' in navigator) {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    console.log('✅ Notification permission granted');
                    return true;
                } else {
                    console.log('❌ Notification permission denied');
                    return false;
                }
            }
            return false;
        }

        // Show browser notification
        function showNotification(title, options = {}) {
            if ('Notification' in window && Notification.permission === 'granted') {
                if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    // Use service worker to show notification (works even when tab is closed)
                    navigator.serviceWorker.ready.then(registration => {
                        registration.showNotification(title, {
                            icon: '/icon-192.png',
                            badge: '/icon-192.png',
                            vibrate: [200, 100, 200],
                            ...options
                        });
                    });
                } else {
                    // Fallback to regular notification
                    new Notification(title, {
                        icon: '/icon-192.png',
                        ...options
                    });
                }
            }
        }

        // Check for pending data on page load and auto-sync if online
        window.addEventListener('load', () => {
            // Request notification permission on first load
            if ('Notification' in window && Notification.permission === 'default') {
                setTimeout(() => {
                    requestNotificationPermission();
                }, 3000); // Wait 3 seconds before asking
            }

            // Defer pending-data check so it doesn't block page interactivity
            const checkPendingData = async () => {
                try {
                    const pendingProducts = JSON.parse(localStorage.getItem('pendingProducts') || '[]');
                    const pendingSales = await offlineDB.getPendingSales();

                    const totalPending = pendingProducts.length + pendingSales.length;

                    if (totalPending > 0) {
                        console.log('Found pending data:', { products: pendingProducts.length, sales: pendingSales.length });

                        if (navigator.onLine) {
                            // Check backoff before auto-syncing
                            const syncFailCount = parseInt(sessionStorage.getItem('syncFailCount') || '0');
                            if (syncFailCount >= 3) {
                                showToast('Sync paused after repeated failures. Refresh to retry.', 'warning');
                            } else {
                                // Verify real connectivity before syncing
                                const online = await isActuallyOnline();
                                if (online) {
                                    showToast('🔄 Syncing ' + totalPending + ' offline item(s)...', 'info');
                                    setTimeout(() => {
                                        syncPendingData();
                                    }, 1500);
                                }
                            }
                        } else {
                            // Just show notification if offline
                            let message = '📦 You have ';
                            if (pendingProducts.length > 0) {
                                message += pendingProducts.length + ' offline product' + (pendingProducts.length > 1 ? 's' : '');
                            }
                            if (pendingSales.length > 0) {
                                if (pendingProducts.length > 0) message += ' and ';
                                message += pendingSales.length + ' offline sale' + (pendingSales.length > 1 ? 's' : '');
                            }
                            message += ' waiting to sync';
                            const now = Date.now();
                            if (!window._pendingToastLast || (now - window._pendingToastLast) > 60000) {
                                showToast(message, 'warning');
                                window._pendingToastLast = now;
                            }
                        }
                    }
                } catch (err) {
                    console.error('Error checking pending data:', err);
                }
            };

            if ('requestIdleCallback' in window) {
                requestIdleCallback(checkPendingData, { timeout: 5000 });
            } else {
                setTimeout(checkPendingData, 2000);
            }
        });
    </script>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="receipt-modal">
        <div class="receipt-modal-backdrop" onclick="closeReceiptModal()"></div>
        <div class="receipt-modal-dialog" id="receiptModalDialog">
            <div class="receipt-modal-header">
                <span class="receipt-modal-title">Receipt</span>
                <div class="receipt-modal-actions">
                    <button class="receipt-modal-btn" onclick="toggleReceiptExpand()" title="Expand/Collapse" id="receiptExpandBtn">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="receipt-modal-btn" onclick="printReceipt()" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                    <a id="receiptPdfLink" href="#" class="receipt-modal-btn" title="Download PDF" target="_blank">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    <button class="receipt-modal-btn receipt-modal-close" onclick="closeReceiptModal()" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="receipt-modal-body" id="receiptModalBody">
                <div class="receipt-modal-loading">
                    <i class="fas fa-spinner fa-spin" style="font-size:24px; color:#800000;"></i>
                    <p>Loading receipt...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Offline Indicator -->
    <div id="offline-indicator" style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background:#dc3545; color:#fff; padding:12px 24px; border-radius:25px; box-shadow:0 4px 12px rgba(0,0,0,0.3); z-index:9999; font-weight:600; animation:slideUp 0.3s;">
        <i class="fas fa-wifi-slash"></i>
        You're Offline - Sales will sync when connected
    </div>

    <!-- PWA Install Prompt -->
    <div id="install-prompt" style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background:#4CAF50; color:#fff; padding:15px 25px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.3); z-index:9999; max-width:90%; text-align:center;">
        <p style="margin:0 0 10px 0; font-weight:600;">📱 Install StreetPOS on your device!</p>
        <button id="install-button" style="background:#fff; color:#4CAF50; border:none; padding:8px 20px; border-radius:5px; font-weight:600; cursor:pointer; margin-right:10px;">
            Install Now
        </button>
        <button id="dismiss-button" style="background:transparent; color:#fff; border:1px solid #fff; padding:8px 20px; border-radius:5px; font-weight:600; cursor:pointer;">
            Maybe Later
        </button>
    </div>

    <style>
        @keyframes slideUp {
            from {
                transform: translateX(-50%) translateY(100px);
                opacity: 0;
            }

            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>

    @stack('scripts')
</body>

</html>
