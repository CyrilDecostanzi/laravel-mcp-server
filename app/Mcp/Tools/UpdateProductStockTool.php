<?php

namespace App\Mcp\Tools;

use App\Services\Inventory\InventoryManagementService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UpdateProductStockTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Update product stock levels. Supports three operations: set (replace stock), add (increase stock), subtract (decrease stock). Returns old and new stock levels with stock status.
    MARKDOWN;

    public function __construct(
        private readonly InventoryManagementService $inventoryService
    ) {}

    /**
     * Handle the tool request.
     *
     * @throws \JsonException
     */
    public function handle(Request $request): Response
    {
        $productId = $request->get('product_id');
        $quantity = $request->get('quantity');
        $operation = $request->get('operation', 'set');

        $result = $this->inventoryService->updateStock($productId, $quantity, $operation);

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
            'product_id' => $schema->integer()
                ->description('Product ID to update stock for')
                ->required(),
            'quantity' => $schema->integer()
                ->description('Quantity to set, add, or subtract')
                ->required(),
            'operation' => $schema->string()
                ->description('Stock operation: set (replace), add (increase), subtract (decrease)')
                ->enum(['set', 'add', 'subtract'])
                ->default('set'),
        ];
    }
}
