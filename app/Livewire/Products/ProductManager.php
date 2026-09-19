<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = 'all';

    public bool $isModalOpen = false;

    public ?int $productId = null;

    public ?int $category_id = null;

    public string $sku = '';

    public string $barcode = '';

    public string $name = '';

    public float $purchase_price = 0;

    public float $selling_price = 0;

    public float $tax_rate = 0;

    public int $initial_stock = 0;

    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.$this->productId],
            'barcode' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['productId', 'category_id', 'sku', 'barcode', 'name', 'purchase_price', 'selling_price', 'tax_rate', 'initial_stock', 'is_active']);
        $this->sku = 'PRD-'.strtoupper(Str::random(6));
        $this->is_active = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->sku = $product->sku;
        $this->barcode = (string) ($product->barcode ?? '');
        $this->name = $product->name;
        $this->purchase_price = (float) $product->purchase_price;
        $this->selling_price = (float) $product->selling_price;
        $this->tax_rate = (float) $product->tax_rate;
        $this->is_active = (bool) $product->is_active;

        $outlet = Outlet::first();
        $this->initial_stock = $outlet ? (int) ($product->stocks()->where('outlet_id', $outlet->id)->value('quantity') ?? 0) : 0;

        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($validated);
            session()->flash('success', "Produk '{$this->name}' berhasil diperbarui.");
        } else {
            $product = Product::create([
                'uuid' => (string) Str::uuid(),
                ...$validated,
            ]);

            $outlet = Outlet::first();
            if ($outlet && $this->initial_stock > 0) {
                Stock::create([
                    'outlet_id' => $outlet->id,
                    'product_id' => $product->id,
                    'quantity' => $this->initial_stock,
                ]);
            }

            session()->flash('success', "Produk '{$this->name}' berhasil ditambahkan.");
        }

        $this->isModalOpen = false;
        $this->reset(['productId', 'category_id', 'sku', 'barcode', 'name', 'purchase_price', 'selling_price', 'tax_rate', 'initial_stock']);
    }

    public function delete(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();
        session()->flash('success', "Produk '{$product->name}' berhasil dihapus.");
    }

    public function render(): View
    {
        $outlet = Outlet::first();

        $products = Product::query()
            ->with(['category', 'stocks' => function ($q) use ($outlet): void {
                if ($outlet) {
                    $q->where('outlet_id', $outlet->id);
                }
            }])
            ->when($this->categoryFilter !== 'all', function ($query): void {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%")
                        ->orWhere('barcode', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.products.product-manager', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }
}
