<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\SalesAnalyticsService;
use Illuminate\JsonSchema\JsonSchema;
use JsonException;
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
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()
                ->description('Maximum number of products to return (default: 10, max: 50)')
                ->default(10),
            'sort_by' => $schema->string()
                ->description('Sort by quantity or revenue')
                ->enum(['quantity', 'revenue'])
                ->default('revenue'),
        ];
    }

    /**
     * Handle the tool request.
     *
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $limit = min($request->get('limit', 10), 50);
        $sortBy = $request->get('sort_by', 'revenue');

        $result = $this->salesAnalyticsService->getTopProducts($limit, $sortBy);

        return Response::text(json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }
}
