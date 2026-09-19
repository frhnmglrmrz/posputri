<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-950 text-zinc-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Putri — Sistem Kasir Offline-First Terpercaya, Berani, & Transparan</title>
    <meta name="description" content="Sistem Point of Sale (POS) ritel modern offline-first yang menjamin operasional kasir tetap melaju tanpa hambatan internet. Audit shift transparan dan kendali multi-outlet terpercaya.">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#09090b">

    <!-- Fonts & Scripts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-zinc-950 text-zinc-100 selection:bg-emerald-500 selection:text-black flex flex-col">

    <!-- TOPBAR NAVIGATION -->
    <header class="border-b border-zinc-800/80 bg-zinc-950/90 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-500 text-zinc-950 flex items-center justify-center font-bold text-lg shadow-sm shadow-emerald-500/20">
                    P
                </div>
                <div class="flex flex-col">
                    <span class="font-bold tracking-tight text-base leading-none text-white flex items-center gap-2">
                        POS PUTRI
                        <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded bg-zinc-800 text-emerald-400 border border-zinc-700/60 font-semibold tracking-wide">v1.0 Offline-First</span>
                    </span>
                    <span class="text-[11px] text-zinc-400 leading-tight mt-0.5">Reliable • Bold • Transparent</span>
                </div>
            </div>

            <!-- Middle Navigation Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-zinc-400">
                <a href="#pilar" class="hover:text-white transition">Tiga Pilar</a>
                <a href="#demo-terminal" class="hover:text-white transition">Simulasi Kasir</a>
                <a href="#arsitektur" class="hover:text-white transition">Arsitektur Offline</a>
                <a href="#demo-akun" class="hover:text-white transition">Akun Demo</a>
            </nav>

            <!-- Right Actions & Online Badge -->
            <div class="flex items-center gap-3" x-data="{ online: navigator.onLine }" x-init="window.addEventListener('online', () => online = true); window.addEventListener('offline', () => online = false)">
                <!-- Live Network Status Pill -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-medium border transition"
                    :class="online ? 'bg-emerald-950/50 border-emerald-800 text-emerald-300' : 'bg-amber-950/50 border-amber-800 text-amber-300'">
                    <span class="w-2 h-2 rounded-full" :class="online ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'"></span>
                    <span x-text="online ? 'SYSTEM ONLINE' : 'OFFLINE CAPABLE'"></span>
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white font-medium text-sm transition">
                        Dashboard
                    </a>
                    <a href="{{ route('pos.index') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-semibold text-sm transition flex items-center gap-1.5 active:scale-95 shadow-sm shadow-emerald-500/20">
                        <span>Buka Kasir</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white font-medium text-sm transition">
                        Masuk Petugas
                    </a>
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-semibold text-sm transition flex items-center gap-1.5 active:scale-95 shadow-sm shadow-emerald-500/20">
                        <span>Mulai Transaksi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION: BOLD, CONFIDENT & DIRECT -->
    <section class="relative overflow-hidden pt-16 pb-20 border-b border-zinc-900 bg-gradient-to-b from-zinc-950 via-zinc-950 to-zinc-900/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                
                <!-- Trust Eyebrow Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-emerald-500/30 bg-emerald-950/30 text-emerald-400 text-xs font-mono font-medium mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>100% OPERASIONAL TANPA KONEKSI INTERNET</span>
                </div>

                <!-- Main Bold Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.1] mb-6">
                    Kasir Cepat. <br class="hidden sm:inline">
                    Transaksi Tanpa Henti. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">100% Transparan.</span>
                </h1>

                <!-- Subheadline -->
                <p class="text-base sm:text-lg text-zinc-400 leading-relaxed mb-8 max-w-2xl mx-auto">
                    Sistem Point of Sale generasi baru yang dirancang untuk kecepatan ritel. Internet padam atau jaringan drop? Kasir Anda tetap melayani pelanggan tanpa jeda sedetik pun.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 mb-12">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold text-base transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20 active:scale-98">
                        <span>Buka Terminal Kasir</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="#demo-akun" class="w-full sm:w-auto px-6 py-3.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 border border-zinc-700/80 text-zinc-200 font-semibold text-base transition flex items-center justify-center gap-2">
                        <span>Pilih Akun Uji Coba</span>
                    </a>
                </div>

                <!-- 3 Key Metric Highlights -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8 border-t border-zinc-900 text-left">
                    <div class="p-4 rounded-xl bg-zinc-900/50 border border-zinc-800/80">
                        <div class="font-mono text-2xl font-bold text-white mb-1">0 Detik</div>
                        <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-1">Downtime Offline</div>
                        <p class="text-xs text-zinc-400">Kasir tetap bisa scan, tambah item, dan bayar meski router WiFi dicabut.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-zinc-900/50 border border-zinc-800/80">
                        <div class="font-mono text-2xl font-bold text-white mb-1">&lt; 30 ms</div>
                        <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-1">Respon Input Lokal</div>
                        <p class="text-xs text-zinc-400">Pencarian produk dan kalkulasi keranjang dieksekusi instan di memori browser.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-zinc-900/50 border border-zinc-800/80">
                        <div class="font-mono text-2xl font-bold text-white mb-1">100%</div>
                        <div class="text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-1">Audit Kas Terbuka</div>
                        <p class="text-xs text-zinc-400">Rekonsiliasi shift tanpa celah: selisih kas fisik vs sistem dicatat transparan.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- INTERACTIVE TERMINAL SIMULATOR: BOLD & TRANSPARENT PROOF -->
    <section id="demo-terminal" class="py-16 border-b border-zinc-900 bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">Transparansi Nyata</span>
                <h2 class="text-3xl font-bold text-white mt-1">Coba Langsung Simulasi Kasir</h2>
                <p class="text-sm text-zinc-400 mt-2">Klik produk di bawah ini untuk melihat bagaimana kalkulasi harga, pajak PPN 11%, dan keranjang bekerja secara instan dan terbuka.</p>
            </div>

            <!-- Mini POS Interactive Widget -->
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
            }" class="max-w-4xl mx-auto rounded-2xl bg-zinc-900 border border-zinc-800 shadow-2xl overflow-hidden">
                
                <!-- Terminal Header Bar -->
                <div class="px-5 py-3.5 bg-zinc-950 border-b border-zinc-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                        <span class="text-xs font-mono text-zinc-400 ml-2 font-medium">Terminal POS Putri — Outlet Utama</span>
                    </div>

                    <!-- Offline Mode Simulator Toggle -->
                    <button type="button" @click="offlineSim = !offlineSim" class="px-3 py-1 rounded text-xs font-mono font-semibold border transition flex items-center gap-1.5"
                        :class="offlineSim ? 'bg-amber-950/70 border-amber-700 text-amber-300' : 'bg-zinc-800 border-zinc-700 text-zinc-300 hover:text-white'">
                        <span class="w-2 h-2 rounded-full" :class="offlineSim ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400'"></span>
                        <span x-text="offlineSim ? 'Simulasi: Internet Putus' : 'Simulasi: Normal Online'"></span>
                    </button>
                </div>

                <!-- Terminal Body: Split Grid & Receipt -->
                <div class="grid grid-cols-1 md:grid-cols-12">
                    
                    <!-- Left: Product Quick Tap Grid (7 Cols) -->
                    <div class="md:col-span-7 p-5 border-b md:border-b-0 md:border-r border-zinc-800 bg-zinc-900/60">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Katalog Cepat Kasir</span>
                            <span class="text-[11px] text-zinc-500">Klik item untuk menambah</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <template x-for="prod in availableProducts" :key="prod.id">
                                <button type="button" @click="addItem(prod)"
                                    class="p-3 rounded-xl bg-zinc-950 border border-zinc-800 hover:border-emerald-500/60 text-left transition active:scale-95 group">
                                    <div class="text-[10px] font-mono text-zinc-500" x-text="prod.category"></div>
                                    <div class="text-xs font-semibold text-white group-hover:text-emerald-300 truncate" x-text="prod.name"></div>
                                    <div class="text-xs font-mono font-bold text-emerald-400 mt-1" x-text="formatIdr(prod.price)"></div>
                                </button>
                            </template>
                        </div>

                        <!-- Offline Guarantee Note -->
                        <div class="mt-4 p-3 rounded-lg bg-zinc-950/80 border border-zinc-800 text-[11px] text-zinc-400 flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Status transaksi di-buffer dalam <strong>IndexedDB browser</strong>. Ketika koneksi internet pulih, sistem secara otomatis mengeksekusi sinkronisasi dengan idempotensi penuh.</span>
                        </div>
                    </div>

                    <!-- Right: Live Transparent Receipt (5 Cols) -->
                    <div class="md:col-span-5 p-5 bg-zinc-950 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                                <span class="text-xs font-bold uppercase tracking-wider text-white">Struk Transparan</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-zinc-800 text-zinc-300" x-text="items.length + ' Baris'"></span>
                            </div>

                            <!-- Cart Items List -->
                            <div class="divide-y divide-zinc-900 max-h-48 overflow-y-auto py-2">
                                <template x-for="item in items" :key="item.id">
                                    <div class="py-2 flex items-center justify-between text-xs">
                                        <div class="truncate pr-2">
                                            <div class="font-medium text-white truncate" x-text="item.name"></div>
                                            <div class="text-[10px] font-mono text-zinc-400" x-text="item.qty + ' × ' + formatIdr(item.price)"></div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 font-mono">
                                            <span class="font-semibold text-zinc-200" x-text="formatIdr(item.price * item.qty)"></span>
                                            <button type="button" @click="removeItem(item.id)" class="text-zinc-500 hover:text-rose-400 text-xs px-1">✕</button>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="items.length === 0" class="py-8 text-center text-xs text-zinc-500">
                                    Keranjang kosong. Klik produk di sebelah kiri.
                                </div>
                            </div>
                        </div>

                        <!-- Price Breakdown (Radical Transparency) -->
                        <div class="pt-4 border-t border-zinc-800/80 font-mono text-xs space-y-1.5">
                            <div class="flex justify-between text-zinc-400">
                                <span>Subtotal Barang</span>
                                <span x-text="formatIdr(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-zinc-400">
                                <span>PPN Transparan (11%)</span>
                                <span x-text="formatIdr(tax)"></span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-zinc-800">
                                <span>TOTAL AKHIR</span>
                                <span class="text-emerald-400" x-text="formatIdr(total)"></span>
                            </div>

                            <a href="{{ route('login') }}" class="mt-4 w-full py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold text-xs uppercase tracking-wider text-center block transition active:scale-95 shadow-md shadow-emerald-500/10">
                                Buka Kasir Sungguhan →
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- TIGA PILAR: TERPERCAYA, BERANI, TRANSPARAN (BENTO GRID) -->
    <section id="pilar" class="py-20 border-b border-zinc-900 bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">Filosofi Inti</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2 tracking-tight">Tiga Pilar Utama POS Putri</h2>
                <p class="text-sm sm:text-base text-zinc-400 mt-3">Dibangun tanpa kompromi untuk pengusaha yang menghargai ketahanan operasional, kecepatan eksekusi, dan kejujuran data.</p>
            </div>

            <!-- Bento Grid 3 Pillars -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- PILAR 1: TERPERCAYA -->
                <div class="p-7 rounded-2xl bg-zinc-900/70 border border-zinc-800 flex flex-col justify-between hover:border-emerald-500/40 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-950/60 border border-emerald-800/60 text-emerald-400 flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-400">Pilar Kesatu</span>
                        <h3 class="text-xl font-bold text-white mt-1 mb-3">Terpercaya (Reliable)</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Keandalan tanpa keraguan. Menggunakan kombinasi IndexedDB (Dexie.js) dan Service Worker PWA, kasir tetap melayani penjualan tanpa koneksi server. Transaksi tersimpan lokal dengan UUID aman, dan tersinkronisasi otomatis dengan mekanisme rekonsiliasi bebas duplikasi.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-zinc-800/80 font-mono text-xs text-zinc-400 flex items-center justify-between">
                        <span>Ketahanan Jaringan</span>
                        <span class="text-emerald-400 font-bold">100% Offline Capable</span>
                    </div>
                </div>

                <!-- PILAR 2: BERANI -->
                <div class="p-7 rounded-2xl bg-zinc-900/70 border border-zinc-800 flex flex-col justify-between hover:border-emerald-500/40 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-zinc-800/80 border border-zinc-700 text-white flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-300">Pilar Kedua</span>
                        <h3 class="text-xl font-bold text-white mt-1 mb-3">Berani (Bold)</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Kecepatan dan ergonomi kasir tanpa basa-basi. Antarmuka dirancang dengan kepadatan informasi optimal, ukuran sentuh standar 44px, pemindai barcode USB instan, dan numpad taktil. Tanpa animasi lambat atau komponen dekoratif yang membuang waktu antrean kasir.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-zinc-800/80 font-mono text-xs text-zinc-400 flex items-center justify-between">
                        <span>Waktu Transaksi</span>
                        <span class="text-white font-bold">&lt; 5 Detik per Pelanggan</span>
                    </div>
                </div>

                <!-- PILAR 3: TRANSPARAN -->
                <div class="p-7 rounded-2xl bg-zinc-900/70 border border-zinc-800 flex flex-col justify-between hover:border-emerald-500/40 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-950/60 border border-emerald-800/60 text-emerald-400 flex items-center justify-center mb-6 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-400">Pilar Ketiga</span>
                        <h3 class="text-xl font-bold text-white mt-1 mb-3">Transparan (Transparent)</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Audit keuangan dan stok terbuka hingga ke satuan terkecil. Setiap rupiah uang kas awal, uang masuk penjualan tunai, dan non-tunai diverifikasi saat penutupan shift. Riwayat pergerakan stok (Stock Movement) mencatat masuk-keluar barang secara akuntabel.
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-zinc-800/80 font-mono text-xs text-zinc-400 flex items-center justify-between">
                        <span>Integritas Audit</span>
                        <span class="text-emerald-400 font-bold">100% Traceable</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ARSITEKTUR OFFLINE-FIRST DETAIL -->
    <section id="arsitektur" class="py-16 border-b border-zinc-900 bg-zinc-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">Teknologi Modern</span>
                <h2 class="text-3xl font-bold text-white mt-1">Arsitektur Dual-Storage Berstandar Enterprise</h2>
                <p class="text-sm text-zinc-400 mt-2">Dua lapis penyimpanan yang bekerja sinkron untuk melindungi setiap rupiah dan data transaksi toko Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="p-6 rounded-2xl bg-zinc-900 border border-zinc-800">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-400 font-mono text-xs font-bold">Layer 1: Browser Storage</span>
                        <span class="text-xs text-zinc-400">IndexedDB (Dexie.js) + PWA</span>
                    </div>
                    <ul class="space-y-3 text-xs text-zinc-300">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Katalog produk lengkap & stok lokal di-cache ke IndexedDB untuk akses instan &lt; 20ms.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Keranjang belanja, transaksi baru, dan nomor invoice di-generate secara otonom di kasir.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Aset antarmuka (JS, CSS, Icon) dicache via Service Worker, bisa dibuka tanpa koneksi server.</span>
                        </li>
                    </ul>
                </div>

                <div class="p-6 rounded-2xl bg-zinc-900 border border-zinc-800">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 rounded bg-zinc-800 text-white font-mono text-xs font-bold">Layer 2: Cloud Backend</span>
                        <span class="text-xs text-zinc-400">Laravel 13 + MySQL Engine</span>
                    </div>
                    <ul class="space-y-3 text-xs text-zinc-300">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Endpoint REST API sinkronisasi dengan idempotensi penuh via `uuid` transaksi unik.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Multi-outlet sync: mendistribusikan pembaruan stok ke cabang lain secara terpusat.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 font-bold">✓</span>
                            <span>Manajemen peran (Spatie RBAC): Hak akses ketat untuk Kasir, Supervisor, dan Administrator.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- DEMO CREDENTIALS: TRANSPARAN & 1-CLICK ACCESS -->
    <section id="demo-akun" class="py-20 bg-zinc-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-400">Akses Terbuka</span>
                <h2 class="text-3xl font-bold text-white mt-1">Coba Langsung Semua Peran Pengguna</h2>
                <p class="text-sm text-zinc-400 mt-2">Pilih salah satu peran di bawah ini untuk menguji antarmuka dan hak akses yang disesuaikan secara spesifik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                
                <!-- Role: Admin -->
                <div class="p-6 rounded-2xl bg-zinc-900 border border-zinc-800 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-mono font-bold">ROLE: ADMIN</span>
                            <span class="text-[10px] text-zinc-500 font-mono">Full Access</span>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Administrator Toko</h4>
                        <p class="text-xs text-zinc-400 mb-4">
                            Kendali penuh terhadap seluruh sistem: manajemen multi-outlet, penambahan user & kasir, pengaturan pajak, serta laporan komprehensif.
                        </p>
                        <div class="p-3 rounded-lg bg-zinc-950 font-mono text-xs space-y-1 text-zinc-300 border border-zinc-800">
                            <div>Email: <span class="text-white font-semibold">admin@posputri.test</span></div>
                            <div>Password: <span class="text-emerald-400">password</span></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="email" value="admin@posputri.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-white font-semibold text-xs transition border border-zinc-700 active:scale-98">
                            Masuk Sebagai Admin →
                        </button>
                    </form>
                </div>

                <!-- Role: Supervisor -->
                <div class="p-6 rounded-2xl bg-zinc-900 border border-zinc-800 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-mono font-bold">ROLE: SUPERVISOR</span>
                            <span class="text-[10px] text-zinc-500 font-mono">Operations</span>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Supervisor Operasional</h4>
                        <p class="text-xs text-zinc-400 mb-4">
                            Manajemen inventaris, audit stok masuk/keluar, persetujuan penutupan shift kasir, dan analitik performa penjualan harian.
                        </p>
                        <div class="p-3 rounded-lg bg-zinc-950 font-mono text-xs space-y-1 text-zinc-300 border border-zinc-800">
                            <div>Email: <span class="text-white font-semibold">supervisor@posputri.test</span></div>
                            <div>Password: <span class="text-emerald-400">password</span></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="email" value="supervisor@posputri.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-white font-semibold text-xs transition border border-zinc-700 active:scale-98">
                            Masuk Sebagai Supervisor →
                        </button>
                    </form>
                </div>

                <!-- Role: Cashier -->
                <div class="p-6 rounded-2xl bg-zinc-900 border border-emerald-500/50 shadow-lg shadow-emerald-500/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-mono font-bold">ROLE: KASIR</span>
                            <span class="text-[10px] text-emerald-400 font-mono">Frontliner</span>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Petugas Kasir Toko</h4>
                        <p class="text-xs text-zinc-400 mb-4">
                            Antarmuka kasir cepat, buka & tutup shift, transaksi tunai & non-tunai (QRIS), pencarian pelanggan, dan cetak struk thermal 58mm.
                        </p>
                        <div class="p-3 rounded-lg bg-zinc-950 font-mono text-xs space-y-1 text-zinc-300 border border-zinc-800">
                            <div>Email: <span class="text-white font-semibold">kasir@posputri.test</span></div>
                            <div>Password: <span class="text-emerald-400">password</span></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="mt-5">
                        @csrf
                        <input type="hidden" name="email" value="kasir@posputri.test">
                        <input type="hidden" name="password" value="password">
                        <button type="submit" class="w-full py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold text-xs transition shadow-sm shadow-emerald-500/20 active:scale-98">
                            Masuk Sebagai Kasir (Mulai Jualan) →
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER: MINIMAL, PROFESSIONAL, BOLD -->
    <footer class="border-t border-zinc-900 bg-zinc-950 py-10 mt-auto text-xs text-zinc-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-zinc-300 font-semibold">POS Putri v1.0</span>
                <span>— Dirancang untuk Kecepatan, Ketahanan Offline, dan Kejujuran Data.</span>
            </div>
            <div class="flex items-center gap-6">
                <span>IndexedDB Dexie.js</span>
                <span>PWA Service Worker</span>
                <span>Laravel 13 Engine</span>
                <a href="{{ route('login') }}" class="text-emerald-400 hover:underline">Masuk Petugas</a>
            </div>
        </div>
    </footer>

</body>
</html>
