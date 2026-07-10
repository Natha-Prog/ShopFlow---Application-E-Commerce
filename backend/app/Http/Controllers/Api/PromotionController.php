<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $promo = Promotion::where('code', strtoupper($validated['code']))->first();

        if (! $promo || ! $promo->isValid($validated['subtotal'])) {
            return response()->json(['message' => 'Code promo invalide'], 422);
        }

        $discount = $promo->calculateDiscount($validated['subtotal']);

        return response()->json([
            'code' => $promo->code,
            'type' => $promo->type,
            'value' => (float) $promo->value,
            'discount' => $discount,
        ]);
    }
}
