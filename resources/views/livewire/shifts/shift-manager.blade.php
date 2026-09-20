<div>
    <x-alert />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Manajemen Shift Kasir</h1>
            <p class="text-xs text-slate-500 mt-1">Buka shift, tutup shift, dan rekonsiliasi kas (PRD Section 42, 43)</p>
        </div>

        @if(!$currentShift)
            <button wire:click="openShiftModal"
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buka Shift Baru</span>
            </button>
        @else
            <button wire:click="openCloseShiftModal({{ $currentShift->id }})"
                class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto active:scale-98">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Tutup Shift Saat Ini</span>
            </button>
        @endif
    </div>

    <!-- Active Shift Card Banner -->
    @if($currentShift)
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-300 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Shift Anda Sedang Berjalan</span>
                </div>
                <div class="text-xs text-slate-700">
                    Dibuka pada: <strong class="text-slate-900">{{ $currentShift->opened_at->format('d/m/Y H:i') }}</strong> oleh <strong>{{ $currentShift->cashier->name }}</strong>
                </div>
            </div>

            <div class="flex items-center gap-6 text-xs font-mono">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-sans font-semibold">Modal Awal</span>
                    <span class="text-base font-bold text-slate-900">Rp{{ number_format($currentShift->opening_cash, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-emerald-700 block text-[10px] uppercase font-sans font-semibold">Estimasi Kas Sistem</span>
                    <span class="text-base font-bold text-emerald-700">Rp{{ number_format($currentShift->expected_cash, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Shift History Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($shifts as $shift)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $shift->cashier->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-[11px]">
                                <div>Buka: {{ $shift->opened_at->format('d/m/Y H:i') }}</div>
                                <div>Tutup: {{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">Rp{{ number_format($shift->opening_cash, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono text-slate-600">Rp{{ number_format($shift->expected_cash, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                {{ $shift->closing_cash !== null ? 'Rp' . number_format($shift->closing_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold">
                                @if($shift->closed_at)
                                    <span class="{{ $shift->difference == 0 ? 'text-emerald-700' : ($shift->difference > 0 ? 'text-cyan-700' : 'text-rose-600') }}">
                                        {{ $shift->difference > 0 ? '+' : '' }}Rp{{ number_format($shift->difference, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($shift->status === \App\Enums\ShiftStatus::Open)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 text-[10px] font-bold">Terbuka</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold">Ditutup</span>
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
        <div class="p-4 border-t border-slate-200 bg-white">
            {{ $shifts->links() }}
        </div>
    </div>

    <!-- Open Shift Modal -->
    @if($isOpenModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-xl p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Buka Shift Kasir Baru</h3>
                    <button wire:click="$set('isOpenModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="startShift" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Modal Kas Awal di Laci (Rp)</label>
                        <input type="number" wire:model="openingCash" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono text-base font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('openingCash') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Pembukaan (Opsional)</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Pecahan kecil disiapkan" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isOpenModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-xs">Mulai Shift</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Close Shift Modal -->
    @if($isCloseModalOpen && $currentShift)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-xl p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Tutup Shift & Rekonsiliasi Kas</h3>
                    <button wire:click="$set('isCloseModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Modal Awal:</span>
                        <span class="font-mono font-semibold text-slate-900">Rp{{ number_format($currentShift->opening_cash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Ekspektasi Kas Laci (Sistem):</span>
                        <span class="font-mono font-bold text-emerald-700">Rp{{ number_format($currentShift->expected_cash, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form wire:submit="endShift" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kas Aktual Fisik di Laci (Rp)</label>
                        <input type="number" wire:model.live="closingCash" placeholder="Hitung uang tunai fisik..." class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono text-base font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('closingCash') <span class="text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($closingCash !== null && $closingCash !== '')
                        @php
                            $diff = (float)$closingCash - (float)$currentShift->expected_cash;
                        @endphp
                        <div class="p-3 rounded-xl border text-xs flex justify-between items-center {{ $diff == 0 ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : ($diff > 0 ? 'bg-cyan-50 border-cyan-300 text-cyan-800' : 'bg-rose-50 border-rose-300 text-rose-800') }}">
                            <span class="font-medium">Perhitungan Selisih:</span>
                            <span class="font-mono font-bold">{{ $diff > 0 ? '+' : '' }}Rp{{ number_format($diff, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Penutupan</label>
                        <input type="text" wire:model="notes" placeholder="Contoh: Selisih uang receh kembalian" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex justify-end gap-2">
                        <button type="button" wire:click="$set('isCloseModalOpen', false)" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-xs">Konfirmasi Tutup Shift</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
