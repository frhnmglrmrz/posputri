<div class="space-y-6">
    <x-alert />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola akun kasir, supervisor, dan administrator sistem POS.</p>
        </div>
        <button wire:click="openCreateModal" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-2 active:scale-98">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white border border-slate-200 p-4 rounded-2xl flex flex-col sm:flex-row gap-4 items-center justify-between shadow-xs">
        <div class="w-full sm:w-96 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs focus:ring-2 focus:ring-emerald-500 shadow-2xs">
            <div class="absolute left-3 top-2.5 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select wire:model.live="roleFilter" class="w-full sm:w-auto px-4 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 text-xs focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                <option value="all">Semua Role</option>
                @foreach($roles as $r)
                <option value="{{ $r->name }}">{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Nama</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Outlet Cabang</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-slate-500 font-mono">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $roleItem)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                {{ $roleItem->name === 'Admin' ? 'bg-slate-900 text-white' : ($roleItem->name === 'Supervisor' ? 'bg-amber-50 text-amber-800 border border-amber-300' : ($roleItem->name === 'Inventory' ? 'bg-cyan-50 text-cyan-800 border border-cyan-300' : 'bg-emerald-50 text-emerald-800 border border-emerald-300')) }}">
                                {{ $roleItem->name }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-slate-700 font-medium">{{ $user->outlet->name ?? 'Semua Outlet' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="edit({{ $user->id }})" class="text-emerald-700 hover:text-emerald-900 font-bold text-xs">
                                Edit
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="delete({{ $user->id }})" wire:confirm="Hapus pengguna ini?" class="text-rose-600 hover:text-rose-800 font-medium text-xs">
                                Hapus
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-4 border-t border-slate-200 bg-white">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
        <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-xl p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <h2 class="text-base font-bold text-slate-900">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    @error('name') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" wire:model="email" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    @error('email') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">{{ $userId ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}</label>
                    <input type="password" wire:model="password" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    @error('password') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Role / Peran</label>
                    <select wire:model="role" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-emerald-500">
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Outlet Cabang</label>
                    <select wire:model="outlet_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Outlet / Kantor Pusat</option>
                        @foreach($outlets as $out)
                        <option value="{{ $out->id }}">{{ $out->name }} ({{ $out->code }})</option>
                        @endforeach
                    </select>
                    @error('outlet_id') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="user_is_active" wire:model="is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="user_is_active" class="font-medium text-slate-700">Akun Aktif</label>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-200">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-xl border border-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
