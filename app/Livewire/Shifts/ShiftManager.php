<?php

namespace App\Livewire\Shifts;

use App\Enums\ShiftStatus;
use App\Models\Outlet;
use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ShiftManager extends Component
{
    use WithPagination;

    public bool $isOpenModalOpen = false;

    public bool $isCloseModalOpen = false;

    public float $openingCash = 0;

    public float $closingCash = 0;

    public string $notes = '';

    public function openShiftModal(): void
    {
        $this->openingCash = 100000;
        $this->notes = '';
        $this->resetErrorBag();
        $this->isOpenModalOpen = true;
    }

    public function startShift(): void
    {
        $this->validate([
            'openingCash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $outlet = Outlet::first();

        Shift::create([
            'uuid' => (string) Str::uuid(),
            'outlet_id' => $outlet->id,
            'cashier_id' => auth()->id(),
            'opening_cash' => $this->openingCash,
            'expected_cash' => $this->openingCash,
            'opened_at' => now(),
            'status' => ShiftStatus::Open,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Shift kasir berhasil dibuka.');
        $this->isOpenModalOpen = false;
    }

    public function openCloseShiftModal(int $shiftId): void
    {
        $shift = Shift::findOrFail($shiftId);

        // Calculate expected cash = opening cash + total cash sales during this shift
        $cashSales = Transaction::where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->whereHas('payments', function ($q): void {
                $q->where('payment_method', 'cash');
            })
            ->sum('total');

        $expected = (float) $shift->opening_cash + (float) $cashSales;
        $shift->update(['expected_cash' => $expected]);

        $this->closingCash = $expected;
        $this->notes = '';
        $this->resetErrorBag();
        $this->isCloseModalOpen = true;
    }

    public function finishShift(int $shiftId): void
    {
        $this->validate([
            'closingCash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $shift = Shift::findOrFail($shiftId);
        $diff = $this->closingCash - (float) $shift->expected_cash;

        $shift->update([
            'closing_cash' => $this->closingCash,
            'difference' => $diff,
            'closed_at' => now(),
            'status' => ShiftStatus::Closed,
            'notes' => trim(($shift->notes ?? '').' | Penutupan: '.$this->notes),
        ]);

        session()->flash('success', 'Shift kasir berhasil ditutup dan direkonsiliasi.');
        $this->isCloseModalOpen = false;
    }

    public function render(): View
    {
        $currentShift = Shift::where('cashier_id', auth()->id())
            ->where('status', ShiftStatus::Open)
            ->first();

        $shifts = Shift::with(['cashier', 'outlet'])
            ->latest()
            ->paginate(10);

        return view('livewire.shifts.shift-manager', [
            'currentShift' => $currentShift,
            'shifts' => $shifts,
        ]);
    }
}
