<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Manajemen Shift Kasir</h1>
            <p class="text-xs text-slate-400 mt-1">Buka shift, tutup shift, dan rekonsiliasi kas (PRD Section 42, 43)</p>
        </div>

        @if(!$currentShift)
            <button wire:click="openShiftModal"
                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20 transition flex items-center gap-1.5 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buka Shift Baru</span>
            </button>
        @else
            <button wire:click="openCloseShiftModal({{ $currentShift->id }})"
                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-lg shadow-rose-600/20 transition flex items-center gap-1.5 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Tutup Shift Saat Ini</span>
            </button>
        @endif
    </div>

    <!-- Active Shift Card Banner -->
    @if($currentShift)
        <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-950/40 via-slate-900 to-slate-900 border border-emerald-800/50 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Shift Anda Sedang Berjalan</span>
                </div>
                <div class="text-sm text-slate-300">
                    Dibuka pada: <strong class="text-white">{{ $currentShift->opened_at->format('d/m/Y H:i') }}</strong> oleh <strong>{{ $currentShift->cashier->name }}</strong>
                </div>
            </div>

            <div class="flex items-center gap-6 text-xs font-mono">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Modal Awal</span>
                    <span class="text-base font-bold text-white">Rp{{ number_format($currentShift->opening_cash, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase">Estimasi Kas Sistem</span>
                    <span class="text-base font-bold text-emerald-400">Rp{{ number_format($currentShift->expected_cash, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Shift History Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3">Waktu Buka / Tutup</th>
                        <th class="px-4 py-3">Modal Awal</th>
                        <th class="px-4 py-3">Ekspektasi Kas</th>
                        <th class="px-4 py-3">Kas Aktual</th>
                        <th class="px-4 py-3">Selisih</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 font-semibold text-white">{{ $shift->cashier->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">
                                <div>Buka: {{ $shift->opened_at->format('d/m/Y H:i') }}</div>
                                <div>Tutup: {{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono">Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono">Rp{{ number_format($shift->expected_cash, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-white">
                                {{ $shift->closing_cash !== null ? 'Rp' . number_format($shift->closing_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold">
                                @if($shift->closed_at)
                                    <span class="{{ $shift->difference == 0 ? 'text-emerald-400' : ($shift->difference > 0 ? 'text-cyan-400' : 'text-rose-400') }}">
                                        {{ $shift->difference > 0 ? '+' : '' }}Rp{{ number_format($shift->difference, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($shift->status === \App\Enums\ShiftStatus::Open)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold">Terbuka</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 text-[10px] font-bold">Ditutup</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat shift.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $shifts->links() }}
        </div>
    </div>

    <!-- Open Shift Modal -->
    @if($isOpenModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">Buka Shift Kasir Baru</h3>
                    <button wire:click="$set('isOpenModalOpen', false)" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="startShift" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Modal Kas Awal di Laci (Rp)</label>
                        <input type="number" wire:model="openingCash" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-base font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('openingCash') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Catatan Pembukaan (Opsional)</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Pecahan kecil disiapkan" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isOpenModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold shadow-md shadow-emerald-600/30">Mulai Shift</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Close Shift Modal -->
    @if($isCloseModalOpen && $currentShift)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-base font-bold text-white">Tutup Shift & Rekonsiliasi Kas</h3>
                    <button wire:click="$set('isCloseModalOpen', false)" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Modal Awal:</span>
                        <span class="font-mono text-white">Rp{{ number_format($currentShift->opening_cash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Ekspektasi Kas Laci (Sistem):</span>
                        <span class="font-mono text-emerald-400 font-bold">Rp{{ number_format($currentShift->expected_cash, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form wire:submit="finishShift({{ $currentShift->id }})" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Kas Aktual Dihitung di Laci (Rp)</label>
                        <input type="number" wire:model="closingCash" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-base font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('closingCash') <span class="text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Catatan Penutupan</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Selisih uang koin" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isCloseModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold shadow-md shadow-rose-600/30">Tutup & Rekonsiliasi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
