<?php

namespace App\Mcp\Tools;

use App\Services\Inventory\InventoryService;
use JsonException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetInventoryAlertsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get inventory alerts for products that need attention. Returns products with low stock, out of stock, and inactive products with remaining inventory.
    MARKDOWN;

    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    /**
     * Handle the tool request.
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $result = $this->inventoryService->getInventoryAlerts();

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
