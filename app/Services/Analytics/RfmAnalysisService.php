<?php

namespace App\Services\Analytics;

use App\Models\User;

class RfmAnalysisService
{
    /**
     * Perform RFM (Recency, Frequency, Monetary) analysis on customers.
     * This segments customers based on their purchase behavior.
     */
    public function getRfmAnalysis(int $limit = 100): array
    {
        // Get customer RFM scores
        $customers = User::select('users.id', 'users.name', 'users.email')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'completed')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->selectRaw('MAX(orders.created_at) as last_order_date')
            ->selectRaw('COUNT(orders.id) as frequency')
            ->selectRaw('SUM(orders.total) as monetary')
            ->having('frequency', '>', 0)
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                $recencyDays = now()->diffInDays($customer->last_order_date);

                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'recency_days' => $recencyDays,
                    'frequency' => (int) $customer->frequency,
                    'monetary' => round((float) $customer->monetary, 2),
                    'last_order_date' => $customer->last_order_date,
                ];
            });

        // Calculate quartiles for scoring
        $recencyValues = $customers->pluck('recency_days')->sort()->values();
        $frequencyValues = $customers->pluck('frequency')->sort()->values();
        $monetaryValues = $customers->pluck('monetary')->sort()->values();

        $recencyQ = $this->calculateQuartiles($recencyValues);
        $frequencyQ = $this->calculateQuartiles($frequencyValues);
        $monetaryQ = $this->calculateQuartiles($monetaryValues);

        // Assign RFM scores
        $scoredCustomers = $customers->map(function ($customer) use ($recencyQ, $frequencyQ, $monetaryQ) {
            // Recency: lower is better, so we reverse the score
            $rScore = $this->getScore($customer['recency_days'], $recencyQ, true);
            $fScore = $this->getScore($customer['frequency'], $frequencyQ);
            $mScore = $this->getScore($customer['monetary'], $monetaryQ);

            $rfmScore = ($rScore * 100) + ($fScore * 10) + $mScore;
            $segment = $this->getRfmSegment($rScore, $fScore, $mScore);

            return array_merge($customer, [
                'r_score' => $rScore,
                'f_score' => $fScore,
                'm_score' => $mScore,
                'rfm_score' => $rfmScore,
                'segment' => $segment,
            ]);
        })->sortByDesc('rfm_score')->values();

        // Segment distribution
        $segmentStats = $scoredCustomers->groupBy('segment')->map(function ($group, $segment) {
            return [
                'count' => $group->count(),
                'total_value' => round($group->sum('monetary'), 2),
                'avg_frequency' => round($group->avg('frequency'), 2),
                'avg_recency_days' => round($group->avg('recency_days'), 1),
            ];
        });

        return [
            'customers' => $scoredCustomers->toArray(),
            'segment_distribution' => $segmentStats->toArray(),
            'total_customers_analyzed' => $scoredCustomers->count(),
            'scoring_method' => 'Quartile-based (1-5 scale)',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get actionable insights from RFM analysis.
     */
    public function getRfmInsights(): array
    {
        $analysis = $this->getRfmAnalysis(500);
        $customers = collect($analysis['customers']);

        return [
            'champions' => [
                'count' => $customers->where('segment', 'Champions')->count(),
                'description' => 'Best customers - bought recently, buy often, and spend the most',
                'action' => 'Reward them, ask for reviews, upsell premium products',
            ],
            'loyal_customers' => [
                'count' => $customers->where('segment', 'Loyal Customers')->count(),
                'description' => 'Spend good money with us often',
                'action' => 'Recommend related products, engage them',
            ],
            'potential_loyalists' => [
                'count' => $customers->where('segment', 'Potential Loyalists')->count(),
                'description' => 'Recent customers with average frequency',
                'action' => 'Offer membership, recommend products',
            ],
            'at_risk' => [
                'count' => $customers->where('segment', 'At Risk')->count(),
                'description' => 'Spent big money, purchased often but long time ago',
                'action' => 'Send personalized emails, offer discounts',
            ],
            'cant_lose_them' => [
                'count' => $customers->where('segment', "Can't Lose Them")->count(),
                'description' => 'Made big purchases and often, but haven\'t returned for a long time',
                'action' => 'Aggressive win-back campaigns',
            ],
            'hibernating' => [
                'count' => $customers->where('segment', 'Hibernating')->count(),
                'description' => 'Last purchase was long ago, low spenders',
                'action' => 'Reactivation campaigns with discounts',
            ],
            'lost' => [
                'count' => $customers->where('segment', 'Lost')->count(),
                'description' => 'Lowest recency, frequency, and monetary scores',
                'action' => 'Ignore or minimal effort',
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Calculate quartiles for a dataset.
     */
    private function calculateQuartiles($values): array
    {
        if ($values->isEmpty()) {
            return ['q1' => 0, 'q2' => 0, 'q3' => 0, 'q4' => 0];
        }

        $count = $values->count();

        return [
            'q1' => $values[(int) ($count * 0.25)] ?? $values->first(),
            'q2' => $values[(int) ($count * 0.50)] ?? $values->first(),
            'q3' => $values[(int) ($count * 0.75)] ?? $values->first(),
            'q4' => $values->last(),
        ];
    }

    /**
     * Get RFM score (1-5) based on quartiles.
     */
    private function getScore($value, array $quartiles, bool $reverse = false): int
    {
        if ($value <= $quartiles['q1']) {
            return $reverse ? 5 : 1;
        } elseif ($value <= $quartiles['q2']) {
            return $reverse ? 4 : 2;
        } elseif ($value <= $quartiles['q3']) {
            return $reverse ? 3 : 3;
        } elseif ($value <= $quartiles['q4']) {
            return $reverse ? 2 : 4;
        }

        return $reverse ? 1 : 5;
    }

    /**
     * Determine customer segment based on RFM scores.
     */
    private function getRfmSegment(int $r, int $f, int $m): string
    {
        // Champions: High R, F, M
        if ($r >= 4 && $f >= 4 && $m >= 4) {
            return 'Champions';
        }

        // Loyal Customers: High F, M but moderate R
        if ($f >= 4 && $m >= 4) {
            return 'Loyal Customers';
        }

        // Potential Loyalists: High R, moderate F, M
        if ($r >= 4 && $f >= 2 && $m >= 2) {
            return 'Potential Loyalists';
        }

        // At Risk: High F, M but low R
        if ($r <= 2 && $f >= 3 && $m >= 3) {
            return 'At Risk';
        }

        // Can't Lose Them: Very high F, M but very low R
        if ($r <= 2 && $f >= 4 && $m >= 4) {
            return "Can't Lose Them";
        }

        // Hibernating: Low R, F, M but not completely lost
        if ($r <= 2 && $f <= 2 && $m <= 2) {
            return 'Hibernating';
        }

        // Lost: Lowest scores
        if ($r == 1 && $f <= 2) {
            return 'Lost';
        }

        // New Customers: High R but low F
        if ($r >= 4 && $f <= 2) {
            return 'New Customers';
        }

        return 'Promising';
    }
}
