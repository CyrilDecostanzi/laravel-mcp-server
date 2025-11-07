<?php

namespace App\Mcp\Tools;

use App\Services\Inventory\InventoryManagementService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class CreateProductTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Create a new product in the catalog. Automatically generates SKU if not provided. Returns the complete product details including generated ID.
    MARKDOWN;

    public function __construct(
        private readonly InventoryManagementService $inventoryService
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'sku' => $request->input('sku'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock', 0),
            'is_active' => $request->input('is_active', true),
        ];

        $result = $this->inventoryService->createProduct($data);

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
            'name' => $schema->string()
                ->description('Product name')
                ->required(),
            'description' => $schema->string()
                ->description('Product description'),
            'sku' => $schema->string()
                ->description('Product SKU (auto-generated if not provided)'),
            'price' => $schema->number()
                ->description('Product price')
                ->minimum(0)
                ->required(),
            'stock' => $schema->integer()
                ->description('Initial stock quantity')
                ->minimum(0)
                ->default(0),
            'is_active' => $schema->boolean()
                ->description('Whether the product is active')
                ->default(true),
        ];
    }
}
