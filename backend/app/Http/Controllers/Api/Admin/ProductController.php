<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('stock_filter')) {
            match ($request->stock_filter) {
                'low' => $query->where('stock', '<=', 5)->where('stock', '>', 0),
                'out' => $query->where('stock', 0),
                'in' => $query->where('stock', '>', 5),
                default => null,
            };
        }

        return response()->json($query->orderBy('name')->get()->map->toApiArray());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);
        $validated['attributes'] = [
            'size' => $request->size,
            'color' => $request->color,
            'brand' => $request->brand,
        ];
        $validated['image'] = $request->image_url ?? $request->image;

        $product = Product::create($validated);

        return response()->json($product->load('category')->toApiArray(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $validated = $this->validateProduct($request, $product->id);

        if ($request->has('size') || $request->has('color') || $request->has('brand')) {
            $validated['attributes'] = [
                'size' => $request->size ?? ($product->getAttribute('attributes')['size'] ?? null),
                'color' => $request->color ?? ($product->getAttribute('attributes')['color'] ?? null),
                'brand' => $request->brand ?? ($product->getAttribute('attributes')['brand'] ?? null),
            ];
        }

        if ($request->filled('image_url') || $request->filled('image')) {
            $validated['image'] = $request->image_url ?? $request->image;
        }

        $product->update($validated);

        return response()->json($product->fresh('category')->toApiArray());
    }

    public function destroy(int $id): JsonResponse
    {
        Product::findOrFail($id)->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate(['image' => 'required|image|max:2048']);

        $path = $request->file('image')->store('products', 'public');

        return response()->json(['url' => '/storage/'.$path]);
    }

    private function validateProduct(Request $request, ?int $id = null): array
    {
        $rules = [
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ];

        // If only updating stock, make other fields optional
        if ($request->has('stock') && !$request->has('name') && !$request->has('price') && !$request->has('category_id')) {
            $rules = [
                'stock' => 'required|integer|min:0',
            ];
        }

        return $request->validate($rules);
    }
}
