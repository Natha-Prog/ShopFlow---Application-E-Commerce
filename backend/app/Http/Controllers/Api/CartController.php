<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map->toApiArray();

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Stock insuffisant'], 422);
        }

        $item = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        $item->quantity = ($item->exists ? $item->quantity : 0) + $validated['quantity'];
        $item->save();
        $item->load('product');

        return response()->json($item->toApiArray(), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($item->product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Stock insuffisant'], 422);
        }

        $item->update(['quantity' => $validated['quantity']]);

        return response()->json($item->fresh('product')->toApiArray());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->where('id', $id)->delete();

        return response()->json(['message' => 'Article supprimé']);
    }
}
