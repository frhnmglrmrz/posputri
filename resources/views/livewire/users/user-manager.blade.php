<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-lg">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola akun kasir, supervisor, dan administrator sistem POS.</p>
        </div>
        <button wire:click="openCreateModal" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-96 relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." class="w-full pl-10 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
            <div class="absolute left-3 top-2.5 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <select wire:model.live="roleFilter" class="w-full sm:w-auto px-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="all">Semua Role</option>
                @foreach($roles as $r)
                <option value="{{ $r->name }}">{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-950/60 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Nama</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Outlet Cabang</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4 font-semibold text-white">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $roleItem)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $roleItem->name === 'Admin' ? 'bg-indigo-950/60 text-indigo-400 border border-indigo-800/50' : ($roleItem->name === 'Supervisor' ? 'bg-amber-950/60 text-amber-400 border border-amber-800/50' : 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/50') }}">
                                {{ $roleItem->name }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $user->outlet->name ?? 'Semua Outlet' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/50' : 'bg-rose-950/60 text-rose-400 border border-rose-800/50' }}">
                                {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="edit({{ $user->id }})" class="text-indigo-400 hover:text-indigo-300 font-semibold text-xs">
                                Edit
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="delete({{ $user->id }})" wire:confirm="Hapus pengguna ini?" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">
                                Hapus
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4">
            <h2 class="text-lg font-bold text-white">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h2>

            <form wire:submit="save" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('name') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('email') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">{{ $userId ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password' }}</label>
                    <input type="password" wire:model="password" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    @error('password') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Role / Peran</label>
                    <select wire:model="role" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">Outlet Cabang</label>
                    <select wire:model="outlet_id" class="w-full px-4 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Outlet / Kantor Pusat</option>
                        @foreach($outlets as $out)
                        <option value="{{ $out->id }}">{{ $out->name }} ({{ $out->code }})</option>
                        @endforeach
                    </select>
                    @error('outlet_id') <span class="text-xs text-rose-400">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="user_is_active" wire:model="is_active" class="rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <label for="user_is_active" class="text-xs text-slate-300">Akun Aktif</label>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-800">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl">
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
</div>
