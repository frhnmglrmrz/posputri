<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Outlet & Perangkat</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola cabang toko, register perangkat kasir, dan konfigurasi umum POS.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($activeTab === 'outlets')
            <button wire:click="openCreateOutletModal" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Outlet</span>
            </button>
            @elseif($activeTab === 'devices')
            <button wire:click="openCreateDeviceModal" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Daftarkan Perangkat</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Tab Bar -->
    <div class="flex gap-2 border-b border-slate-800">
        <button wire:click="$set('activeTab', 'outlets')" class="px-4 py-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'outlets' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-200' }}">
            Outlet (Cabang)
        </button>
        <button wire:click="$set('activeTab', 'devices')" class="px-4 py-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'devices' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-200' }}">
            Perangkat Kasir (POS Terminals)
        </button>
        <button wire:click="$set('activeTab', 'settings')" class="px-4 py-2 text-sm font-semibold border-b-2 transition {{ $activeTab === 'settings' ? 'border-indigo-500 text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-200' }}">
            Pengaturan Sistem
        </button>
    </div>

    <!-- TAB 1: OUTLETS -->
    @if($activeTab === 'outlets')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($outlets as $outlet)
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-lg flex flex-col justify-between">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-800 text-indigo-400 border border-slate-700">
                        {{ $outlet->code }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $outlet->is_active ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/50' : 'bg-slate-800 text-slate-400' }}">
                        {{ $outlet->is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-white">{{ $outlet->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $outlet->address ?: 'Alamat belum diatur' }}</p>
                    @if($outlet->phone)
                    <p class="text-xs text-slate-400 mt-0.5">Telp: {{ $outlet->phone }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span>{{ $outlet->devices_count }} Perangkat Terhubung</span>
                <button wire:click="editOutlet({{ $outlet->id }})" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                    Edit
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400 bg-slate-900 border border-slate-800 rounded-2xl">
            Belum ada data outlet. Klik "Tambah Outlet" untuk menambahkan cabang.
        </div>
        @endforelse
    </div>
    @endif

    <!-- TAB 2: DEVICES -->
    @if($activeTab === 'devices')
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-950/60 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Nama Perangkat</th>
                        <th class="px-6 py-3.5">Outlet</th>
                        <th class="px-6 py-3.5">Device Token</th>
                        <th class="px-6 py-3.5">Terakhir Aktif</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($devices as $device)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4 font-semibold text-white">{{ $device->name }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $device->outlet->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-indigo-400">{{ $device->device_token }}</td>
                        <td class="px-6 py-4 text-xs text-slate-400">{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Belum pernah' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/50">
                                {{ $device->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="deleteDevice({{ $device->id }})" wire:confirm="Hapus perangkat ini?" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada perangkat terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- TAB 3: SETTINGS -->
    @if($activeTab === 'settings')
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl max-w-2xl space-y-6">
        <div>
            <h3 class="text-lg font-bold text-white">Konfigurasi Kasir & Struk</h3>
            <p class="text-xs text-slate-400 mt-1">Pengaturan struk printer thermal (58mm / 80mm) dan nilai pajak default.</p>
        </div>

        <form wire:submit="saveSettings" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Header Struk (Nama Usaha)</label>
                <input type="text" wire:model="receipt_header" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Footer Struk</label>
                <textarea wire:model="receipt_footer" rows="2" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1">Tarif Pajak Default (%)</label>
                <input type="number" step="0.1" wire:model="default_tax" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Modal Outlet -->
    @if($isOutletModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4">
            <h2 class="text-lg font-bold text-white">{{ $outletId ? 'Edit Outlet' : 'Tambah Outlet Baru' }}</h2>

            <form wire:submit="saveOutlet" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Kode Cabang</label>
                    <input type="text" wire:model="outlet_code" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('outlet_code') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nama Outlet / Cabang</label>
                    <input type="text" wire:model="outlet_name" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('outlet_name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Alamat</label>
                    <textarea wire:model="outlet_address" rows="2" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nomor Telepon</label>
                    <input type="text" wire:model="outlet_phone" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="outlet_is_active" wire:model="outlet_is_active" class="rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <label for="outlet_is_active" class="text-xs text-slate-300">Outlet Aktif</label>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-800">
                    <button type="button" wire:click="$set('isOutletModalOpen', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Device -->
    @if($isDeviceModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4">
            <h2 class="text-lg font-bold text-white">Daftarkan Perangkat Kasir</h2>

            <form wire:submit="saveDevice" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Outlet</label>
                    <select wire:model="device_outlet_id" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($outlets as $out)
                        <option value="{{ $out->id }}">{{ $out->name }} ({{ $out->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nama Perangkat (e.g. Kasir 1 Depan)</label>
                    <input type="text" wire:model="device_name" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('device_name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-800">
                    <button type="button" wire:click="$set('isDeviceModalOpen', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30">
                        Daftarkan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
