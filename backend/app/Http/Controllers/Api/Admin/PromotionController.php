<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Promotion::orderByDesc('created_at')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePromotion($request);
        $validated['code'] = strtoupper($validated['code']);

        $promo = Promotion::create($validated);

        return response()->json($promo, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $promo = Promotion::findOrFail($id);
        $validated = $this->validatePromotion($request, $promo->id);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $promo->update($validated);

        return response()->json($promo);
    }

    public function destroy(int $id): JsonResponse
    {
        Promotion::findOrFail($id)->delete();

        return response()->json(['message' => 'Promotion supprimée']);
    }

    private function validatePromotion(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'code' => 'required|string|unique:promotions,code'.($id ? ",$id" : ''),
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);
    }
}
