<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\RfmAnalysisService;
use Illuminate\JsonSchema\JsonSchema;
use JsonException;
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
     *
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $limit = $request->get('limit', 100);
        $includeInsights = $request->get('include_insights', true);

        $result = $this->rfmService->getRfmAnalysis($limit);

        if ($includeInsights) {
            $insights = $this->rfmService->getRfmInsights();
            $result['insights'] = $insights;
        }

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
            'limit' => $schema->integer()
                ->description('Maximum number of customers to analyze')
                ->default(100),
            'include_insights' => $schema->boolean()
                ->description('Include actionable insights for each segment')
                ->default(true),
        ];
    }
}
