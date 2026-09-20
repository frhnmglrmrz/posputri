<div>
    <x-alert />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Sync Center (Pusat Sinkronisasi)</h1>
            <p class="text-xs text-slate-500 mt-1">Monitoring sinkronisasi transaksi offline ke server MySQL (PRD Section 62)</p>
        </div>

        <button type="button"
            onclick="window.posSyncEngine && window.posSyncEngine.syncPendingTransactions()"
            class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition flex items-center gap-2 self-start sm:self-auto active:scale-98">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Sinkronkan Sekarang</span>
        </button>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200 mb-4 max-w-fit">
        <button type="button" wire:click="$set('statusTab', 'all')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="$wire.statusTab === 'all' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'">
            Semua ({{ $counts['all'] }})
        </button>
        <button type="button" wire:click="$set('statusTab', 'SYNCED')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="$wire.statusTab === 'SYNCED' ? 'bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold' : 'text-slate-600 hover:text-slate-900'">
            Synced ({{ $counts['synced'] }})
        </button>
        <button type="button" wire:click="$set('statusTab', 'FAILED')"
            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="$wire.statusTab === 'FAILED' ? 'bg-rose-50 text-rose-800 border border-rose-300 font-bold' : 'text-slate-600 hover:text-slate-900'">
            Failed ({{ $counts['failed'] }})
        </button>
    </div>

    <!-- Logs Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Waktu Sync</th>
                        <th class="px-4 py-3">Entitas</th>
                        <th class="px-4 py-3">UUID Entitas</th>
                        <th class="px-4 py-3">Operasi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Pesan Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 text-slate-500 font-mono text-[11px]">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $log->entity_type }}</td>
                            <td class="px-4 py-3 font-mono text-slate-500 text-[11px]">{{ $log->entity_uuid }}</td>
                            <td class="px-4 py-3 uppercase text-[10px] font-bold text-slate-700">{{ $log->operation }}</td>
                            <td class="px-4 py-3">
                                @if($log->status === 'SYNCED')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 text-[10px] font-bold">SYNCED</span>
                                @elseif($log->status === 'PENDING')
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-300 text-[10px] font-bold">PENDING</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-300 text-[10px] font-bold">FAILED</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-rose-600 text-[11px] truncate max-w-xs">{{ $log->error_message ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat sinkronisasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 bg-white">
            {{ $logs->links() }}
        </div>
    </div>
</div>
