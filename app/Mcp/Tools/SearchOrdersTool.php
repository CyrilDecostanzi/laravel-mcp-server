<?php

namespace App\Mcp\Tools;

use App\Models\Order;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchOrdersTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Search for orders with flexible filtering. Supports search by order number, customer email, status, date range, and amount range. Returns detailed order information including items and customer details.
    MARKDOWN;

    /**
     * Define the tool's input schema.
     */
    public function inputSchema(): JsonSchema
    {
        return new JsonSchema([
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search query (order number, customer email, or name)',
                ],
                'status' => [
                    'type' => 'string',
                    'description' => 'Filter by order status',
                    'enum' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled'],
                ],
                'date_from' => [
                    'type' => 'string',
                    'description' => 'Filter orders from this date (YYYY-MM-DD)',
                ],
                'date_to' => [
                    'type' => 'string',
                    'description' => 'Filter orders until this date (YYYY-MM-DD)',
                ],
                'min_amount' => [
                    'type' => 'number',
                    'description' => 'Minimum order total amount',
                ],
                'max_amount' => [
                    'type' => 'number',
                    'description' => 'Maximum order total amount',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of results (default: 20, max: 100)',
                    'default' => 20,
                    'minimum' => 1,
                    'maximum' => 100,
                ],
            ],
        ]);
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $params = $request->validate([
            'query' => 'string',
            'status' => 'string|in:pending,processing,shipped,delivered,cancelled',
            'date_from' => 'string|date',
            'date_to' => 'string|date',
            'min_amount' => 'numeric',
            'max_amount' => 'numeric',
            'limit' => 'integer|min:1|max:100',
        ]);
        
        $query = Order::with(['user', 'items.product']);
        
        // Text search
        if (!empty($params['query'])) {
            $searchTerm = $params['query'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('email', 'like', "%{$searchTerm}%")
                                ->orWhere('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Status filter
        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }
        
        // Date range filter
        if (!empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }
        if (!empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }
        
        // Amount range filter
        if (!empty($params['min_amount'])) {
            $query->where('total', '>=', $params['min_amount']);
        }
        if (!empty($params['max_amount'])) {
            $query->where('total', '<=', $params['max_amount']);
        }
        
        $limit = min($params['limit'] ?? 20, 100);
        
        $orders = $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                        'email' => $order->user->email,
                    ],
                    'status' => $order->status,
                    'amounts' => [
                        'subtotal' => (float) $order->subtotal,
                        'tax' => (float) $order->tax,
                        'shipping' => (float) $order->shipping,
                        'total' => (float) $order->total,
                    ],
                    'items_count' => $order->items->count(),
                    'items' => $order->items->map(fn($item) => [
                        'product_name' => $item->product_name,
                        'sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total' => (float) $item->total_price,
                    ]),
                    'shipping_address' => [
                        'line1' => $order->shipping_address_line1,
                        'line2' => $order->shipping_address_line2,
                        'city' => $order->shipping_city,
                        'state' => $order->shipping_state,
                        'postal_code' => $order->shipping_postal_code,
                        'country' => $order->shipping_country,
                    ],
                    'dates' => [
                        'created' => $order->created_at->toISOString(),
                        'shipped' => $order->shipped_at?->toISOString(),
                        'delivered' => $order->delivered_at?->toISOString(),
                    ],
                    'notes' => $order->notes,
                ];
            });

        $data = [
            'orders' => $orders,
            'count' => $orders->count(),
            'filters_applied' => array_filter($params ?? []),
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
