<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\SalesAnalyticsService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetTopProductsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get top-performing products based on sales metrics. Returns best-selling products by quantity sold and revenue generated, with optional limit parameter.
    MARKDOWN;

    public function __construct(
        private readonly SalesAnalyticsService $salesAnalyticsService
    ) {}

    /**
     * Define the tool's input schema.
     */
    public function inputSchema(): JsonSchema
    {
        return new JsonSchema([
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of products to return (default: 10, max: 50)',
                    'default' => 10,
                    'minimum' => 1,
                    'maximum' => 50,
                ],
                'sort_by' => [
                    'type' => 'string',
                    'description' => 'Sort by quantity or revenue',
                    'enum' => ['quantity', 'revenue'],
                    'default' => 'revenue',
                ],
            ],
        ]);
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $limit = min($request->params['limit'] ?? 10, 50);
        $sortBy = $request->params['sort_by'] ?? 'revenue';

        $result = $this->salesAnalyticsService->getTopProducts($limit, $sortBy);

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }
}
