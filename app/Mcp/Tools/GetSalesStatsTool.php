<?php

namespace App\Mcp\Tools;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
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

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        // Overall statistics
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->status => [
                    'count' => $item->count,
                    'revenue' => (float) $item->revenue,
                ]
            ]);
        
        // Time-based statistics
        $today = Order::whereDate('created_at', today())
            ->selectRaw('count(*) as orders, sum(total) as revenue')
            ->first();
        
        $thisWeek = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('count(*) as orders, sum(total) as revenue')
            ->first();
        
        $thisMonth = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('count(*) as orders, sum(total) as revenue')
            ->first();
        
        $lastMonth = Order::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->selectRaw('count(*) as orders, sum(total) as revenue')
            ->first();
        
        // Payment statistics
        $completedPayments = Payment::where('status', 'completed')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $failedPayments = Payment::where('status', 'failed')->count();
        $totalPaymentAmount = Payment::where('status', 'completed')->sum('amount');
        
        // Payment methods breakdown
        $paymentMethods = Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->payment_method => [
                    'count' => $item->count,
                    'total' => (float) $item->total,
                ]
            ]);
        
        // Growth metrics
        $growthRate = $lastMonth->revenue > 0 
            ? (($thisMonth->revenue - $lastMonth->revenue) / $lastMonth->revenue) * 100 
            : 0;

        $data = [
            'overview' => [
                'total_orders' => $totalOrders,
                'total_revenue' => round((float) $totalRevenue, 2),
                'average_order_value' => round($averageOrderValue, 2),
                'completed_payments' => $completedPayments,
                'total_payment_amount' => round((float) $totalPaymentAmount, 2),
            ],
            'orders_by_status' => $ordersByStatus,
            'time_periods' => [
                'today' => [
                    'orders' => $today->orders ?? 0,
                    'revenue' => round((float) ($today->revenue ?? 0), 2),
                ],
                'this_week' => [
                    'orders' => $thisWeek->orders ?? 0,
                    'revenue' => round((float) ($thisWeek->revenue ?? 0), 2),
                ],
                'this_month' => [
                    'orders' => $thisMonth->orders ?? 0,
                    'revenue' => round((float) ($thisMonth->revenue ?? 0), 2),
                ],
                'last_month' => [
                    'orders' => $lastMonth->orders ?? 0,
                    'revenue' => round((float) ($lastMonth->revenue ?? 0), 2),
                ],
            ],
            'growth' => [
                'monthly_growth_rate' => round($growthRate, 2) . '%',
            ],
            'payments' => [
                'completed' => $completedPayments,
                'pending' => $pendingPayments,
                'failed' => $failedPayments,
                'by_method' => $paymentMethods,
            ],
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
