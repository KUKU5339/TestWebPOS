<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = ['user_id', 'name', 'price', 'stock', 'image'];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        $supabaseUrl = env('SUPABASE_URL');
        $bucket = env('SUPABASE_STORAGE_BUCKET', 'products');

        $path = ltrim($this->image, '/');

        if ($supabaseUrl) {
            if (!str_starts_with($path, $bucket . '/')) {
                $path = $bucket . '/' . $path;
            }
            return rtrim($supabaseUrl, '/') . '/storage/v1/object/public/' . $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
