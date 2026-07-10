<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->orderBy('name')->get()->map->toApiArray();

        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'reviews.user'])->findOrFail($id);

        $data = $product->toApiArray();
        $data['reviews'] = $product->reviews->map(fn ($r) => [
            'id' => $r->id,
            'rating' => $r->rating,
            'comment' => $r->comment,
            'created_at' => $r->created_at?->toISOString(),
            'user' => [
                'id' => $r->user->id,
                'name' => $r->user->name,
            ],
        ]);
        $data['average_rating'] = $product->reviews->avg('rating') ?? 0;

        return response()->json($data);
    }
}
