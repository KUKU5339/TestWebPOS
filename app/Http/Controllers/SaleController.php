<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    private function clearSaleCaches()
    {
        $userId = auth()->id();
        $user = auth()->user();
        $threshold = $user->default_stock_threshold ?? 5;

        Cache::forget("dashboard_total_sales_{$userId}");
        Cache::forget("dashboard_total_items_{$userId}");
        Cache::forget("dashboard_today_sales_{$userId}");
        Cache::forget("dashboard_today_revenue_{$userId}");
        Cache::forget("dashboard_low_stock_{$userId}");
        Cache::forget("dashboard_top_products_{$userId}");
        Cache::forget("dashboard_stats_{$userId}");
        Cache::forget("dashboard_today_{$userId}");
        Cache::forget("low_stock_{$userId}_{$threshold}");
    }

    public function index()
    {
        return view('sales.index');
    }

    public function indexJson(Request $request)
    {
        $sales = Sale::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(50);

        return response()->json([
            'sales' => $sales->map(fn($s) => [
                'id' => $s->id,
                'product_name' => $s->product->name ?? 'Deleted Product',
                'quantity' => $s->quantity,
                'total' => number_format($s->total, 2),
                'date' => $s->created_at->format('M d, Y h:i A'),
            ]),
            'pagination' => [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'total' => $sales->total(),
                'has_pages' => $sales->hasPages(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        // Check if this is a cart-based sale (from Quick Sale Mode)
        if ($request->has('cart')) {
            $cartData = $request->input('cart');

            // Handle JSON requests
            if (is_string($cartData)) {
                $cartData = json_decode($cartData, true);
            }

            if (empty($cartData)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Cart is empty!'], 400);
                }
                return back()->withErrors(['cart' => 'Cart is empty!']);
            }

            $sales = [];
            $totalAmount = 0;

            foreach ($cartData as $item) {
                $product = Product::where('id', $item['id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$product) continue;

                $quantity = (int) $item['quantity'];

                if ($quantity > $product->stock) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Not enough stock for {$product->name}!"
                        ], 400);
                    }
                    return back()->withErrors(['quantity' => "Not enough stock for {$product->name}!"]);
                }

                $total = $product->price * $quantity;
                $totalAmount += $total;

                $sale = Sale::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'total'      => $total,
                ]);

                $product->decrement('stock', $quantity);
                $sales[] = $sale->id;
            }

            $this->clearSaleCaches();

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully!',
                    'total' => $totalAmount,
                    'sales_count' => count($sales),
                    'sale_id' => $sales[0] ?? null,  // Return first sale ID for legacy receipt
                    'sale_ids' => $sales
                ]);
            }

            return redirect()->route('sales.receipt.batch')
                ->with('success', 'Sale completed!')
                ->with('sale_ids', $sales)
                ->with('sale_notification', [
                    'total' => $totalAmount,
                    'items' => count($cartData)
                ]);
        }

        // Original single-product sale logic (at the bottom of store method)
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock!'
                ], 400);
            }
            return back()->withErrors(['quantity' => 'Not enough stock!']);
        }

        $total = $product->price * $quantity;

        $sale = Sale::create([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'total'      => $total,
        ]);

        $product->decrement('stock', $quantity);

        $this->clearSaleCaches();

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sale saved successfully!',
                'total' => $total,
                'sale_id' => $sale->id  // Return sale ID for receipt
            ]);
        }

        // Regular redirect for non-AJAX
        return redirect()->route('sales.receipt', $sale->id);
    }

    public function quickSale()
    {
        return view('sales.quick-sale');
    }

    public function productsInStockJson()
    {
        $products = Product::where('user_id', auth()->id())
            ->where('stock', '>', 0)
            ->get();

        return response()->json([
            'products' => $products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'stock' => $p->stock,
                'image_url' => $p->image_url,
            ]),
        ]);
    }

    public function generateReceipt($saleId)
    {
        $sale = Sale::with('product')
            ->where('id', $saleId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        if (!$sale->product) {
            return redirect()->route('sales.index')->with('error', 'Receipt cannot be generated. Product has been deleted.');
        }

        return view('receipt', compact('sale'));
    }

    public function downloadReceiptPdf($saleId)
    {
        $sale = Sale::with('product')
            ->where('id', $saleId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        if (!$sale->product) {
            return redirect()->route('sales.index')->with('error', 'Receipt cannot be generated. Product has been deleted.');
        }

        $pdf = Pdf::loadView('receipt', compact('sale'));
        return $pdf->download('receipt.pdf');
    }

    public function batchReceipt()
    {
        $ids = session('sale_ids', []);
        if (empty($ids)) {
            return redirect()->route('sales.index')->with('error', 'No receipt data.');
        }
        $sales = Sale::with(['product', 'user'])
            ->whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->get();
        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No receipt data.');
        }
        $grandTotal = $sales->sum('total');
        return view('receipt-batch', compact('sales', 'grandTotal'));
    }

    public function downloadBatchReceiptPdf(Request $request)
    {
        $idsParam = $request->query('sale_ids', []);
        if (is_string($idsParam)) {
            $ids = array_filter(array_map('intval', explode(',', $idsParam)));
        } elseif (is_array($idsParam)) {
            $ids = array_filter(array_map('intval', $idsParam));
        } else {
            $ids = [];
        }

        if (empty($ids)) {
            return redirect()->route('sales.index')->with('error', 'No receipt data.');
        }

        $sales = Sale::with(['product', 'user'])
            ->whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'No receipt data.');
        }

        $grandTotal = $sales->sum('total');
        $pdf = Pdf::loadView('receipt-batch', compact('sales', 'grandTotal'));
        return $pdf->download('receipt.pdf');
    }

    public function destroy($id, Request $request)
    {
        $sale = Sale::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$sale) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
            }
            return redirect()->route('sales.index')->with('error', 'Sale not found');
        }

        $sale->delete();

        $this->clearSaleCaches();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('sales.index')->with('success', 'Sale deleted');
    }
    public function getReceiptData($saleId)
    {
        $sale = Sale::with(['product', 'user'])
            ->where('id', $saleId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found.'], 404);
        }

        if (!$sale->product) {
            return response()->json(['success' => false, 'message' => 'Product has been deleted.'], 404);
        }

        return response()->json([
            'success' => true,
            'receipt' => [
                'id' => $sale->id,
                'receipt_number' => str_pad($sale->id, 6, '0', STR_PAD_LEFT),
                'date' => $sale->created_at->format('M d, Y'),
                'time' => $sale->created_at->format('h:i A'),
                'cashier' => $sale->user->name ?? 'N/A',
                'product_name' => $sale->product->name,
                'quantity' => $sale->quantity,
                'unit_price' => number_format($sale->product->price, 2),
                'total' => number_format($sale->total, 2),
                'pdf_url' => route('sales.receipt.pdf', $sale->id),
            ]
        ]);
    }

    public function syncOfflineSale(Request $request)
    {
        try {
            $saleData = $request->all();

            // Validate the incoming data
            if (!isset($saleData['cart']) || empty($saleData['cart'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart data provided'
                ], 400);
            }

            $cartData = $saleData['cart'];
            if (is_string($cartData)) {
                $cartData = json_decode($cartData, true);
            }

            $sales = [];
            $totalAmount = 0;
            $skippedItems = [];

            foreach ($cartData as $item) {
                $product = Product::where('id', $item['id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if (!$product) {
                    $skippedItems[] = $item['name'] ?? "Product ID {$item['id']}";
                    continue;
                }

                $quantity = (int) $item['quantity'];

                // Check stock availability
                if ($quantity > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough stock for {$product->name}. Available: {$product->stock}"
                    ], 400);
                }

                $total = $product->price * $quantity;
                $totalAmount += $total;

                // Create the sale
                $sale = Sale::create([
                    'user_id'    => auth()->id(),
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'total'      => $total,
                ]);

                // Update stock
                $product->decrement('stock', $quantity);
                $sales[] = $sale->id;
            }

            $this->clearSaleCaches();

            $message = 'Offline sale synced successfully';
            if (!empty($skippedItems)) {
                $message .= '. Warning: Some items were skipped (product not found): ' . implode(', ', $skippedItems);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'total' => $totalAmount,
                'sales_count' => count($sales),
                'sale_ids' => $sales,
                'skipped_items' => $skippedItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync sale: ' . $e->getMessage()
            ], 500);
        }
    }
}
