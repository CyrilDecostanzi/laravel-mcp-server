<?php

namespace App\Mcp\Tools;

use App\Services\Inventory\InventoryManagementService;
use Illuminate\JsonSchema\JsonSchema;
use JsonException;
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
     *
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $data = [
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'sku' => $request->get('sku'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock', 0),
            'is_active' => $request->get('is_active', true),
        ];

        $result = $this->inventoryService->createProduct($data);

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
            'name' => $schema->string()
                ->description('Product name')
                ->required(),
            'description' => $schema->string()
                ->description('Product description'),
            'sku' => $schema->string()
                ->description('Product SKU (auto-generated if not provided)'),
            'price' => $schema->number()
                ->description('Product price')
                ->required(),
            'stock' => $schema->integer()
                ->description('Initial stock quantity')
                ->default(0),
            'is_active' => $schema->boolean()
                ->description('Whether the product is active')
                ->default(true),
        ];
    }
}
