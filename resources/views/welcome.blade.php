<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Putri — Sistem Kasir Offline-First Terpercaya, Berani, & Transparan</title>
    <meta name="description" content="Sistem Point of Sale (POS) ritel modern offline-first yang menjamin operasional kasir tetap melaju tanpa hambatan internet. Audit shift transparan dan kendali multi-outlet terpercaya.">

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-900 selection:bg-emerald-600 selection:text-white flex flex-col">

    <!-- TOPBAR NAVIGATION (Clean Square / Shopify POS Standard) -->
    <header class="border-b border-slate-200 bg-white/95 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <x-application-logo class="w-10 h-10 rounded-xl shadow-xs" />
                <div class="flex flex-col">
                    <span class="font-bold tracking-tight text-base leading-none text-slate-900 flex items-center gap-2">
                        POS PUTRI
                        <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-300 font-semibold tracking-wide">v1.0 Offline-First</span>
                    </span>
                    <span class="text-[11px] text-slate-500 font-medium leading-tight mt-0.5">Reliable • Bold • Transparent</span>
                </div>
            </div>

            <!-- Middle Navigation Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600">
                <a href="#pilar" class="hover:text-slate-900 transition">Tiga Pilar</a>
                <a href="#demo-terminal" class="hover:text-slate-900 transition">Simulasi Kasir</a>
                <a href="#arsitektur" class="hover:text-slate-900 transition">Arsitektur Offline</a>
                <a href="#demo-akun" class="hover:text-slate-900 transition">Akun Demo</a>
            </nav>

            <!-- Right Actions & Online Badge -->
            <div class="flex items-center gap-3" x-data="{ online: navigator.onLine }" x-init="window.addEventListener('online', () => online = true); window.addEventListener('offline', () => online = false)">
                <!-- Live Network Status Pill -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-medium border transition"
                    :class="online ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800'">
                    <span class="w-2 h-2 rounded-full" :class="online ? 'bg-emerald-600 animate-pulse' : 'bg-amber-500'"></span>
                    <span x-text="online ? 'SYSTEM ONLINE' : 'OFFLINE READY'"></span>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-semibold text-xs transition">
                        Dashboard
                    </a>
                    <a href="{{ route('pos.index') }}" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1.5 active:scale-95 shadow-sm">
                        <span>Buka Kasir</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1.5 active:scale-95 shadow-sm">
                        <span>Masuk Petugas / Kasir</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION: CLEAN, CRISP, COMMERCIAL GRADE -->
    <section class="relative overflow-hidden pt-16 pb-20 border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                
                <!-- Trust Eyebrow Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-800 text-xs font-mono font-semibold mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <span>100% OPERASIONAL MANDIRI TANPA KONEKSI INTERNET</span>
                </div>

                <!-- Main Bold Headline (No Neon AI Gradients!) -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15] mb-6">
                    Kasir Cepat. <br class="hidden sm:inline">
                    Transaksi Tanpa Henti. <br>
                    <span class="text-emerald-700">Transparan Sepenuhnya.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed mb-8 max-w-2xl mx-auto">
                    Sistem Point of Sale ritel modern yang dirancang untuk kecepatan kasir dan kepastian operasional. Internet padam atau sinyal drop? Kasir toko Anda tetap melayani pelanggan tanpa jeda sedetik pun.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 mb-12">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base transition flex items-center justify-center gap-2 shadow-sm active:scale-98">
                        <span>Buka Terminal Kasir</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="#demo-akun" class="w-full sm:w-auto px-6 py-3.5 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-800 font-semibold text-base transition flex items-center justify-center gap-2">
                        <span>Pilih Akun Uji Coba</span>
                    </a>
                </div>

                <!-- 3 Key Metric Highlights (Solid White Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8 border-t border-slate-200 text-left">
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="font-mono text-2xl font-bold text-slate-900 mb-1">0 Detik</div>
                        <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-1">Downtime Offline</div>
                        <p class="text-xs text-slate-600">Kasir tetap bisa scan, tambah keranjang, dan cetak struk meski router WiFi dicabut.</p>
                    </div>

                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="font-mono text-2xl font-bold text-slate-900 mb-1">&lt; 30 ms</div>
                        <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-1">Respon Input Lokal</div>
                        <p class="text-xs text-slate-600">Pencarian produk dan kalkulasi keranjang dieksekusi instan langsung di memori browser.</p>
                    </div>

                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="font-mono text-2xl font-bold text-slate-900 mb-1">100%</div>
                        <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-1">Audit Kas Terbuka</div>
                        <p class="text-xs text-slate-600">Rekonsiliasi shift tanpa celah: selisih kas fisik vs sistem tercatat transparan.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- INTERACTIVE TERMINAL SIMULATOR: AUTHENTIC SQUARE POS REGISTER INTERFACE -->
    <section id="demo-terminal" class="py-16 border-b border-slate-200 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-700">Simulasi Langsung</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Uji Coba Antarmuka Kasir</h2>
                <p class="text-sm text-slate-600 mt-2">Sentuh produk di bawah ini untuk melihat bagaimana kalkulasi harga, pajak PPN 11%, dan keranjang bekerja secara instan dan terbuka.</p>
            </div>

            <!-- Mini POS Interactive Widget (Styled like Square Terminal) -->
            <div x-data="{
                items: [
                    { id: 1, name: 'Kopi Susu Gula Aren', price: 18000, category: 'Minuman', qty: 1 },
                    { id: 2, name: 'Butter Croissant Premium', price: 22000, category: 'Pastry', qty: 1 }
                ],
                availableProducts: [
                    { id: 1, name: 'Kopi Susu Gula Aren', price: 18000, category: 'Minuman' },
                    { id: 2, name: 'Butter Croissant Premium', price: 22000, category: 'Pastry' },
                    { id: 3, name: 'Matcha Latte Oatmilk', price: 26000, category: 'Minuman' },
                    { id: 4, name: 'Almond Danish Sweet', price: 24000, category: 'Pastry' },
                    { id: 5, name: 'Earl Grey Artisan Tea', price: 20000, category: 'Minuman' }
                ],
                offlineSim: false,
                addItem(p) {
                    let existing = this.items.find(i => i.id === p.id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.items.push({ ...p, qty: 1 });
                    }
                },
                removeItem(id) {
                    this.items = this.items.filter(i => i.id !== id);
                },
                get subtotal() {
                    return this.items.reduce((acc, i) => acc + (i.price * i.qty), 0);
                },
                get tax() {
                    return Math.round(this.subtotal * 0.11);
                },
                get total() {
                    return this.subtotal + this.tax;
                },
                formatIdr(num) {
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                }
            }" class="max-w-4xl mx-auto rounded-2xl bg-white border border-slate-300 shadow-xl overflow-hidden">
                
                <!-- Terminal Header Bar -->
                <div class="px-5 py-3.5 bg-slate-100 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                        <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                        <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                        <span class="text-xs font-mono text-slate-700 ml-2 font-semibold">Terminal POS Putri — Register 01 (Outlet Utama)</span>
                    </div>

                    <!-- Offline Mode Simulator Toggle -->
                    <button type="button" @click="offlineSim = !offlineSim" class="px-3 py-1 rounded text-xs font-mono font-semibold border transition flex items-center gap-1.5"
                        :class="offlineSim ? 'bg-amber-100 border-amber-300 text-amber-900' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50'">
                        <span class="w-2 h-2 rounded-full" :class="offlineSim ? 'bg-amber-500 animate-pulse' : 'bg-emerald-600'"></span>
                        <span x-text="offlineSim ? 'Simulasi: Internet Terputus' : 'Simulasi: Online'"></span>
                    </button>
                </div>

                <!-- Terminal Body: Split Grid & Receipt -->
                <div class="grid grid-cols-1 md:grid-cols-12">
                    
                    <!-- Left: Product Quick Tap Grid (7 Cols) -->
                    <div class="md:col-span-7 p-5 border-b md:border-b-0 md:border-r border-slate-200 bg-white">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Katalog Cepat Kasir</span>
                            <span class="text-[11px] text-slate-500 font-medium">Klik item untuk menambah ke keranjang</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <template x-for="prod in availableProducts" :key="prod.id">
                                <button type="button" @click="addItem(prod)"
                                    class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/30 text-left transition active:scale-95 group">
                                    <div class="text-[10px] font-mono text-slate-500 font-medium" x-text="prod.category"></div>
                                    <div class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 truncate mt-0.5" x-text="prod.name"></div>
                                    <div class="text-xs font-mono font-extrabold text-emerald-700 mt-1.5" x-text="formatIdr(prod.price)"></div>
                                </button>
                            </template>
                        </div>

                        <!-- Offline Guarantee Note -->
                        <div class="mt-4 p-3 rounded-lg bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Transaksi di-buffer dalam <strong>IndexedDB lokal browser</strong>. Ketika koneksi pulih, data disinkronkan otomatis tanpa duplikasi.</span>
                        </div>
                    </div>

                    <!-- Right: Live Transparent Receipt (5 Cols) -->
                    <div class="md:col-span-5 p-5 bg-slate-50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-800">Tiket Belanja</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold" x-text="items.length + ' Item'"></span>
                            </div>

                            <!-- Cart Items List -->
                            <div class="divide-y divide-slate-200 max-h-48 overflow-y-auto py-2">
                                <template x-for="item in items" :key="item.id">
                                    <div class="py-2 flex items-center justify-between text-xs">
                                        <div class="truncate pr-2">
                                            <div class="font-semibold text-slate-900 truncate" x-text="item.name"></div>
                                            <div class="text-[10px] font-mono text-slate-500" x-text="item.qty + ' × ' + formatIdr(item.price)"></div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 font-mono">
                                            <span class="font-bold text-slate-900" x-text="formatIdr(item.price * item.qty)"></span>
                                            <button type="button" @click="removeItem(item.id)" class="text-slate-400 hover:text-rose-600 text-xs px-1">✕</button>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="items.length === 0" class="py-8 text-center text-xs text-slate-500">
                                    Keranjang kosong. Klik produk di sebelah kiri.
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="pt-4 border-t border-slate-200 font-mono text-xs space-y-1.5">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span class="font-medium text-slate-900" x-text="formatIdr(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>PPN (11%)</span>
                                <span class="font-medium text-slate-900" x-text="formatIdr(tax)"></span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-200">
                                <span>TOTAL TAGIHAN</span>
                                <span class="text-emerald-700 font-extrabold text-base" x-text="formatIdr(total)"></span>
                            </div>

                            <a href="{{ route('login') }}" class="mt-4 w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider text-center block transition active:scale-95 shadow-sm">
                                Buka Kasir Sungguhan →
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- TIGA PILAR: TERPERCAYA, BERANI, TRANSPARAN (CLEAN WHITE MATRIX) -->
    <section id="pilar" class="py-20 border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-700">Fondasi Sistem</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">Tiga Nilai Inti POS Putri</h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3">Dibangun tanpa kompromi untuk pemilik usaha yang membutuhkan ketahanan operasional, kecepatan eksekusi, dan akurasi keuangan mutlak.</p>
            </div>

            <!-- 3 Pillars Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- PILAR 1: TERPERCAYA -->
                <div class="p-7 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:border-emerald-500 hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-700">Pilar Kesatu</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-1 mb-3">Terpercaya (Reliable)</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Keandalan tanpa keraguan. Menggunakan kombinasi IndexedDB (Dexie.js) dan Service Worker PWA, kasir tetap melayani penjualan tanpa koneksi server. Transaksi tersimpan lokal dengan UUID aman, dan tersinkronisasi otomatis dengan mekanisme rekonsiliasi bebas duplikasi.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-200 font-mono text-xs text-slate-500 flex items-center justify-between">
                        <span>Ketahanan Jaringan</span>
                        <span class="text-emerald-700 font-bold">100% Offline Capable</span>
                    </div>
                </div>

                <!-- PILAR 2: BERANI -->
                <div class="p-7 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:border-emerald-500 hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-800 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-700">Pilar Kedua</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-1 mb-3">Berani (Bold)</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Kecepatan dan ergonomi kasir tanpa kompromi. Antarmuka dirancang dengan kepadatan informasi optimal, ukuran sentuh standar 44px, pemindai barcode USB instan, dan numpad taktil. Tanpa animasi lambat atau komponen dekoratif yang membuang waktu antrean kasir.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-200 font-mono text-xs text-slate-500 flex items-center justify-between">
                        <span>Waktu Transaksi</span>
                        <span class="text-slate-900 font-bold">&lt; 5 Detik per Pelanggan</span>
                    </div>
                </div>

                <!-- PILAR 3: TRANSPARAN -->
                <div class="p-7 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:border-emerald-500 hover:shadow-md transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-700">Pilar Ketiga</span>
                        <h3 class="text-xl font-bold text-slate-900 mt-1 mb-3">Transparan (Transparent)</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Audit keuangan dan stok terbuka hingga ke satuan terkecil. Setiap rupiah uang kas awal, uang masuk penjualan tunai, dan non-tunai diverifikasi saat penutupan shift. Riwayat pergerakan stok (Stock Movement) mencatat masuk-keluar barang secara akuntabel.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-200 font-mono text-xs text-slate-500 flex items-center justify-between">
                        <span>Integritas Audit</span>
                        <span class="text-emerald-700 font-bold">100% Traceable</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ARSITEKTUR OFFLINE-FIRST DETAIL -->
    <section id="arsitektur" class="py-16 border-b border-slate-200 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-700">Arsitektur Tangguh</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Dual-Storage Berstandar Enterprise</h2>
                <p class="text-sm text-slate-600 mt-2">Dua lapis penyimpanan sinkron yang melindungi kelangsungan bisnis dan data transaksi kasir toko Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 font-mono text-xs font-bold">Layer 1: Browser Storage</span>
                        <span class="text-xs text-slate-500 font-medium">IndexedDB (Dexie.js) + PWA</span>
                    </div>
                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Katalog 100+ produk & stok lokal di-cache ke IndexedDB untuk akses instan &lt; 20ms.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Keranjang belanja, transaksi baru, dan nomor invoice di-generate secara otonom di terminal.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Aset antarmuka (JS, CSS, Font) dicache via Service Worker, kasir dapat dibuka tanpa koneksi server.</span>
                        </li>
                    </ul>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded bg-slate-200 text-slate-800 font-mono text-xs font-bold">Layer 2: Cloud Backend</span>
                        <span class="text-xs text-slate-500 font-medium">Laravel Engine + MySQL</span>
                    </div>
                    <ul class="space-y-3 text-xs text-slate-700">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Endpoint REST API sinkronisasi dengan idempotensi penuh via `uuid` transaksi unik.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Multi-outlet sync: mendistribusikan pembaruan stok ke cabang lain secara terpusat.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>Manajemen peran (Spatie RBAC): Hak akses ketat untuk Kasir, Supervisor, dan Administrator.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- DEMO CREDENTIALS: TRANSPARAN & 1-CLICK ACCESS -->
    <section id="demo-akun" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-700">Akses Langsung</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Coba Langsung Semua Peran Pengguna</h2>
                <p class="text-sm text-slate-600 mt-2">Pilih salah satu peran di bawah ini untuk menguji antarmuka dan hak akses yang disesuaikan secara spesifik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                
                <!-- Role: Admin -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-800 text-xs font-mono font-bold">ROLE: ADMIN</span>
                            <span class="text-[10px] text-slate-500 font-mono font-semibold">Full Access</span>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 mb-2">Administrator Toko</h4>
                        <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                            Kendali penuh seluruh sistem: manajemen multi-outlet, penambahan staf kasir, pengaturan pajak, dan laporan laba-rugi.
                        </p>
                        <div class="p-3 rounded-lg bg-white font-mono text-xs space-y-1 text-slate-700 border border-slate-200">
                            <div>Email: <span class="text-slate-900 font-bold">admin@posputri.test</span></div>
                            <div>Password: <span class="text-emerald-700 font-bold">password</span></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="email" value="admin@posputri.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm active:scale-98">
                            Masuk Sebagai Admin →
                        </button>
                    </form>
                </div>

                <!-- Role: Supervisor -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-900 text-xs font-mono font-bold">ROLE: SUPERVISOR</span>
                            <span class="text-[10px] text-slate-500 font-mono font-semibold">Operations</span>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 mb-2">Supervisor Operasional</h4>
                        <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                            Manajemen inventaris, audit stok opname, persetujuan penutupan shift kasir, dan pantauan performa penjualan harian.
                        </p>
                        <div class="p-3 rounded-lg bg-white font-mono text-xs space-y-1 text-slate-700 border border-slate-200">
                            <div>Email: <span class="text-slate-900 font-bold">supervisor@posputri.test</span></div>
                            <div>Password: <span class="text-emerald-700 font-bold">password</span></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="email" value="supervisor@posputri.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm active:scale-98">
                            Masuk Sebagai Supervisor →
                        </button>
                    </form>
                </div>

                <!-- Role: Cashier -->
                <div class="p-6 rounded-2xl bg-emerald-50/50 border-2 border-emerald-500/80 flex flex-col justify-between shadow-md">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-800 text-xs font-mono font-bold">ROLE: KASIR</span>
                            <span class="text-[10px] text-emerald-700 font-mono font-bold">Frontliner</span>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 mb-2">Petugas Kasir Toko</h4>
                        <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                            Antarmuka kasir cepat, buka & tutup shift, transaksi tunai & non-tunai (QRIS), pencarian pelanggan, dan cetak struk thermal 58mm.
                        </p>
                        <div class="p-3 rounded-lg bg-white font-mono text-xs space-y-1 text-slate-700 border border-emerald-200">
                            <div>Email: <span class="text-slate-900 font-bold">kasir@posputri.test</span></div>
                            <div>Password: <span class="text-emerald-700 font-bold">password</span></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="email" value="kasir@posputri.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm active:scale-98">
                            Masuk Sebagai Kasir (Mulai Jualan) →
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER: CLEAN, PROFESSIONAL, SQUARE/SHOPIFY STANDARD -->
    <footer class="border-t border-slate-200 bg-white py-10 mt-auto text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <x-application-logo class="w-6 h-6 rounded-lg" />
                <span class="text-slate-900 font-bold">POS Putri v1.0</span>
                <span>— Dirancang untuk Kecepatan Kasir, Ketahanan Offline, dan Transparansi Data.</span>
            </div>
            <div class="flex items-center gap-6">
                <span>IndexedDB Dexie.js</span>
                <span>PWA Service Worker</span>
                <span>Laravel Engine</span>
                <a href="{{ route('login') }}" class="text-emerald-700 font-semibold hover:underline">Masuk Petugas</a>
            </div>
        </div>
    </footer>

</body>
</html>
