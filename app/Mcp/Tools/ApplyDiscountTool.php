<?php

namespace App\Mcp\Tools;

use App\Services\Inventory\InventoryManagementService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ApplyDiscountTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Apply a percentage discount to a product's price. The discount is immediately applied to the product. Returns original price, discount amount, and new price with savings calculation.
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
        $discountPercentage = $request->get('discount_percentage');

        $result = $this->inventoryService->applyDiscount($productId, $discountPercentage);

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
                ->description('Product ID to apply discount to')
                ->required(),
            'discount_percentage' => $schema->number()
                ->description('Discount percentage (0-100)')
                ->required(),
        ];
    }
}
