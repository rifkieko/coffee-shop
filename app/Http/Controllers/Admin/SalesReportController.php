<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);

        $baseQuery = $this->buildBaseQuery($filters);

        $orders = (clone $baseQuery)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = clone $baseQuery;

        $summary = [
            'paid_orders' => $summaryQuery->count(),
            'revenue' => (clone $baseQuery)->sum('total_amount'),
        ];

        $summary['average'] = $summary['paid_orders'] > 0
            ? $summary['revenue'] / $summary['paid_orders']
            : 0;

        return view('admin.reports.sales', [
            'orders' => $orders,
            'filters' => $filters,
            'summary' => $summary,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);

        $orders = $this->buildBaseQuery($filters)
            ->orderBy('created_at')
            ->get();

        $filename = 'sales-report-'.now()->format('Ymd_His').'.csv';
        $delimiter = ',';
        $contentType = 'text/csv; charset=UTF-8';

        return response()->streamDownload(function () use ($orders, $filters, $delimiter): void {
            $handle = fopen('php://output', 'w');

            // Add BOM so Excel opens UTF-8 correctly.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Tanggal',
                'Nomor Pesanan',
                'Nama Pelanggan',
                'Meja',
                'Menu Dibeli',
                'Status Pesanan',
                'Status Pembayaran',
                'Total (Rp)',
            ], $delimiter);

            foreach ($orders as $order) {
                $items = $order->items->map(function ($item) {
                    $name = $item->menu_name ?? $item->menuItem?->name ?? 'Menu';
                    return "{$item->quantity}x {$name}";
                })->join('; ');

                fputcsv($handle, [
                    $order->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i'),
                    $order->order_number,
                    $order->customer_name ?? $order->user?->name ?? 'Tamu',
                    $order->table_number ?? 'Take Away',
                    $items,
                    $order->status->label(),
                    $order->payment_status->label(),
                    $order->total_amount,
                ], $delimiter);
            }

            fputcsv($handle, [], $delimiter);
            fputcsv($handle, ['Total Pesanan Dibayar', $orders->count()], $delimiter);
            fputcsv($handle, ['Total Penghasilan (Rp)', $orders->sum('total_amount')], $delimiter);
            fclose($handle);
        }, $filename, [
            'Content-Type' => $contentType,
        ]);
    }

    protected function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $start = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $end = $validated['end_date'] ?? now()->toDateString();

        if ($end < $start) {
            $end = $start;
        }

        return [
            'start_date' => $start,
            'end_date' => $end,
        ];
    }

    protected function buildBaseQuery(array $filters)
    {
        return Order::with(['user', 'items.menuItem'])
            ->where('payment_status', PaymentStatus::Paid)
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
    }
}
