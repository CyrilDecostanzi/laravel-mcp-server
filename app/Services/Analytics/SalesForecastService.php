<?php

namespace App\Services\Analytics;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SalesForecastService
{
    /**
     * Forecast sales for the next period using moving average.
     */
    public function forecastSales(string $period = 'daily', int $forecastDays = 7): array
    {
        // Get historical data
        $historicalData = $this->getHistoricalData($period, 90);

        if (empty($historicalData)) {
            return [
                'error' => 'Not enough historical data for forecasting',
                'timestamp' => now()->toISOString(),
            ];
        }

        // Calculate trend using simple moving average
        $forecast = $this->calculateForecast($historicalData, $forecastDays, $period);

        // Calculate confidence metrics
        $accuracy = $this->calculateAccuracy($historicalData);

        return [
            'forecast_period' => $period,
            'forecast_days' => $forecastDays,
            'historical_data_points' => count($historicalData),
            'forecast' => $forecast,
            'accuracy_metrics' => $accuracy,
            'method' => 'Simple Moving Average with Trend Analysis',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get advanced sales insights and predictions.
     */
    public function getSalesInsights(): array
    {
        // Current period stats
        $thisMonth = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as orders, SUM(total) as revenue')
            ->first();

        $lastMonth = Order::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as orders, SUM(total) as revenue')
            ->first();

        // Growth rates
        $revenueGrowth = $lastMonth->revenue > 0
            ? (($thisMonth->revenue - $lastMonth->revenue) / $lastMonth->revenue) * 100
            : 0;

        $orderGrowth = $lastMonth->orders > 0
            ? (($thisMonth->orders - $lastMonth->orders) / $lastMonth->orders) * 100
            : 0;

        // Trend analysis
        $trend = $this->analyzeTrend();

        // Seasonal patterns
        $seasonality = $this->detectSeasonality();

        return [
            'current_month' => [
                'orders' => (int) $thisMonth->orders,
                'revenue' => round((float) $thisMonth->revenue, 2),
            ],
            'previous_month' => [
                'orders' => (int) $lastMonth->orders,
                'revenue' => round((float) $lastMonth->revenue, 2),
            ],
            'growth' => [
                'revenue_growth_percentage' => round($revenueGrowth, 2),
                'order_growth_percentage' => round($orderGrowth, 2),
                'trend' => $revenueGrowth > 0 ? 'Growing' : ($revenueGrowth < 0 ? 'Declining' : 'Stable'),
            ],
            'trend_analysis' => $trend,
            'seasonality' => $seasonality,
            'predictions' => [
                'next_month_revenue_estimate' => round($thisMonth->revenue * (1 + ($revenueGrowth / 100)), 2),
                'confidence' => $this->getConfidenceLevel($revenueGrowth),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get historical sales data.
     */
    private function getHistoricalData(string $period, int $days): array
    {
        $groupFormat = match ($period) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return Order::select(
            DB::raw("DATE_FORMAT(created_at, '{$groupFormat}') as period"),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get()
            ->map(fn ($item) => [
                'period' => $item->period,
                'revenue' => (float) $item->revenue,
                'orders' => (int) $item->orders,
            ])
            ->toArray();
    }

    /**
     * Calculate forecast using moving average.
     */
    private function calculateForecast(array $historicalData, int $forecastDays, string $period): array
    {
        $revenues = array_column($historicalData, 'revenue');
        $orders = array_column($historicalData, 'orders');

        // Calculate moving average
        $windowSize = min(7, count($revenues));
        $recentRevenues = array_slice($revenues, -$windowSize);
        $recentOrders = array_slice($orders, -$windowSize);

        $avgRevenue = array_sum($recentRevenues) / count($recentRevenues);
        $avgOrders = array_sum($recentOrders) / count($recentOrders);

        // Calculate trend
        $trend = $this->calculateTrend($revenues);

        $forecasts = [];
        for ($i = 1; $i <= $forecastDays; $i++) {
            $forecastRevenue = $avgRevenue * (1 + ($trend * $i * 0.01));
            $forecastOrders = $avgOrders * (1 + ($trend * $i * 0.01));

            $forecasts[] = [
                'day' => $i,
                'period' => $this->getFuturePeriod($i, $period),
                'predicted_revenue' => round($forecastRevenue, 2),
                'predicted_orders' => (int) round($forecastOrders),
                'confidence' => max(50, 95 - ($i * 5)), // Confidence decreases over time
            ];
        }

        return $forecasts;
    }

    /**
     * Calculate trend percentage.
     */
    private function calculateTrend(array $values): float
    {
        if (count($values) < 2) {
            return 0;
        }

        $first = array_slice($values, 0, (int) (count($values) / 2));
        $second = array_slice($values, (int) (count($values) / 2));

        $avgFirst = array_sum($first) / count($first);
        $avgSecond = array_sum($second) / count($second);

        return $avgFirst > 0 ? (($avgSecond - $avgFirst) / $avgFirst) * 100 : 0;
    }

    /**
     * Analyze overall trend.
     */
    private function analyzeTrend(): array
    {
        $last30Days = $this->getHistoricalData('daily', 30);
        $revenues = array_column($last30Days, 'revenue');

        if (empty($revenues)) {
            return ['status' => 'No data', 'direction' => 'unknown'];
        }

        $trend = $this->calculateTrend($revenues);

        return [
            'percentage' => round($trend, 2),
            'direction' => $trend > 5 ? 'Strong Upward' : ($trend > 0 ? 'Slight Upward' : ($trend < -5 ? 'Strong Downward' : 'Stable')),
            'recommendation' => $trend < -5 ? 'Consider promotional campaigns' : ($trend > 10 ? 'Maintain momentum, increase inventory' : 'Monitor closely'),
        ];
    }

    /**
     * Detect seasonal patterns.
     */
    private function detectSeasonality(): array
    {
        $monthlyData = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as revenue')
        )
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();

        if (empty($monthlyData)) {
            return ['detected' => false];
        }

        $avgRevenue = array_sum($monthlyData) / count($monthlyData);
        $maxMonth = array_search(max($monthlyData), $monthlyData);
        $minMonth = array_search(min($monthlyData), $monthlyData);

        return [
            'detected' => true,
            'peak_month' => $maxMonth,
            'lowest_month' => $minMonth,
            'variance' => round((max($monthlyData) - min($monthlyData)) / $avgRevenue * 100, 2).'%',
        ];
    }

    /**
     * Calculate accuracy metrics.
     */
    private function calculateAccuracy(array $historicalData): array
    {
        if (count($historicalData) < 10) {
            return ['note' => 'Not enough data for accurate metrics'];
        }

        $revenues = array_column($historicalData, 'revenue');
        $mean = array_sum($revenues) / count($revenues);

        // Calculate standard deviation
        $variance = 0;
        foreach ($revenues as $revenue) {
            $variance += pow($revenue - $mean, 2);
        }
        $stdDev = sqrt($variance / count($revenues));

        return [
            'mean_revenue' => round($mean, 2),
            'standard_deviation' => round($stdDev, 2),
            'coefficient_of_variation' => round(($stdDev / $mean) * 100, 2).'%',
            'confidence_level' => 'Medium',
        ];
    }

    /**
     * Get confidence level based on growth stability.
     */
    private function getConfidenceLevel(float $growthRate): string
    {
        $abs = abs($growthRate);
        if ($abs <= 10) {
            return 'High (stable growth)';
        } elseif ($abs <= 30) {
            return 'Medium (moderate volatility)';
        }

        return 'Low (high volatility)';
    }

    /**
     * Get future period label.
     */
    private function getFuturePeriod(int $days, string $period): string
    {
        return match ($period) {
            'daily' => now()->addDays($days)->format('Y-m-d'),
            'weekly' => now()->addWeeks($days)->format('Y-W'),
            'monthly' => now()->addMonths($days)->format('Y-m'),
            default => now()->addDays($days)->format('Y-m-d'),
        };
    }
}
