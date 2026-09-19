<?php

namespace App\Livewire\Inventory;

use App\Enums\StockMovementType;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $tab = 'stocks'; // 'stocks' or 'movements'

    public bool $isAdjustModalOpen = false;

    public ?int $selectedProductId = null;

    public int $quantityChange = 0;

    public string $movementType = 'ADJUSTMENT';

    public string $notes = '';

    public function openAdjustModal(int $productId): void
    {
        $this->selectedProductId = $productId;
        $this->quantityChange = 0;
        $this->movementType = 'ADJUSTMENT';
        $this->notes = '';
        $this->resetErrorBag();
        $this->isAdjustModalOpen = true;
    }

    public function saveAdjustment(InventoryService $inventoryService): void
    {
        $this->validate([
            'selectedProductId' => ['required', 'exists:products,id'],
            'quantityChange' => ['required', 'integer', 'not_in:0'],
            'movementType' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $outlet = Outlet::first();
        $product = Product::findOrFail($this->selectedProductId);

        $type = match ($this->movementType) {
            'PURCHASE' => StockMovementType::Purchase,
            'RETURN' => StockMovementType::ReturnOrder,
            default => StockMovementType::Adjustment,
        };

        $inventoryService->adjustStock(
            outlet: $outlet,
            product: $product,
            quantityChange: $this->quantityChange,
            type: $type,
            referenceType: 'MANUAL_ADJUSTMENT',
            referenceUuid: (string) auth()->id(),
            notes: $this->notes ?: 'Penyesuaian manual oleh '.auth()->user()->name
        );

        session()->flash('success', "Stok {$product->name} berhasil disesuaikan ({$this->quantityChange}).");
        $this->isAdjustModalOpen = false;
    }

    public function render(): View
    {
        $outlet = Outlet::first();

        $stocks = Product::query()
            ->with(['category', 'stocks' => function ($q) use ($outlet): void {
                if ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                }
            }])
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            })
            ->paginate(15);

        $movements = StockMovement::query()
            ->with('product')
            ->latest()
            ->take(30)
            ->get();

        return view('livewire.inventory.inventory-manager', [
            'stocks' => $stocks,
            'movements' => $movements,
        ]);
    }
}
