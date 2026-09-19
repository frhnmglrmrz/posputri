@extends('layouts.app')

@section('content')
<div class="p-8 rounded-2xl bg-white border border-slate-200 text-center max-w-xl mx-auto my-12 shadow-xs">
    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 mb-4">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </div>
    <h2 class="text-xl font-bold text-slate-900 mb-2">{{ $title ?? 'Modul' }}</h2>
    <p class="text-sm text-slate-500 mb-6">Modul ini siap dikembangkan pada fase berikutnya sesuai roadmap PRD.</p>
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-xs font-semibold transition">
        <span>Kembali ke Dashboard</span>
    </a>
</div>
@endsection
