<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $days = (int) $request->get('days', 30);
        $since = Carbon::now()->subDays($days);

        $orders = Order::where('created_at', '>=', $since)->get();
        $allOrders = Order::all();

        $salesByDay = Order::where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as date, SUM(total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $statusBreakdown = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $topProducts = Product::withCount(['orderItems as sales_count' => function ($q) use ($since) {
            $q->whereHas('order', fn ($oq) => $oq->where('created_at', '>=', $since));
        }])
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'sales' => $p->sales_count,
                'revenue' => (float) $p->price * $p->sales_count,
            ]);

        $lowStock = Product::where('stock', '<=', 5)->orderBy('stock')->limit(10)->get()
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'stock' => $p->stock]);

        return response()->json([
            'total_sales' => (float) $orders->sum('total'),
            'total_orders' => $orders->count(),
            'total_products' => Product::count(),
            'total_users' => User::where('role', 'client')->count(),
            'pending_orders' => $allOrders->where('status', 'pending')->count(),
            'completed_orders' => $allOrders->where('status', 'delivered')->count(),
            'sales_by_day' => $salesByDay,
            'status_breakdown' => $statusBreakdown,
            'top_products' => $topProducts,
            'low_stock' => $lowStock,
            'recent_orders' => Order::with('user')->orderByDesc('created_at')->limit(5)->get()
                ->map->toApiArray(),
        ]);
    }
}
