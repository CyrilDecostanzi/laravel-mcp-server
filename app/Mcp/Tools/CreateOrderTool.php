<?php

namespace App\Mcp\Tools;

use App\Services\Order\OrderCreationService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateOrderTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Create a new order for a customer. This tool validates stock availability, calculates totals including tax, and can optionally decrease product stock. Returns the complete order details with order number.
    MARKDOWN;

    public function __construct(
        private readonly OrderCreationService $orderService
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $data = [
            'customer_id' => $request->input('customer_id'),
            'items' => $request->input('items'),
            'status' => $request->input('status', 'pending'),
            'notes' => $request->input('notes'),
            'decrease_stock' => $request->input('decrease_stock', true),
        ];

        $result = $this->orderService->createOrder($data);

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'customer_id' => $schema->integer()
                ->description('Customer ID who is placing the order')
                ->required(),
            'items' => $schema->array()
                ->description('Array of order items')
                ->items(
                    $schema->object()
                        ->properties([
                            'product_id' => $schema->integer()->description('Product ID')->required(),
                            'quantity' => $schema->integer()->description('Quantity to order')->required(),
                            'unit_price' => $schema->number()->description('Unit price (optional, defaults to product price)'),
                        ])
                )
                ->required(),
            'status' => $schema->string()
                ->description('Order status')
                ->enum(['pending', 'processing', 'completed', 'cancelled', 'failed'])
                ->default('pending'),
            'notes' => $schema->string()
                ->description('Optional notes for the order'),
            'decrease_stock' => $schema->boolean()
                ->description('Whether to decrease product stock when creating order')
                ->default(true),
        ];
    }
}
