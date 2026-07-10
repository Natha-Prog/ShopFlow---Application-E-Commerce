<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(fn ($f) => $f->product?->toApiArray())
            ->filter();

        return response()->json($favorites->values());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json(['message' => 'Ajouté aux favoris'], 201);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Retiré des favoris']);
    }
}
