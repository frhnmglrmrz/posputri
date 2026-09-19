<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $viewingTransactionId = null;

    public bool $isDetailModalOpen = false;

    public string $voidReason = '';

    public function viewDetails(int $id): void
    {
        $this->viewingTransactionId = $id;
        $this->isDetailModalOpen = true;
    }

    public function voidTransaction(int $id, TransactionService $transactionService): void
    {
        $transaction = Transaction::findOrFail($id);

        if (! auth()->user()->hasAnyRole(['Admin', 'Supervisor'])) {
            session()->flash('error', 'Hanya Admin atau Supervisor yang berhak membatalkan transaksi.');

            return;
        }

        $success = $transactionService->voidTransaction($transaction, $this->voidReason ?: 'Dibatalkan oleh supervisor');

        if ($success) {
            session()->flash('success', "Transaksi #{$transaction->transaction_number} berhasil dibatalkan dan stok dikembalikan.");
        } else {
            session()->flash('error', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        $this->isDetailModalOpen = false;
        $this->voidReason = '';
    }

    public function render(): View
    {
        $transactions = Transaction::query()
            ->with(['cashier', 'customer', 'payments'])
            ->when($this->statusFilter !== 'all', function ($query): void {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query): void {
                $query->where('transaction_number', 'like', "%{$this->search}%")
                    ->orWhere('uuid', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        $selectedTransaction = $this->viewingTransactionId
            ? Transaction::with(['items.product', 'payments', 'cashier', 'outlet'])->find($this->viewingTransactionId)
            : null;

        return view('livewire.transactions.transaction-history', [
            'transactions' => $transactions,
            'selectedTransaction' => $selectedTransaction,
        ]);
    }
}
