<?php

namespace App\Livewire\Sync;

use App\Models\SyncLog;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class SyncCenter extends Component
{
    use WithPagination;

    public string $statusTab = 'all'; // all, SYNCED, FAILED, PENDING

    public function render(): View
    {
        $logs = SyncLog::query()
            ->when($this->statusTab !== 'all', function ($query): void {
                $query->where('status', $this->statusTab);
            })
            ->latest()
            ->paginate(15);

        $counts = [
            'all' => SyncLog::count(),
            'synced' => SyncLog::where('status', 'SYNCED')->count(),
            'failed' => SyncLog::where('status', 'FAILED')->count(),
        ];

        return view('livewire.sync.sync-center', [
            'logs' => $logs,
            'counts' => $counts,
        ]);
    }
}
