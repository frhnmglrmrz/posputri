<?php

namespace App\Livewire\Outlets;

use App\Models\Device;
use App\Models\Outlet;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

class OutletManager extends Component
{
    public string $activeTab = 'outlets'; // 'outlets', 'devices', 'settings'

    // Outlet form
    public bool $isOutletModalOpen = false;

    public ?int $outletId = null;

    public string $outlet_name = '';

    public string $outlet_code = '';

    public string $outlet_address = '';

    public string $outlet_phone = '';

    public bool $outlet_is_active = true;

    // Device form
    public bool $isDeviceModalOpen = false;

    public ?int $deviceId = null;

    public ?int $device_outlet_id = null;

    public string $device_name = '';

    // Settings
    public string $receipt_header = 'POS PUTRI';

    public string $receipt_footer = 'Terima kasih atas kunjungan Anda!';

    public float $default_tax = 11.0;

    public function mount(): void
    {
        $this->receipt_header = Setting::get('receipt_header', 'POS PUTRI');
        $this->receipt_footer = Setting::get('receipt_footer', 'Terima kasih atas kunjungan Anda!');
        $this->default_tax = (float) Setting::get('default_tax', 11.0);
    }

    public function openCreateOutletModal(): void
    {
        $this->reset(['outletId', 'outlet_name', 'outlet_code', 'outlet_address', 'outlet_phone']);
        $this->outlet_is_active = true;
        $this->outlet_code = 'OUT-'.strtoupper(Str::random(4));
        $this->resetErrorBag();
        $this->isOutletModalOpen = true;
    }

    public function editOutlet(int $id): void
    {
        $outlet = Outlet::findOrFail($id);
        $this->outletId = $outlet->id;
        $this->outlet_name = $outlet->name;
        $this->outlet_code = $outlet->code;
        $this->outlet_address = (string) ($outlet->address ?? '');
        $this->outlet_phone = (string) ($outlet->phone ?? '');
        $this->outlet_is_active = (bool) $outlet->is_active;
        $this->resetErrorBag();
        $this->isOutletModalOpen = true;
    }

    public function saveOutlet(): void
    {
        $this->validate([
            'outlet_name' => ['required', 'string', 'max:255'],
            'outlet_code' => ['required', 'string', 'max:50', 'unique:outlets,code,'.$this->outletId],
            'outlet_address' => ['nullable', 'string'],
            'outlet_phone' => ['nullable', 'string', 'max:50'],
        ]);

        if ($this->outletId) {
            $outlet = Outlet::findOrFail($this->outletId);
            $outlet->update([
                'name' => $this->outlet_name,
                'code' => $this->outlet_code,
                'address' => $this->outlet_address,
                'phone' => $this->outlet_phone,
                'is_active' => $this->outlet_is_active,
            ]);
            session()->flash('success', "Outlet '{$this->outlet_name}' berhasil diperbarui.");
        } else {
            Outlet::create([
                'uuid' => (string) Str::uuid(),
                'name' => $this->outlet_name,
                'code' => $this->outlet_code,
                'address' => $this->outlet_address,
                'phone' => $this->outlet_phone,
                'is_active' => $this->outlet_is_active,
            ]);
            session()->flash('success', "Outlet '{$this->outlet_name}' berhasil ditambahkan.");
        }

        $this->isOutletModalOpen = false;
        $this->reset(['outletId', 'outlet_name', 'outlet_code', 'outlet_address', 'outlet_phone']);
    }

    public function openCreateDeviceModal(): void
    {
        $this->reset(['deviceId', 'device_name']);
        $this->device_outlet_id = Outlet::first()?->id;
        $this->resetErrorBag();
        $this->isDeviceModalOpen = true;
    }

    public function saveDevice(): void
    {
        $this->validate([
            'device_name' => ['required', 'string', 'max:255'],
            'device_outlet_id' => ['required', 'exists:outlets,id'],
        ]);

        Device::create([
            'uuid' => (string) Str::uuid(),
            'outlet_id' => $this->device_outlet_id,
            'name' => $this->device_name,
            'device_token' => 'DEV-'.Str::upper(Str::random(16)),
            'status' => 'active',
            'last_seen_at' => now(),
        ]);

        session()->flash('success', "Perangkat '{$this->device_name}' berhasil didaftarkan.");
        $this->isDeviceModalOpen = false;
        $this->reset(['deviceId', 'device_name', 'device_outlet_id']);
    }

    public function deleteDevice(int $id): void
    {
        $device = Device::findOrFail($id);
        $device->delete();
        session()->flash('success', "Perangkat '{$device->name}' berhasil dihapus.");
    }

    public function saveSettings(): void
    {
        Setting::set('receipt_header', $this->receipt_header);
        Setting::set('receipt_footer', $this->receipt_footer);
        Setting::set('default_tax', (string) $this->default_tax);

        session()->flash('success', 'Pengaturan sistem berhasil disimpan.');
    }

    public function render(): View
    {
        return view('livewire.outlets.outlet-manager', [
            'outlets' => Outlet::withCount('devices')->latest()->get(),
            'devices' => Device::with('outlet')->latest()->get(),
        ]);
    }
}
