<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function statsJson()
    {
        $userId = auth()->id();
        $stats = Cache::remember("dashboard_stats_{$userId}", 60, function () use ($userId) {
            return [
                'totalSales' => Sale::where('user_id', $userId)->sum('total'),
                'totalProducts' => Product::where('user_id', $userId)->count(),
                'totalItemsSold' => Sale::where('user_id', $userId)->sum('quantity'),
            ];
        });

        return response()->json($stats);
    }

    public function recentSalesJson()
    {
        $userId = auth()->id();
        $sales = Sale::with('product')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'product_name' => $sale->product->name ?? 'Deleted Product',
                    'quantity' => $sale->quantity,
                    'total' => number_format($sale->total, 2),
                    'date' => $sale->created_at->format('M d, Y h:i A'),
                ];
            });

        return response()->json(['sales' => $sales]);
    }

    public function lowStockJson()
    {
        $userId = auth()->id();
        $lowStock = Cache::remember("dashboard_low_stock_{$userId}", 60, function () use ($userId) {
            return Product::where('user_id', $userId)
                ->where('stock', '<=', 5)
                ->get();
        });

        return response()->json([
            'products' => $lowStock->map(fn($p) => [
                'name' => $p->name,
                'stock' => $p->stock,
            ]),
            'count' => $lowStock->count(),
        ]);
    }

    public function topProductsJson()
    {
        $userId = auth()->id();
        $topProducts = Cache::remember("dashboard_top_products_{$userId}", 300, function () use ($userId) {
            return Product::where('user_id', $userId)
                ->withSum('sales', 'quantity')
                ->orderBy('sales_sum_quantity', 'desc')
                ->take(5)
                ->get()
                ->map(fn($p) => [
                    'name' => $p->name,
                    'total_sold' => $p->sales_sum_quantity ?? 0,
                ]);
        });

        return response()->json(['products' => $topProducts]);
    }

    public function todayJson()
    {
        $userId = auth()->id();
        $todayStats = Cache::remember("dashboard_today_{$userId}", 60, function () use ($userId) {
            return [
                'todaySales' => Sale::where('user_id', $userId)->whereDate('created_at', today())->count(),
                'todayRevenue' => Sale::where('user_id', $userId)->whereDate('created_at', today())->sum('total'),
            ];
        });

        return response()->json($todayStats);
    }
}
