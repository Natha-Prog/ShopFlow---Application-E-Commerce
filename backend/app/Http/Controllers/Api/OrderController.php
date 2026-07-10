<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promotion;
use App\Services\NullPaymentService;
use App\Services\PaymentConfigService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(
        private ShippingService $shippingService,
        private PaymentConfigService $paymentConfig,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map->toApiArray();

        return response()->json($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with(['items.product', 'user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order->toApiArray());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.first_name' => 'required|string',
            'shipping_address.last_name' => 'required|string',
            'shipping_address.street' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.country' => 'required|string',
            'shipping_address.phone' => 'nullable|string',
            'shipping_address.latitude' => 'required_if:shipping_method,delivery|nullable|numeric|between:-90,90',
            'shipping_address.longitude' => 'required_if:shipping_method,delivery|nullable|numeric|between:-180,180',
            'shipping_address.region' => 'required_if:shipping_method,shipping|nullable|string',
            'shipping_address.distance_km' => 'nullable|numeric|min:0',
            'payment_method' => ['required', 'string', Rule::in($this->paymentConfig->getAllowedIds())],
            'payment_phone' => 'nullable|string',
            'shipping_method' => 'required|in:pickup,delivery,shipping',
            'promo_code' => 'nullable|string',
        ]);

        if (! $this->paymentConfig->isValid($validated['payment_method'])) {
            return response()->json(['message' => 'Mode de paiement invalide ou indisponible'], 422);
        }

        if ($this->paymentConfig->requiresPhone($validated['payment_method'])) {
            $paymentPhone = $validated['payment_phone'] ?? $validated['shipping_address']['phone'] ?? '';
            if ($paymentPhone === '') {
                return response()->json(['message' => 'Numéro Mobile Money requis pour ce mode de paiement'], 422);
            }
            if (! $this->isValidMadagascarPhone($paymentPhone)) {
                return response()->json(['message' => 'Numéro de téléphone mobile invalide (format 03X XX XXX XX)'], 422);
            }
            $validated['shipping_address']['payment_phone'] = $paymentPhone;
        }

        $cartItems = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Panier vide'], 422);
        }

        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return response()->json([
                    'message' => "Stock insuffisant pour {$item->product->name}",
                ], 422);
            }
        }

        try {
            $shippingQuote = $this->shippingService->quoteForOrder(
                $validated['shipping_method'],
                $validated['shipping_address'],
            );
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $shipping = (float) $shippingQuote['fee'];
        $shippingAddress = $validated['shipping_address'];

        if ($validated['shipping_method'] === 'delivery') {
            $shippingAddress['distance_km'] = $shippingQuote['distance_km'];
            $shippingAddress['region'] = $shippingQuote['region'];
        }

        if ($validated['shipping_method'] === 'shipping') {
            $shippingAddress['region'] = $shippingQuote['region'];
        }

        $subtotal = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);
        $discount = 0;
        $promoCode = null;

        if (! empty($validated['promo_code'])) {
            $promo = Promotion::where('code', strtoupper($validated['promo_code']))->first();
            if ($promo && $promo->isValid($subtotal)) {
                $discount = $promo->calculateDiscount($subtotal);
                $promoCode = $promo->code;
            }
        }

        $total = max(0, $subtotal + $shipping - $discount);

        $paymentService = new NullPaymentService;
        $paymentResult = $paymentService->createPaymentIntent($total, 'MGA', [
            'user_id' => $request->user()->id,
        ]);

        $order = DB::transaction(function () use (
            $request, $cartItems, $subtotal, $shipping, $discount, $total,
            $validated, $promoCode, $paymentResult, $shippingAddress
        ) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'discount' => $discount,
                'total' => $total,
                'shipping_address' => $shippingAddress,
                'payment_method' => $validated['payment_method'],
                'shipping_method' => $validated['shipping_method'],
                'payment_status' => 'pending',
                'payment_intent_id' => $paymentResult['id'],
                'promo_code' => $promoCode,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            if ($promoCode) {
                Promotion::where('code', $promoCode)->increment('used_count');
            }

            CartItem::where('user_id', $request->user()->id)->delete();

            return $order;
        });

        return response()->json($order->fresh(['items.product'])->toApiArray(), 201);
    }

    private function isValidMadagascarPhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        return (bool) preg_match('/^0(3[2-8])\d{7}$/', $digits);
    }
}
