<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\RfmAnalysisService;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetRfmAnalysisTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Perform RFM (Recency, Frequency, Monetary) analysis to segment customers based on their purchase behavior. Returns customer segments like Champions, Loyal Customers, At Risk, etc. with actionable insights for each segment.
    MARKDOWN;

    public function __construct(
        private readonly RfmAnalysisService $rfmService
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $limit = $request->input('limit', 100);
        $includeInsights = $request->input('include_insights', true);

        $result = $this->rfmService->getRfmAnalysis($limit);

        if ($includeInsights) {
            $insights = $this->rfmService->getRfmInsights();
            $result['insights'] = $insights;
        }

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
            'limit' => $schema->integer()
                ->description('Maximum number of customers to analyze')
                ->default(100),
            'include_insights' => $schema->boolean()
                ->description('Include actionable insights for each segment')
                ->default(true),
        ];
    }
}
