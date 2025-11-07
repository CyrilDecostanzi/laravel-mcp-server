<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\ProductRecommendationService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetTrendingProductsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get trending products based on recent sales performance. Shows products with highest sales velocity in the specified period with units sold, order count, and revenue metrics.
    MARKDOWN;

    public function __construct(
        private readonly ProductRecommendationService $recommendationService
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $days = $request->input('days', 7);
        $limit = $request->input('limit', 10);

        $result = $this->recommendationService->getTrendingProducts($days, $limit);

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
            'days' => $schema->integer()
                ->description('Number of days to analyze for trends')
                ->minimum(1)
                ->default(7),
            'limit' => $schema->integer()
                ->description('Maximum number of trending products to return')
                ->default(10),
        ];
    }
}
