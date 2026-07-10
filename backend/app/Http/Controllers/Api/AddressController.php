<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            Address::where('user_id', $request->user()->id)->orderByDesc('is_default')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateAddress($request);

        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address = Address::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($address, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        $validated = $this->validateAddress($request);

        if ($validated['is_default'] ?? false) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json($address);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        Address::where('user_id', $request->user()->id)->where('id', $id)->delete();

        return response()->json(['message' => 'Adresse supprimée']);
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);
    }
}
