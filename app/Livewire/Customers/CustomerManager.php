<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerManager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $isModalOpen = false;

    public ?int $customerId = null;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['customerId', 'name', 'phone', 'email', 'address']);
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = (string) ($customer->phone ?? '');
        $this->email = (string) ($customer->email ?? '');
        $this->address = (string) ($customer->address ?? '');
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->customerId) {
            $customer = Customer::findOrFail($this->customerId);
            $customer->update($validated);
            session()->flash('success', "Pelanggan '{$this->name}' berhasil diperbarui.");
        } else {
            Customer::create([
                'uuid' => (string) Str::uuid(),
                ...$validated,
            ]);
            session()->flash('success', "Pelanggan '{$this->name}' berhasil ditambahkan.");
        }

        $this->isModalOpen = false;
        $this->reset(['customerId', 'name', 'phone', 'email', 'address']);
    }

    public function delete(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        session()->flash('success', "Pelanggan '{$customer->name}' berhasil dihapus.");
    }

    public function render(): View
    {
        $customers = Customer::query()
            ->when($this->search, function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        return view('livewire.customers.customer-manager', [
            'customers' => $customers,
        ]);
    }
}
