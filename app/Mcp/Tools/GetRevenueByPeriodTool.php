<?php

namespace App\Mcp\Tools;

use App\Models\Order;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Collection;
use JsonException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetRevenueByPeriodTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get revenue trends and statistics for a specific time period. Returns daily, weekly, or monthly revenue breakdowns with comparison to previous periods.
    MARKDOWN;

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'period' => $schema->string()
                ->description('Time period for revenue breakdown')
                ->enum(['daily', 'weekly', 'monthly'])
                ->default('monthly'),
            'limit' => $schema->integer()
                ->description('Number of periods to return (default: 12 for monthly, 8 for weekly, 30 for daily)'),
        ];
    }

    /**
     * Handle the tool request.
     * @throws JsonException
     */
    public function handle(Request $request): Response
    {
        $period = $request->get('period', 'monthly');
        $limit = $request->get('limit') ?? match ($period) {
            'daily' => 30,
            'weekly' => 8,
            'monthly' => 12,
        };

        $revenueData = match ($period) {
            'daily' => $this->getDailyRevenue($limit),
            'weekly' => $this->getWeeklyRevenue($limit),
            'monthly' => $this->getMonthlyRevenue($limit),
        };

        $data = [
            'period' => $period,
            'revenue_data' => $revenueData,
            'summary' => [
                'total_revenue' => round($revenueData->sum('revenue'), 2),
                'total_orders' => $revenueData->sum('orders'),
                'average_revenue_per_period' => round($revenueData->avg('revenue'), 2),
                'highest_revenue_period' => $revenueData->sortByDesc('revenue')->first(),
                'lowest_revenue_period' => $revenueData->sortBy('revenue')->first(),
            ],
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    /**
     * Get daily revenue breakdown.
     */
    private function getDailyRevenue(int $limit): Collection
    {
        return Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays($limit))
            ->groupBy('date')
            ->orderByDesc('date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'orders' => (int) $item->orders,
                'revenue' => round((float) $item->revenue, 2),
            ]);
    }

    /**
     * Get weekly revenue breakdown.
     */
    private function getWeeklyRevenue(int $limit)
    {
        return Order::selectRaw('YEARWEEK(created_at, 1) as week, MIN(DATE(created_at)) as start_date, MAX(DATE(created_at)) as end_date, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subWeeks($limit))
            ->groupBy('week')
            ->orderByDesc('week')
            ->get()
            ->map(fn ($item) => [
                'week' => $item->week,
                'period' => $item->start_date.' to '.$item->end_date,
                'orders' => (int) $item->orders,
                'revenue' => round((float) $item->revenue, 2),
            ]);
    }

    /**
     * Get monthly revenue breakdown.
     */
    private function getMonthlyRevenue(int $limit)
    {
        return Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subMonths($limit))
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(fn ($item) => [
                'period' => date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year)),
                'year' => (int) $item->year,
                'month' => (int) $item->month,
                'orders' => (int) $item->orders,
                'revenue' => round((float) $item->revenue, 2),
            ]);
    }
}
