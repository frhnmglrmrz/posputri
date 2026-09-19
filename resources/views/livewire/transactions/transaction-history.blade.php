<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Riwayat Transaksi</h1>
            <p class="text-xs text-slate-400 mt-1">Daftar transaksi kasir online & offline yang telah tersinkron (PRD Section 24, 25)</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <div class="flex-1 max-w-sm">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no transaksi atau UUID..."
                class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="w-40">
            <select wire:model.live="statusFilter" class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="all">Semua Status</option>
                <option value="completed">Selesai</option>
                <option value="void">Dibatalkan (Void)</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">No Transaksi / UUID</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Kasir</th>
                        <th class="px-4 py-3">Total Belanja</th>
                        <th class="px-4 py-3">Metode Bayar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 font-mono">
                                <div class="font-bold text-white">{{ $tx->transaction_number }}</div>
                                <div class="text-[10px] text-slate-500 truncate max-w-xs">{{ $tx->uuid }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-400 font-mono text-[11px]">{{ $tx->transaction_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-200">{{ $tx->cashier->name ?? 'Kasir' }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-emerald-400">Rp{{ number_format($tx->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 uppercase text-[10px] font-bold text-slate-300">
                                {{ $tx->payments->pluck('payment_method')->join(', ') ?: 'Cash' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($tx->status === \App\Enums\TransactionStatus::Completed)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-bold">Selesai</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-bold">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="viewDetails({{ $tx->id }})" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-semibold transition">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada transaksi tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($isDetailModalOpen && $selectedTransaction)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-white">Detail Transaksi</h3>
                        <p class="text-[11px] text-slate-400 font-mono">{{ $selectedTransaction->transaction_number }}</p>
                    </div>
                    <button wire:click="$set('isDetailModalOpen', false)" class="text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Items -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Daftar Produk</h4>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 divide-y divide-slate-800/80 text-xs">
                        @foreach($selectedTransaction->items as $item)
                            <div class="py-2 first:pt-0 last:pb-0 flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-white">{{ $item->product_name }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="font-bold font-mono text-slate-200">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Totals -->
                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-1 text-xs">
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal:</span>
                        <span class="font-mono text-white">Rp{{ number_format($selectedTransaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($selectedTransaction->discount > 0)
                        <div class="flex justify-between text-rose-400">
                            <span>Diskon:</span>
                            <span class="font-mono">-Rp{{ number_format($selectedTransaction->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-sm text-white pt-1 border-t border-slate-800">
                        <span>Total:</span>
                        <span class="font-mono text-emerald-400">Rp{{ number_format($selectedTransaction->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400 pt-1">
                        <span>Dibayar:</span>
                        <span class="font-mono text-slate-200">Rp{{ number_format($selectedTransaction->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Kembalian:</span>
                        <span class="font-mono text-amber-400">Rp{{ number_format($selectedTransaction->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Void Action (PRD Section 15: Supervisor/Admin can void) -->
                @hasanyrole('Admin|Supervisor')
                    @if($selectedTransaction->status === \App\Enums\TransactionStatus::Completed)
                        <div class="pt-3 border-t border-slate-800 space-y-2">
                            <label class="block text-xs font-semibold text-rose-400">Batalkan Transaksi (Void) & Kembalikan Stok</label>
                            <input type="text" wire:model="voidReason" placeholder="Alasan pembatalan..." class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <button type="button" wire:click="voidTransaction({{ $selectedTransaction->id }})" wire:confirm="Batalkan transaksi ini dan kembalikan stok?" class="w-full py-2 px-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition">
                                Batalkan Transaksi (Void)
                            </button>
                        </div>
                    @endif
                @endhasanyrole
            </div>
        </div>
    @endif
</div>
