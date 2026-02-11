<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Courier New', monospace; background:#f5f5f5; padding:20px; display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .receipt-container { width:100%; max-width:360px; background:#fff; padding:20px; box-shadow:0 0 20px rgba(0,0,0,0.1); border:2px dashed #ddd; }
        .receipt-header { text-align:center; border-bottom:2px dashed #333; padding-bottom:15px; margin-bottom:15px; }
        .store-name { font-size:20px; font-weight:bold; margin-bottom:5px; text-transform:uppercase; letter-spacing:1px; }
        .store-tagline { font-size:11px; color:#666; margin-bottom:10px; }
        .receipt-info { font-size:11px; color:#666; line-height:1.4; }
        .section-title { font-size:12px; font-weight:bold; border-bottom:1px solid #333; padding-bottom:5px; margin-bottom:10px; text-transform:uppercase; }
        .item-row { margin-bottom:10px; font-size:12px; }
        .item-name { font-weight:bold; margin-bottom:3px; }
        .item-details { display:flex; justify-content:space-between; color:#666; font-size:11px; }
        .receipt-totals { border-top:2px dashed #333; padding-top:10px; margin-top:10px; }
        .total-row { display:flex; justify-content:space-between; margin-bottom:5px; font-size:12px; }
        .total-row.grand-total { font-size:16px; font-weight:bold; border-top:1px solid #333; padding-top:8px; margin-top:8px; }
        .receipt-footer { text-align:center; border-top:2px dashed #333; padding-top:15px; margin-top:15px; font-size:11px; }
        .thank-you { font-weight:bold; margin-bottom:5px; font-size:13px; }
        .action-buttons { display:flex; gap:8px; margin-top:12px; }
        .btn { padding:8px 12px; border:1px solid #333; background:#fff; cursor:pointer; font-size:12px; }
        @media print { .action-buttons { display:none; } body { background:#fff; padding:0; } .receipt-container { box-shadow:none; border:none; } }
    </style>
}</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="store-name">🍢 StreetPOS</div>
            <div class="store-tagline">Your Neighborhood Street Food</div>
            <div class="receipt-info">
                <div>Date: {{ $sales->first()->created_at->format('M d, Y') }}</div>
                <div>Time: {{ $sales->first()->created_at->format('h:i A') }}</div>
                <div>Cashier: {{ $sales->first()->user->name ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="section-title">Order Details</div>
        @foreach($sales as $s)
            <div class="item-row">
                <div class="item-name">{{ $s->product->name ?? 'Deleted Product' }}</div>
                <div class="item-details">
                    <span>{{ $s->quantity }} × ₱{{ number_format($s->product->price ?? 0, 2) }}</span>
                    <span>₱{{ number_format($s->total, 2) }}</span>
                </div>
            </div>
        @endforeach
        <div class="receipt-totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₱{{ number_format($grandTotal, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>₱{{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>
        <div class="receipt-footer">
            <div class="thank-you">SALAMAT PO! 🙏</div>
            <div>Thank you for your purchase!</div>
        </div>
        <div class="action-buttons">
            <button class="btn" onclick="window.print()">🖨️ Print</button>
            <a href="{{ route('sales.index') }}" class="btn">← Back</a>
        </div>
    </div>
</body>
</html>
