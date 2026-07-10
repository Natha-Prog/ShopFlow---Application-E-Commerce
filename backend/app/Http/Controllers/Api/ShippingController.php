<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ShippingController extends Controller
{
    public function __construct(private ShippingService $shippingService) {}

    public function regions(): JsonResponse
    {
        return response()->json($this->shippingService->getRegions());
    }

    public function quote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_method' => 'required|in:pickup,delivery,shipping',
            'latitude' => 'required_if:shipping_method,delivery|numeric|between:-90,90',
            'longitude' => 'required_if:shipping_method,delivery|numeric|between:-180,180',
            'region' => 'required_if:shipping_method,shipping|string',
        ]);

        try {
            $quote = $this->shippingService->quote($validated['shipping_method'], $validated);

            return response()->json($quote);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
