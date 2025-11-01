<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\CustomerInsightsService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetCustomerInsightsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get customer insights and analytics. Returns top customers by revenue, customer lifetime value, purchase frequency, and customer segments.
    MARKDOWN;

    public function __construct(
        private readonly CustomerInsightsService $customerInsightsService
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
                    'description' => 'Number of top customers to return (default: 20)',
                    'default' => 20,
                    'minimum' => 1,
                    'maximum' => 100,
                ],
            ],
        ]);
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $limit = min($request->params['limit'] ?? 20, 100);

        $result = $this->customerInsightsService->getCustomerInsights($limit);

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }
}
