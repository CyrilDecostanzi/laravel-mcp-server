<?php

namespace App\Mcp\Tools;

use App\Services\Order\OrderCreationService;
use Illuminate\JsonSchema\JsonSchema;
use JsonException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UpdateOrderStatusTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Update the status of an existing order. Valid statuses are: pending, processing, completed, cancelled, failed. Returns the old and new status.
    MARKDOWN;

    public function __construct(
        private readonly OrderCreationService $orderService
    ) {}

    /**
     * Handle the tool request.
     *
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $orderId = $request->get('order_id');
        $status = $request->get('status');

        $result = $this->orderService->updateOrderStatus($orderId, $status);

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'order_id' => $schema->integer()
                ->description('Order ID to update')
                ->required(),
            'status' => $schema->string()
                ->description('New order status')
                ->enum(['pending', 'processing', 'completed', 'cancelled', 'failed'])
                ->required(),
        ];
    }
}
