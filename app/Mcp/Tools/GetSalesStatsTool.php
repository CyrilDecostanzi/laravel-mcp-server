<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\SalesAnalyticsService;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetSalesStatsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get comprehensive sales statistics and dashboard metrics. Returns total revenue, order counts, average order value, payment statistics, and trends over different time periods.
    MARKDOWN;

    public function __construct(
        private readonly SalesAnalyticsService $salesAnalyticsService
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $result = $this->salesAnalyticsService->getSalesStats();

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }
}
