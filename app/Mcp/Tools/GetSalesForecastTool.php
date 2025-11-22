<?php

namespace App\Mcp\Tools;

use App\Services\Analytics\SalesForecastService;
use Illuminate\JsonSchema\JsonSchema;
use JsonException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetSalesForecastTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Forecast future sales using historical data and trend analysis. Provides predictions for revenue and order volume with confidence metrics. Can forecast by daily, weekly, or monthly periods.
    MARKDOWN;

    public function __construct(
        private readonly SalesForecastService $forecastService
    ) {}

    /**
     * Handle the tool request.
     *
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $period = $request->get('period', 'daily');
        $forecastDays = $request->get('forecast_days', 7);
        $includeInsights = $request->get('include_insights', true);

        $result = $this->forecastService->forecastSales($period, $forecastDays);

        if ($includeInsights) {
            $insights = $this->forecastService->getSalesInsights();
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
            'period' => $schema->string()
                ->description('Forecast period: daily, weekly, or monthly')
                ->enum(['daily', 'weekly', 'monthly'])
                ->default('daily'),
            'forecast_days' => $schema->integer()
                ->description('Number of periods to forecast')
                ->default(7),
            'include_insights' => $schema->boolean()
                ->description('Include trend analysis and insights')
                ->default(true),
        ];
    }
}
