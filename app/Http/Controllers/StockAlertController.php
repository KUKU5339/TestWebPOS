<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class StockAlertController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $threshold = $user->default_stock_threshold;

        // Single query for all alert-worthy products, partitioned in PHP
        $allAlertProducts = Product::where('user_id', $user->id)
            ->where('stock', '<=', $threshold + 2)
            ->orderBy('stock', 'asc')
            ->get();

        $outOfStock = $allAlertProducts->where('stock', 0)->values();
        $lowStockProducts = $allAlertProducts->where('stock', '<=', $threshold)->values();
        $approachingLowStock = $allAlertProducts->where('stock', '>', $threshold)->values();

        return view('stock-alerts.index', compact(
            'lowStockProducts',
            'outOfStock',
            'approachingLowStock',
            'threshold'
        ));
    }

    public function updateThreshold(Request $request)
    {
        $request->validate([
            'threshold' => 'required|integer|min:1|max:100'
        ]);

        $user = auth()->user();
        $user->update([
            'default_stock_threshold' => $request->threshold
        ]);

        return back()->with('success', 'Stock alert threshold updated!');
    }

    public function generateShoppingList()
    {
        $user = auth()->user();
        $threshold = $user->default_stock_threshold;

        $lowStockProducts = Product::where('user_id', $user->id)
            ->where('stock', '<=', $threshold)
            ->orderBy('stock', 'asc')
            ->get();

        $pdf = Pdf::loadView('stock-alerts.shopping-list', compact('lowStockProducts', 'user'));
        return $pdf->download('shopping-list-' . date('Y-m-d') . '.pdf');
    }

    public function lowStockJson()
    {
        $user = auth()->user();
        $threshold = $user->default_stock_threshold ?? 5;
        $cacheKey = "low_stock_{$user->id}_{$threshold}";

        $lowStockProducts = Cache::remember($cacheKey, 300, function () use ($user, $threshold) {
            return Product::where('user_id', $user->id)
                ->where('stock', '<=', $threshold)
                ->get(['id', 'name', 'stock']);
        });

        return response()->json([
            'count' => $lowStockProducts->count(),
            'products' => $lowStockProducts->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'stock' => $p->stock,
            ]),
        ]);
    }

    public function toggleAlerts(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'enable_stock_alerts' => !$user->enable_stock_alerts
        ]);

        $status = $user->enable_stock_alerts ? 'enabled' : 'disabled';
        return back()->with('success', "Stock alerts $status!");
    }
}
