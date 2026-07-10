<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['items.product', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return response()->json(
            $query->orderByDesc('created_at')->get()->map->toApiArray()
        );
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);

        return response()->json($order->toApiArray());
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed,refunded',
        ]);

        $order->update($validated);

        return response()->json($order->fresh(['items.product', 'user'])->toApiArray());
    }

    public function export(): StreamedResponse
    {
        $orders = Order::with(['user', 'items'])->orderByDesc('created_at')->get();

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Client', 'Email', 'Statut', 'Total', 'Date']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->user?->name,
                    $order->user?->email,
                    $order->status,
                    $order->total,
                    $order->created_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'commandes.csv', ['Content-Type' => 'text/csv']);
    }
}
