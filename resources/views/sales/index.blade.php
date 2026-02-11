@extends('layout')

@section('content')
<style>
    .sales-container {
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

    .section-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .modern-table {
            font-size: 13px;
        }

        .modern-table th,
        .modern-table td {
            padding: 8px;
        }
    }
</style>

<div class="sales-container">
    <div class="page-header">
        <h2>📊 Sales History</h2>
        <p>Track your daily sales and transactions</p>
    </div>

    <div class="section-card">
        <div id="salesContent">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 10; $i++)
                    <tr>
                        <td><div class="skeleton skeleton-text" style="width:60%"></div></td>
                        <td><div class="skeleton skeleton-text" style="width:30px"></div></td>
                        <td><div class="skeleton skeleton-text" style="width:80px"></div></td>
                        <td><div class="skeleton skeleton-text" style="width:130px"></div></td>
                        <td><div class="skeleton" style="width:70px; height:28px; border-radius:5px;"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div id="salesPagination" style="margin-top: 20px; display: flex; justify-content: center;"></div>
    </div>
</div>

@push('scripts')
<script>
    function renderSales(data) {
        var container = document.getElementById('salesContent');

        if (!data.sales || data.sales.length === 0) {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-receipt"></i>' +
                '<h3 style="color:#666; margin:0 0 10px 0;">No Sales Yet</h3>' +
                '<p>Start by recording your first sale or use Quick Sale Mode</p></div>';
            document.getElementById('salesPagination').innerHTML = '';
            return;
        }

        var html = '<table class="modern-table"><thead><tr>' +
            '<th>Product</th><th>Quantity</th><th>Total</th><th>Date</th><th>Actions</th>' +
            '</tr></thead><tbody>';

        data.sales.forEach(function(s) {
            html += '<tr>' +
                '<td class="product-name">' + s.product_name + '</td>' +
                '<td>' + s.quantity + '</td>' +
                '<td class="amount">\u20B1' + s.total + '</td>' +
                '<td class="date-time">' + s.date + '</td>' +
                '<td>' +
                    '<button onclick="openReceiptModal(' + s.id + ')" ' +
                    'style="background:#800000; color:#FFD700; border:none; padding:6px 12px; border-radius:5px; cursor:pointer; font-size:12px; font-weight:600; transition:all 0.3s; margin-right:6px;" ' +
                    'onmouseover="this.style.background=\'#a00000\'" onmouseout="this.style.background=\'#800000\'" title="View Receipt">' +
                    '<i class="fas fa-receipt"></i> Receipt</button>' +
                    '<button onclick="deleteSale(' + s.id + ')" ' +
                    'style="background:#dc3545; color:#fff; border:none; padding:6px 12px; border-radius:5px; cursor:pointer; font-size:12px; font-weight:600; transition:all 0.3s;" ' +
                    'onmouseover="this.style.background=\'#b02a37\'" onmouseout="this.style.background=\'#dc3545\'" title="Delete Sale">' +
                    '<i class="fas fa-trash"></i> Delete</button>' +
                '</td>' +
            '</tr>';
        });

        html += '</tbody></table>';
        container.innerHTML = html;

        // Render pagination
        var pag = data.pagination;
        var pagHtml = '';
        if (pag.has_pages) {
            for (var i = 1; i <= pag.last_page; i++) {
                var active = i === pag.current_page ? 'background:#800000; color:#fff;' : 'background:#fff; color:#800000;';
                pagHtml += '<a href="?page=' + i + '" style="' + active + ' padding:8px 14px; border:1px solid #800000; border-radius:5px; text-decoration:none; font-weight:600; margin:0 3px;" onclick="event.preventDefault(); loadSales(' + i + ');">' + i + '</a>';
            }
        }
        document.getElementById('salesPagination').innerHTML = pagHtml;
    }

    function loadSales(page) {
        var url = '/api/sales' + (page ? '?page=' + page : window.location.search || '');
        lazyFetch(url).then(function(data) {
            if (data) renderSales(data);
        }).catch(function(err) {
            console.error('Sales load error:', err);
            document.getElementById('salesContent').innerHTML =
                '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Failed to load sales</h3><p>Please refresh the page</p></div>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var params = new URLSearchParams(window.location.search);
        var page = params.get('page');
        loadSales(page ? parseInt(page) : null);
    });

    async function deleteSale(id) {
        if (!confirm('Delete this sale?')) return;
        try {
            const token = await (window.getFreshCsrfToken ? window.getFreshCsrfToken() : (document.querySelector('meta[name="csrf-token"]')?.content || ''));
            const res = await fetch('/sales/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) {
                showToast('Failed to delete sale', 'error');
                return;
            }
            const data = await res.json().catch(() => ({ success: res.ok }));
            if (data && data.success) {
                showToast('Sale deleted', 'success');
                // Reload current page of sales
                var currentPage = parseInt(new URLSearchParams(window.location.search).get('page') || '1');
                loadSales(isNaN(currentPage) ? null : currentPage);
            } else {
                showToast('Failed to delete sale', 'error');
            }
        } catch (err) {
            console.error('Delete sale error:', err);
            showToast('Failed to delete sale', 'error');
        }
    }
</script>
@endpush

@endsection
