<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private function canUseSupabase(): bool
    {
        return (bool) (env('SUPABASE_URL') && env('SUPABASE_STORAGE_KEY') && env('SUPABASE_STORAGE_SECRET'));
    }

    public function index(Request $request)
    {
        return view('products.index');
    }

    public function indexJson(Request $request)
    {
        $query = Product::where('user_id', Auth::id());

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20);

        return response()->json([
            'products' => $products->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'stock' => $p->stock,
                'image' => $p->image,
                'image_url' => $p->image_url,
            ]),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
                'has_pages' => $products->hasPages(),
            ],
        ]);
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $data = $request->only(['name', 'price', 'stock']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            if ($this->canUseSupabase()) {
                $file = $request->file('image');
                $filename = 'products/' . uniqid() . '.' . $file->getClientOriginalExtension();
                Storage::disk('supabase')->put($filename, file_get_contents($file->getRealPath()));
                $data['image'] = $filename;
            } else {
                $file = $request->file('image');
                $filename = 'products/' . uniqid() . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
                $data['image'] = $filename;
            }
        }

        Product::create($data);
        // Clear dashboard cache for this user
        $userId = Auth::id();
        \Illuminate\Support\Facades\Cache::forget("dashboard_stats_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("dashboard_low_stock_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("low_stock_{$userId}_" . (Auth::user()->default_stock_threshold ?? 5));
        return redirect()->route('products.index', ['_fresh' => 1])->with('success', 'Product added!');
    }

    public function edit(Product $product)
    {
        // Check if product belongs to current user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Check if product belongs to current user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $data = $request->only(['name', 'price', 'stock']);

        if ($request->hasFile('image')) {
            if ($this->canUseSupabase()) {
                try {
                    if ($product->image && Storage::disk('supabase')->exists($product->image)) {
                        Storage::disk('supabase')->delete($product->image);
                    }
                } catch (\Throwable $e) {
                }
                $file = $request->file('image');
                $filename = 'products/' . uniqid() . '.' . $file->getClientOriginalExtension();
                Storage::disk('supabase')->put($filename, file_get_contents($file->getRealPath()));
                $data['image'] = $filename;
            } else {
                try {
                    if ($product->image && Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }
                } catch (\Throwable $e) {
                }
                $file = $request->file('image');
                $filename = 'products/' . uniqid() . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
                $data['image'] = $filename;
            }
        }

        $product->update($data);
        // Clear dashboard cache for this user
        $userId = Auth::id();
        \Illuminate\Support\Facades\Cache::forget("dashboard_low_stock_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("low_stock_{$userId}_" . (Auth::user()->default_stock_threshold ?? 5));
        return redirect()->route('products.index', ['_fresh' => 1])->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        // Check if product belongs to current user
        if ($product->user_id !== Auth::id()) {
            abort(403);
        }

        if ($product->image) {
            if ($this->canUseSupabase()) {
                try {
                    if (Storage::disk('supabase')->exists($product->image)) {
                        Storage::disk('supabase')->delete($product->image);
                    }
                } catch (\Throwable $e) {
                }
            } else {
                try {
                    if (Storage::disk('public')->exists($product->image)) {
                        Storage::disk('public')->delete($product->image);
                    }
                } catch (\Throwable $e) {
                }
            }
        }

        $product->delete();
        // Clear dashboard cache for this user
        $userId = Auth::id();
        \Illuminate\Support\Facades\Cache::forget("dashboard_stats_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("dashboard_low_stock_{$userId}");
        \Illuminate\Support\Facades\Cache::forget("low_stock_{$userId}_" . (Auth::user()->default_stock_threshold ?? 5));
        return redirect()->route('products.index', ['_fresh' => 1])->with('success', 'Product deleted!');
    }

    public function syncOfflineProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0'
            ]);

            Product::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'image' => null // No image from offline creation
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product synced successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
