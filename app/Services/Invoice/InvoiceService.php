<?php

namespace App\Services\Invoice;

use App\Models\Invoice;

class InvoiceService
{
    /**
     * Get invoice details with payment tracking.
     */
    public function getInvoiceDetails(int $invoiceId): array
    {
        $invoice = Invoice::with(['user', 'order', 'payments'])->find($invoiceId);

        if (!$invoice) {
            return [
                'success' => false,
                'error' => "Invoice with ID {$invoiceId} not found",
            ];
        }

        $totalPaid = $invoice->payments->where('status', 'completed')->sum('amount');
        $balance = (float) $invoice->amount - (float) $totalPaid;
        $isOverdue = $invoice->status !== 'paid' && $invoice->due_date < now();

        return [
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date->toDateString(),
                'due_date' => $invoice->due_date->toDateString(),
                'is_overdue' => $isOverdue,
                'days_until_due' => $invoice->due_date->diffInDays(now(), false),
            ],
            'customer' => [
                'id' => $invoice->user->id,
                'name' => $invoice->user->name,
                'email' => $invoice->user->email,
            ],
            'order' => $invoice->order ? [
                'id' => $invoice->order->id,
                'order_number' => $invoice->order->order_number,
                'status' => $invoice->order->status,
                'total' => (float) $invoice->order->total,
            ] : null,
            'payment_summary' => [
                'total_amount' => (float) $invoice->amount,
                'total_paid' => round((float) $totalPaid, 2),
                'balance' => round($balance, 2),
                'payment_count' => $invoice->payments->count(),
            ],
            'payments' => $invoice->payments->map(fn($payment) => [
                'id' => $payment->id,
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'paid_at' => $payment->paid_at?->toISOString(),
            ])->toArray(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get invoice statistics.
     */
    public function getInvoiceStatistics(): array
    {
        $total = Invoice::count();
        $byStatus = Invoice::select('status')
            ->selectRaw('count(*) as count')
            ->selectRaw('sum(amount) as total_amount')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->status => [
                    'count' => $item->count,
                    'total_amount' => (float) $item->total_amount,
                ]
            ]);

        $overdue = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        return [
            'total_invoices' => $total,
            'by_status' => $byStatus,
            'overdue_count' => $overdue,
            'timestamp' => now()->toISOString(),
        ];
    }
}
