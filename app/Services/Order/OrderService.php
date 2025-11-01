<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Carbon;

class OrderService
{
    /**
     * Search orders with flexible filters.
     */
    public function searchOrders(array $filters): array
    {
        $query = Order::query()->with(['user', 'orderItems.product']);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['start_date']));
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['end_date']));
        }

        if (!empty($filters['min_amount'])) {
            $query->where('total', '>=', $filters['min_amount']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('user_id', $filters['customer_id']);
        }

        $limit = min($filters['limit'] ?? 50, 100);

        $orders = $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'total' => (float) $order->total,
                'created_at' => $order->created_at->toISOString(),
                'customer' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'items_count' => $order->orderItems->count(),
                'items' => $order->orderItems->map(fn($item) => [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'N/A',
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => round($item->quantity * (float) $item->price, 2),
                ])->toArray(),
            ]);

        return [
            'filters' => $filters,
            'total_results' => $orders->count(),
            'limit' => $limit,
            'orders' => $orders->toArray(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get order statistics.
     */
    public function getOrderStatistics(): array
    {
        $total = Order::count();
        $byStatus = Order::select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total_orders' => $total,
            'by_status' => $byStatus,
            'timestamp' => now()->toISOString(),
        ];
    }
}
