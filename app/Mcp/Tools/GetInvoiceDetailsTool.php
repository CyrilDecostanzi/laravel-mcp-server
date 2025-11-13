<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetInvoiceDetailsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get detailed invoice information including associated order, customer, and payment details. Search by invoice number or filter by status and date range.
    MARKDOWN;

    /**
     * Define the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Search query (invoice number, customer email, or name)'),
            'status' => $schema->string()
                ->description('Filter by invoice status')
                ->enum(['draft', 'sent', 'paid', 'overdue', 'cancelled']),
            'date_from' => $schema->string()
                ->description('Filter invoices from this issue date (YYYY-MM-DD)'),
            'date_to' => $schema->string()
                ->description('Filter invoices until this issue date (YYYY-MM-DD)'),
            'limit' => $schema->integer()
                ->description('Maximum number of results (default: 20, max: 100)')
                ->default(20),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $params = $request->validate([
            'query' => 'string',
            'status' => 'string|in:draft,sent,paid,overdue,cancelled',
            'date_from' => 'string|date',
            'date_to' => 'string|date',
            'limit' => 'integer|min:1|max:100',
        ]);

        $query = Invoice::with(['order', 'user', 'payments']);

        // Text search
        if (! empty($params['query'])) {
            $searchTerm = $params['query'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('email', 'like', "%{$searchTerm}%")
                            ->orWhere('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Status filter
        if (! empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        // Date range filter
        if (! empty($params['date_from'])) {
            $query->whereDate('issue_date', '>=', $params['date_from']);
        }
        if (! empty($params['date_to'])) {
            $query->whereDate('issue_date', '<=', $params['date_to']);
        }

        $limit = min($params['limit'] ?? 20, 100);

        $invoices = $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($invoice) {
                $totalPaid = $invoice->payments->where('status', 'completed')->sum('amount');
                $balance = (float) $invoice->amount - (float) $totalPaid;

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer' => [
                        'id' => $invoice->user->id,
                        'name' => $invoice->user->name,
                        'email' => $invoice->user->email,
                    ],
                    'order' => [
                        'id' => $invoice->order->id,
                        'order_number' => $invoice->order->order_number,
                        'status' => $invoice->order->status,
                    ],
                    'status' => $invoice->status,
                    'amounts' => [
                        'total' => (float) $invoice->amount,
                        'paid' => round($totalPaid, 2),
                        'balance' => round($balance, 2),
                    ],
                    'dates' => [
                        'issue_date' => $invoice->issue_date->toDateString(),
                        'due_date' => $invoice->due_date->toDateString(),
                        'paid_at' => $invoice->paid_at?->toDateString(),
                        'days_until_due' => $invoice->due_date->diffInDays(now(), false),
                    ],
                    'is_overdue' => $invoice->isOverdue(),
                    'payments' => $invoice->payments->map(fn ($payment) => [
                        'id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'method' => $payment->payment_method,
                        'status' => $payment->status,
                        'amount' => (float) $payment->amount,
                        'paid_at' => $payment->paid_at?->toISOString(),
                    ]),
                    'payment_count' => $invoice->payments->count(),
                ];
            });

        $data = [
            'invoices' => $invoices,
            'count' => $invoices->count(),
            'summary' => [
                'total_invoice_amount' => round($invoices->sum('amounts.total'), 2),
                'total_paid' => round($invoices->sum('amounts.paid'), 2),
                'total_balance' => round($invoices->sum('amounts.balance'), 2),
                'overdue_count' => $invoices->where('is_overdue', true)->count(),
            ],
            'filters_applied' => array_filter($params ?? []),
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
