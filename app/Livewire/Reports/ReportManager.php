<?php

namespace App\Livewire\Reports;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportManager extends Component
{
    public string $startDate = '';

    public string $endDate = '';

    public string $reportType = 'sales'; // sales, products, cashiers, payments

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function exportCsv(): StreamedResponse
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();
        $fileName = 'laporan-penjualan-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($start, $end): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['BERKAH MART - LAPORAN PENJUALAN & TRANSAKSI']);
            fputcsv($handle, ['Periode', $start->format('d/m/Y').' s/d '.$end->format('d/m/Y')]);
            fputcsv($handle, ['Tanggal Ekspor', now()->format('d/m/Y H:i:s')]);
            fputcsv($handle, []);

            // Summary
            $totalRevenue = (float) Transaction::where('status', 'completed')
                ->whereBetween('transaction_at', [$start, $end])
                ->sum('total');
            $totalTransactions = Transaction::where('status', 'completed')
                ->whereBetween('transaction_at', [$start, $end])
                ->count();
            $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

            fputcsv($handle, ['RINGKASAN']);
            fputcsv($handle, ['Total Omzet (Rp)', $totalRevenue]);
            fputcsv($handle, ['Total Transaksi', $totalTransactions]);
            fputcsv($handle, ['Rata-rata Basket Size (Rp)', $avgTransaction]);
            fputcsv($handle, []);

            // Top Products
            fputcsv($handle, ['PRODUK TERLARIS']);
            fputcsv($handle, ['Peringkat', 'Nama Produk', 'Jumlah Terjual (pcs)', 'Total Penjualan (Rp)']);
            $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
                ->whereHas('transaction', fn ($q) => $q->where('status', 'completed')->whereBetween('transaction_at', [$start, $end]))
                ->groupBy('product_name')
                ->orderByDesc('total_qty')
                ->take(20)
                ->get();
            foreach ($topProducts as $idx => $prod) {
                fputcsv($handle, [$idx + 1, $prod->product_name, $prod->total_qty, $prod->total_amount]);
            }
            fputcsv($handle, []);

            // Payment Methods
            fputcsv($handle, ['METODE PEMBAYARAN']);
            fputcsv($handle, ['Metode', 'Jumlah Transaksi', 'Total Nominal (Rp)']);
            $paymentBreakdown = Payment::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
                ->whereHas('transaction', fn ($q) => $q->where('status', 'completed')->whereBetween('transaction_at', [$start, $end]))
                ->groupBy('payment_method')
                ->get();
            foreach ($paymentBreakdown as $pay) {
                fputcsv($handle, [strtoupper($pay->payment_method), $pay->count, $pay->total_amount]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function render(): View
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // Summary Totals
        $totalRevenue = Transaction::where('status', 'completed')
            ->whereBetween('transaction_at', [$start, $end])
            ->sum('total');

        $totalTransactions = Transaction::where('status', 'completed')
            ->whereBetween('transaction_at', [$start, $end])
            ->count();

        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Top Products Sold
        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('transaction', function ($q) use ($start, $end): void {
                $q->where('status', 'completed')
                    ->whereBetween('transaction_at', [$start, $end]);
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // Payment Methods Breakdown
        $paymentBreakdown = Payment::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->whereHas('transaction', function ($q) use ($start, $end): void {
                $q->where('status', 'completed')
                    ->whereBetween('transaction_at', [$start, $end]);
            })
            ->groupBy('payment_method')
            ->get();

        // Cashier Performance
        $cashierStats = User::role('Cashier')
            ->withCount(['transactions' => function ($q) use ($start, $end): void {
                $q->where('status', 'completed')
                    ->whereBetween('transaction_at', [$start, $end]);
            }])
            ->withSum(['transactions' => function ($q) use ($start, $end): void {
                $q->where('status', 'completed')
                    ->whereBetween('transaction_at', [$start, $end]);
            }], 'total')
            ->get();

        return view('livewire.reports.report-manager', [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'avgTransaction' => $avgTransaction,
            'topProducts' => $topProducts,
            'paymentBreakdown' => $paymentBreakdown,
            'cashierStats' => $cashierStats,
        ]);
    }
}
