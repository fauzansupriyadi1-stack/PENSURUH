<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PenSuruh - Dashboard</title>
    <link rel="icon" type="image/png" href="/images/sources/hero_courier_clean.jpg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="dashboard-page" data-active-role="{{ $activeRole ?? 'penyuruh' }}" class="bg-gray-50 text-gray-900 antialiased font-[Poppins] min-h-screen">

    {{-- ============================================================= --}}
    {{-- STICKY NAVBAR (sama dengan landing page)                      --}}
    {{-- ============================================================= --}}
    <header class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between">
            <a href="/" class="text-[20px] sm:text-[24px] font-black tracking-tight flex items-center select-none">
                <span class="text-[#0471fc]">Pen</span><span class="text-[#f3c903]">Suruh</span>
            </a>

            <nav class="hidden md:flex items-center gap-4 text-[13px] font-bold">
                <a href="#" class="dashboard-tab-btn px-3 py-1.5 rounded-full transition-all {{ $activeRole === 'penyuruh' ? 'text-[#0471fc]' : 'text-gray-500 hover:text-[#0471fc]' }}" data-tab="ringkasan">Beranda</a>
                <a href="#" class="dashboard-tab-btn px-3 py-1.5 rounded-full transition-all text-gray-500 hover:text-[#0471fc]" data-tab="tugas">
                    Pesanan <span class="ml-1 bg-[#0471fc] text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $activeRole === 'penyuruh' ? $myOrders->count() : $openOrders->count() }}</span>
                </a>
                <a href="#" class="dashboard-tab-btn px-3 py-1.5 rounded-full transition-all text-gray-500 hover:text-[#0471fc]" data-tab="riwayat">
                    Riwayat <span class="ml-1 bg-gray-200 text-gray-600 text-[10px] px-1.5 py-0.5 rounded-full">{{ $completedOrders->count() }}</span>
                </a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Role Switcher --}}
                <div class="hidden sm:flex items-center gap-1 bg-gray-100 rounded-full p-0.5 text-[11px] font-bold">
                    <a href="{{ route('dashboard.switch-role', 'penyuruh') }}" class="mode-link px-3 py-1 rounded-full transition-all {{ $activeRole === 'penyuruh' ? 'bg-[#0471fc] text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}" data-role="penyuruh">Penyuruh</a>
                    <a href="{{ route('dashboard.switch-role', 'disuruh') }}" class="mode-link px-3 py-1 rounded-full transition-all {{ $activeRole === 'disuruh' ? 'bg-[#0471fc] text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}" data-role="disuruh">Mitra</a>
                    <a href="{{ route('dashboard.switch-role', 'admin') }}" class="mode-link px-3 py-1 rounded-full transition-all {{ $activeRole === 'admin' ? 'bg-[#0471fc] text-white shadow-sm' : 'text-gray-500 hover:text-gray-700' }}" data-role="admin">Admin</a>
                </div>

                {{-- User Avatar --}}
                @if(isset($user) && $user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full border-2 border-[#0471fc]/30 object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-[#0471fc] text-white flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}</div>
                @endif

                <form action="/logout" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-gray-400 hover:text-red-500 px-1.5 py-1 transition-colors cursor-pointer">Keluar</button>
                </form>
            </div>
        </div>

        {{-- Mobile nav tabs --}}
        <div class="md:hidden flex items-center gap-1 px-4 pb-2 overflow-x-auto no-scrollbar">
            <a href="#" class="dashboard-tab-btn text-[12px] font-bold px-3 py-1 rounded-full whitespace-nowrap bg-[#0471fc] text-white" data-tab="ringkasan">Beranda</a>
            <a href="#" class="dashboard-tab-btn text-[12px] font-bold px-3 py-1 rounded-full whitespace-nowrap bg-gray-100 text-gray-600" data-tab="tugas">Pesanan {{ $activeRole === 'penyuruh' ? $myOrders->count() : $openOrders->count() }}</a>
            <a href="#" class="dashboard-tab-btn text-[12px] font-bold px-3 py-1 rounded-full whitespace-nowrap bg-gray-100 text-gray-600" data-tab="riwayat">Riwayat</a>
            {{-- Mobile role switcher --}}
            <div class="flex items-center gap-1 ml-2 border-l border-gray-200 pl-2">
                <a href="{{ route('dashboard.switch-role', 'penyuruh') }}" class="mode-link text-[11px] font-bold px-2 py-1 rounded-full {{ $activeRole === 'penyuruh' ? 'bg-[#0471fc] text-white' : 'text-gray-400' }}" data-role="penyuruh">P</a>
                <a href="{{ route('dashboard.switch-role', 'disuruh') }}" class="mode-link text-[11px] font-bold px-2 py-1 rounded-full {{ $activeRole === 'disuruh' ? 'bg-[#0471fc] text-white' : 'text-gray-400' }}" data-role="disuruh">M</a>
                <a href="{{ route('dashboard.switch-role', 'admin') }}" class="mode-link text-[11px] font-bold px-2 py-1 rounded-full {{ $activeRole === 'admin' ? 'bg-[#0471fc] text-white' : 'text-gray-400' }}" data-role="admin">A</a>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-3 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                <span class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 text-xs font-bold">✓</span>
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 mt-4">
            <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 text-red-800 text-sm font-semibold flex items-center gap-2">
                <span class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center text-red-600 text-xs font-bold">!</span>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ============================================================= --}}
    {{-- MAIN CONTENT                                                  --}}
    {{-- ============================================================= --}}
    <main class="max-w-[1100px] mx-auto px-4 sm:px-6 py-6 sm:py-8">

        {{-- Greeting --}}
        <div class="mb-6">
            <p class="text-sm text-gray-500">Selamat Datang</p>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
                Halo, {{ $user->name ?? 'Pengguna' }}
                @if($activeRole === 'penyuruh')
                    <span id="current-role-badge" class="ml-2 text-[10px] sm:text-xs font-bold bg-[#0471fc]/10 text-[#0471fc] px-2.5 py-1 rounded-full align-middle">PENYURUH</span>
                @elseif($activeRole === 'disuruh')
                    <span id="current-role-badge" class="ml-2 text-[10px] sm:text-xs font-bold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full align-middle">MITRA</span>
                @else
                    <span id="current-role-badge" class="ml-2 text-[10px] sm:text-xs font-bold bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full align-middle">ADMIN</span>
                @endif
            </h1>
        </div>

        {{-- =========================================================== --}}
        {{-- VIEW: PENYURUH                                              --}}
        {{-- =========================================================== --}}
        <div class="role-view" id="view-penyuruh" style="{{ $activeRole !== 'penyuruh' ? 'display:none;' : '' }}">

            {{-- Tab: Beranda --}}
            <div class="tab-content active" data-tab-panel="ringkasan">

                {{-- Wallet Card --}}
                <section class="bg-gradient-to-br from-[#0a1628] to-[#162544] rounded-[20px] sm:rounded-[24px] p-5 sm:p-7 text-white mb-6 shadow-lg">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[11px] font-semibold text-white/60 uppercase tracking-wider">Saldo Dompet PenSuruh</span>
                                <span class="text-[10px] text-white/40 font-mono">PS-00340227AB</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="wallet-balance-amount text-2xl sm:text-3xl font-extrabold" id="penyuruh-saldo" data-value="Rp 1.450.000">Rp 1.450.000</span>
                                <button type="button" class="eye-toggle text-white/50 hover:text-white text-sm cursor-pointer" onclick="toggleBalance()" aria-label="Sembunyikan saldo">👁</button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openTopUpModal()" class="bg-[#0471fc] hover:bg-[#0360d6] text-white text-xs font-bold px-4 py-2 rounded-full transition-all shadow-sm cursor-pointer">+ Isi Saldo</button>
                            <button type="button" onclick="switchDashboardTab('tugas')" class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold px-4 py-2 rounded-full transition-all cursor-pointer">Pesanan</button>
                            <button type="button" onclick="switchDashboardTab('riwayat')" class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold px-4 py-2 rounded-full transition-all cursor-pointer">Mutasi</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/10 rounded-2xl p-3.5">
                            <div class="text-[11px] text-white/60 font-semibold mb-1">Poin Suruh</div>
                            <div class="text-sm font-extrabold text-[#f3c903]">★ 4.780</div>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-3.5">
                            <div class="text-[11px] text-white/60 font-semibold mb-1">Voucher Aktif</div>
                            <div class="text-sm font-extrabold text-[#38b6ff]" id="penyuruh-voucher-count">8 Kupon</div>
                        </div>
                    </div>
                </section>

                {{-- Service Grid --}}
                <section class="mb-6">
                    <div class="mb-4">
                        <h2 class="text-base sm:text-lg font-extrabold text-gray-900">Mau suruh apa hari ini?</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Pilih layanan untuk langsung memesan pesuruh terdekat</p>
                    </div>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 sm:gap-3">
                        <button type="button" class="service-tile group flex flex-col items-center gap-1.5 bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 hover:border-[#0471fc]/30 hover:shadow-md transition-all cursor-pointer selected" data-service="Antar Paket & Dokumen" data-price="15000" onclick="selectService(this)">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-blue-50 text-xl flex items-center justify-center group-hover:bg-blue-100 transition-colors">📦</div>
                            <span class="text-[11px] sm:text-xs font-bold text-gray-700 text-center leading-tight">Antar Paket</span>
                            <span class="text-[10px] text-gray-400 font-medium">Rp 15rb</span>
                        </button>
                        <button type="button" class="service-tile group flex flex-col items-center gap-1.5 bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 hover:border-[#0471fc]/30 hover:shadow-md transition-all cursor-pointer" data-service="Beli Makan & Minum" data-price="12000" onclick="selectService(this)">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-red-50 text-xl flex items-center justify-center group-hover:bg-red-100 transition-colors">🍜</div>
                            <span class="text-[11px] sm:text-xs font-bold text-gray-700 text-center leading-tight">Beli Makan</span>
                            <span class="text-[10px] text-gray-400 font-medium">Rp 12rb</span>
                        </button>
                        <button type="button" class="service-tile group flex flex-col items-center gap-1.5 bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 hover:border-[#0471fc]/30 hover:shadow-md transition-all cursor-pointer" data-service="Titip Belanja & Antre" data-price="18000" onclick="selectService(this)">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-yellow-50 text-xl flex items-center justify-center group-hover:bg-yellow-100 transition-colors">🛒</div>
                            <span class="text-[11px] sm:text-xs font-bold text-gray-700 text-center leading-tight">Titip Belanja</span>
                            <span class="text-[10px] text-gray-400 font-medium">Rp 18rb</span>
                        </button>
                        <button type="button" class="service-tile group flex flex-col items-center gap-1.5 bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 hover:border-[#0471fc]/30 hover:shadow-md transition-all cursor-pointer" data-service="Bersih Rumah & Kost" data-price="45000" onclick="selectService(this)">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-teal-50 text-xl flex items-center justify-center group-hover:bg-teal-100 transition-colors">🧹</div>
                            <span class="text-[11px] sm:text-xs font-bold text-gray-700 text-center leading-tight">Bersih Kost</span>
                            <span class="text-[10px] text-gray-400 font-medium">Rp 45rb</span>
                        </button>
                        <button type="button" class="service-tile group flex flex-col items-center gap-1.5 bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 hover:border-[#0471fc]/30 hover:shadow-md transition-all cursor-pointer" data-service="Jemput / Antar Orang" data-price="14000" onclick="selectService(this)">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-green-50 text-xl flex items-center justify-center group-hover:bg-green-100 transition-colors">🛵</div>
                            <span class="text-[11px] sm:text-xs font-bold text-gray-700 text-center leading-tight">Antar Jemput</span>
                            <span class="text-[10px] text-gray-400 font-medium">Rp 14rb</span>
                        </button>
                        <button type="button" class="service-tile group flex flex-col items-center gap-1.5 bg-white rounded-2xl p-3 sm:p-4 border border-gray-100 hover:border-[#0471fc]/30 hover:shadow-md transition-all cursor-pointer" data-service="Suruh Custom / Lainnya" data-price="25000" onclick="selectService(this)">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-purple-50 text-xl flex items-center justify-center group-hover:bg-purple-100 transition-colors">📝</div>
                            <span class="text-[11px] sm:text-xs font-bold text-gray-700 text-center leading-tight">Custom</span>
                            <span class="text-[10px] text-gray-400 font-medium">Rp 25rb</span>
                        </button>
                    </div>
                </section>

                {{-- Order Form + Active Order --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

                    {{-- Form Pesan --}}
                    <section class="lg:col-span-3 bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-extrabold text-gray-900">Pesan Pesuruh Sekarang</h2>
                            <span class="text-[11px] text-emerald-600 font-bold">● 14 Mitra Online</span>
                        </div>
                        <form id="orderForm" action="{{ route('orders.create') }}" method="POST" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1" for="order-service">Jenis Layanan</label>
                                <select id="order-service" name="service_type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all" onchange="onServiceSelectChange(this)">
                                    <option value="Antar Paket & Dokumen" data-price="15000" selected>Antar Paket & Dokumen (Rp 15.000)</option>
                                    <option value="Beli Makan & Minum" data-price="12000">Beli Makan & Minum (Rp 12.000)</option>
                                    <option value="Titip Belanja & Antre" data-price="18000">Titip Belanja & Antre (Rp 18.000)</option>
                                    <option value="Bersih Rumah & Kost" data-price="45000">Bersih Rumah & Kost (Rp 45.000)</option>
                                    <option value="Jemput / Antar Orang" data-price="14000">Jemput / Antar Orang (Rp 14.000)</option>
                                    <option value="Suruh Custom / Lainnya" data-price="25000">Suruh Custom / Lainnya (Rp 25.000)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1" for="order-pickup">Lokasi Jemput / Lokasi Tugas</label>
                                <input type="text" id="order-pickup" name="pickup_location" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all" placeholder="Contoh: Kost Kejawan Putih Tambak No. 12" required>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <button type="button" class="text-[10px] font-bold bg-blue-50 text-[#0471fc] px-2.5 py-1 rounded-full hover:bg-blue-100 transition-colors cursor-pointer" onclick="setPickup('Kampus PENS Sukolilo')">Kampus PENS</button>
                                    <button type="button" class="text-[10px] font-bold bg-blue-50 text-[#0471fc] px-2.5 py-1 rounded-full hover:bg-blue-100 transition-colors cursor-pointer" onclick="setPickup('Kost Kejawan Putih')">Kost Kejawan</button>
                                    <button type="button" class="text-[10px] font-bold bg-blue-50 text-[#0471fc] px-2.5 py-1 rounded-full hover:bg-blue-100 transition-colors cursor-pointer" onclick="setPickup('Galaxy Mall Surabaya')">Galaxy Mall</button>
                                    <button type="button" class="text-[10px] font-bold bg-blue-50 text-[#0471fc] px-2.5 py-1 rounded-full hover:bg-blue-100 transition-colors cursor-pointer" onclick="setPickup('Stasiun Gubeng')">Stasiun Gubeng</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1" for="order-dropoff">Lokasi Tujuan <span class="font-normal text-gray-400">(opsional)</span></label>
                                <input type="text" id="order-dropoff" name="dropoff_location" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all" placeholder="Contoh: Gedung D4 PENS / Jl. Arif Rahman Hakim">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1" for="order-notes">Detail Tugas untuk Pesuruh</label>
                                <textarea id="order-notes" name="description" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all resize-none" placeholder="Contoh: Tolong antarkan map biru ke ruang TU lantai 2." required></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1" for="order-voucher">Pakai Voucher</label>
                                    <select id="order-voucher" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all" onchange="updateOrderTotal()">
                                        <option value="0">Tanpa Voucher</option>
                                        <option value="5000">HEMAT5RB (-Rp 5.000)</option>
                                        <option value="10000">SURUHKILAT (-Rp 10.000)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Estimasi Waktu</label>
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-500">⚡ 15 - 25 menit</div>
                                </div>
                            </div>
                            <input type="hidden" name="price" id="order-price-hidden" value="15000">
                            <div class="bg-gradient-to-r from-[#0471fc]/5 to-[#38b6ff]/5 rounded-2xl p-4 flex items-center justify-between border border-[#0471fc]/10">
                                <div>
                                    <div class="text-xs font-bold text-gray-600">Total Estimasi Ongkos</div>
                                    <small id="order-summary-detail" class="text-[11px] text-gray-400">Antar Paket & Dokumen</small>
                                </div>
                                <div class="text-lg sm:text-xl font-extrabold text-[#0471fc]" id="order-total-price">Rp 15.000</div>
                            </div>
                            <button type="submit" id="btn-submit-order" class="w-full bg-[#0471fc] hover:bg-[#0360d6] text-white font-bold text-sm py-3 rounded-full transition-all shadow-sm active:scale-[0.98] cursor-pointer">Cari Pesuruh Sekarang</button>
                        </form>
                    </section>

                    {{-- Right Column --}}
                    <div class="lg:col-span-2 flex flex-col gap-5">

                        {{-- Pesanan Aktif --}}
                        <section class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-extrabold text-gray-900">Pesanan Aktif</h3>
                                @php $aktif = $myOrders->where('status', 'open')->first(); @endphp
                                @if($aktif)
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Menunggu Mitra</span>
                                @else
                                    <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Tidak Ada</span>
                                @endif
                            </div>
                            @if($aktif)
                                <div class="font-bold text-sm text-gray-900">{{ $aktif->service_type ?? $aktif->title }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $aktif->pickup_location ?? '-' }} → {{ $aktif->dropoff_location ?? 'Lokasi Tugas' }}</div>
                                <div class="flex items-center gap-1 mt-4 mb-2">
                                    <div class="flex-1 h-1 bg-[#0471fc] rounded-full"></div>
                                    <div class="flex-1 h-1 {{ $aktif->disuruh_id ? 'bg-[#0471fc]' : 'bg-gray-200' }} rounded-full"></div>
                                    <div class="flex-1 h-1 bg-gray-200 rounded-full"></div>
                                    <div class="flex-1 h-1 bg-gray-200 rounded-full"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-400 font-semibold">
                                    <span class="text-[#0471fc]">Dipesan</span><span class="{{ $aktif->disuruh_id ? 'text-[#0471fc]' : '' }}">Dijemput</span><span>Dikerjakan</span><span>Selesai</span>
                                </div>
                                @if($aktif->disuruh_id)
                                    <div class="flex items-center gap-3 mt-4 bg-gray-50 rounded-xl p-3">
                                        <div class="w-9 h-9 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($aktif->mitra->name ?? 'M', 0, 2)) }}</div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $aktif->mitra->name ?? 'Mitra' }}</div>
                                            <div class="text-[11px] text-gray-500">★ 4.9 · Sedang menuju lokasi</div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-6 text-gray-400">
                                    <div class="text-2xl mb-2">📋</div>
                                    <div class="text-xs">Belum ada pesanan aktif.<br>Pesan pesuruh sekarang!</div>
                                </div>
                            @endif
                        </section>

                        {{-- Pesan Lagi --}}
                        <section class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-extrabold text-gray-900">Pesan Lagi</h3>
                                <a href="#" class="text-[11px] font-bold text-[#0471fc] hover:underline" onclick="switchDashboardTab('tugas'); return false;">Lihat Semua</a>
                            </div>
                            @forelse($myOrders->take(3) as $ord)
                                <div class="flex items-center justify-between py-2.5 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">{{ $ord->service_type ?? $ord->title }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $ord->pickup_location ?? '-' }} · Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <button type="button" class="text-[11px] font-bold text-[#0471fc] border border-[#0471fc]/30 px-3 py-1 rounded-full hover:bg-[#0471fc]/5 transition-colors cursor-pointer" onclick="alert('Fitur pesan ulang segera hadir!')">Pesan</button>
                                </div>
                            @empty
                                <div class="text-xs text-gray-400 text-center py-4">Belum ada riwayat pesanan.</div>
                            @endforelse
                        </section>
                    </div>
                </div>
            </div>

            {{-- Tab: Pesanan --}}
            <div class="tab-content" data-tab-panel="tugas">
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4">Daftar Pesanan Suruh Kamu</h3>
                    <div class="flex gap-2 mb-4">
                        <button type="button" class="filter-tab text-xs font-bold px-3 py-1.5 rounded-full bg-[#0471fc] text-white cursor-pointer active" onclick="filterCustomerTasks(this, 'all')">Semua</button>
                        <button type="button" class="filter-tab text-xs font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 cursor-pointer" onclick="filterCustomerTasks(this, 'aktif')">Aktif</button>
                        <button type="button" class="filter-tab text-xs font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 cursor-pointer" onclick="filterCustomerTasks(this, 'done')">Selesai</button>
                    </div>
                    <ul id="penyuruh-task-list" class="divide-y divide-gray-100">
                        @forelse($myOrders as $ord)
                            <li class="task-item-compact flex items-center gap-3 py-3" data-status="{{ $ord->status === 'completed' ? 'done' : 'aktif' }}">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $ord->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $ord->status === 'completed' ? 'Selesai' : 'Aktif' }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 truncate">{{ $ord->service_type ?? $ord->title }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $ord->created_at->format('d M Y') }} · Mitra: {{ $ord->mitra->name ?? 'Belum ada' }}</div>
                                </div>
                                <span class="text-sm font-extrabold text-gray-700 whitespace-nowrap">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-400">Belum ada pesanan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Tab: Riwayat --}}
            <div class="tab-content" data-tab-panel="riwayat">
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4">Riwayat Mutasi Dompet</h3>
                    <ul class="divide-y divide-gray-100" id="penyuruh-history-list">
                        @forelse($completedOrders as $ord)
                            <li class="flex items-center gap-3 py-3">
                                <div class="w-9 h-9 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-sm font-bold flex-shrink-0">↑</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800">{{ $ord->service_type ?? $ord->title }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                                </div>
                                <span class="text-sm font-extrabold text-red-500 whitespace-nowrap">-Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-400">Belum ada riwayat transaksi.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- =========================================================== --}}
        {{-- VIEW: YANG DISURUH (MITRA)                                  --}}
        {{-- =========================================================== --}}
        <div class="role-view" id="view-disuruh" style="{{ $activeRole !== 'disuruh' ? 'display:none;' : '' }}">

            <div class="tab-content active" data-tab-panel="ringkasan">

                {{-- Mitra Status Bar --}}
                <section class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <div class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Status Akun Mitra</div>
                        <div class="text-base font-extrabold text-gray-900 mt-0.5" id="mitra-status-text">🟢 Online — Siap Menerima Tugas</div>
                    </div>
                    <button type="button" id="btn-mitra-online" onclick="toggleMitraOnline(this)" class="text-xs font-bold text-red-500 border border-red-200 px-4 py-2 rounded-full hover:bg-red-50 transition-colors cursor-pointer">Nonaktifkan</button>
                </section>

                {{-- Earnings + Performance --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <section class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[11px] text-gray-500 font-semibold">Pendapatan Tersedia</span>
                            <span class="text-[10px] text-gray-400 font-mono">MTR-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-4">
                            <span class="wallet-balance-amount text-2xl font-extrabold text-gray-900" data-value="Rp 875.000">Rp 875.000</span>
                            <button type="button" class="eye-toggle text-gray-400 hover:text-gray-600 text-sm cursor-pointer" onclick="toggleBalance()">👁</button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="text-xs font-bold bg-[#0471fc] text-white px-4 py-2 rounded-full hover:bg-[#0360d6] transition-all cursor-pointer" onclick="alert('Penarikan dana mitra ke rekening diproses (Demo).')">Tarik Pendapatan</button>
                            <button type="button" class="text-xs font-bold text-[#0471fc] border border-[#0471fc]/30 px-4 py-2 rounded-full hover:bg-[#0471fc]/5 transition-colors cursor-pointer" onclick="switchDashboardTab('riwayat')">Slip Komisi</button>
                        </div>
                    </section>

                    <section class="bg-gradient-to-br from-[#0a1628] to-[#162544] rounded-[20px] p-5 text-white">
                        <div class="text-[10px] font-bold text-[#f3c903] uppercase tracking-wider mb-2">Performa Mitra</div>
                        <div class="text-xs text-white/60 mb-0.5">Pendapatan Minggu Ini</div>
                        <div class="text-xl font-extrabold mb-3">Rp 340.000 <span class="text-[11px] font-semibold text-[#38b6ff]">{{ $completedOrders->count() }} Tugas Selesai</span></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white/10 rounded-xl p-3">
                                <div class="text-[10px] text-white/50 font-semibold">Rating</div>
                                <div class="text-sm font-extrabold text-[#f3c903]">★ 4.92</div>
                            </div>
                            <div class="bg-white/10 rounded-xl p-3">
                                <div class="text-[10px] text-white/50 font-semibold">Tingkat Terima</div>
                                <div class="text-sm font-extrabold">96%</div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Orders Incoming + Active Tasks --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <section class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-extrabold text-gray-900">Orderan Masuk di Sekitarmu</h3>
                            <span class="text-[11px] font-bold text-[#0471fc]">Radius 3 km</span>
                        </div>
                        @forelse($openOrders->take(3) as $ord)
                            <div class="bg-gray-50 rounded-2xl p-4 {{ !$loop->last ? 'mb-3' : '' }}">
                                <div class="flex justify-between items-start mb-1.5">
                                    <strong class="text-sm text-gray-900">{{ $ord->service_type ?? $ord->title }}</strong>
                                    <span class="text-sm font-extrabold text-emerald-600">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-3">{{ $ord->pickup_location ?? 'Lokasi Tugas' }} → {{ $ord->dropoff_location ?? 'Tujuan' }}</div>
                                <form action="{{ route('orders.take', $ord->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#0471fc] hover:bg-[#0360d6] text-white text-xs font-bold py-2.5 rounded-full transition-all cursor-pointer">Ambil Order Ini</button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400">
                                <div class="text-2xl mb-2">🔍</div>
                                <div class="text-xs">Tidak ada orderan masuk saat ini.<br>Tetap online untuk menerima tugas!</div>
                            </div>
                        @endforelse
                    </section>

                    <section class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-extrabold text-gray-900 mb-4">Tugas Yang Harus Dikerjakan</h3>
                        <ul id="mitra-active-tasks" class="divide-y divide-gray-100">
                            @forelse($myOrders->where('status', 'taken') as $ord)
                                <li class="flex items-center gap-3 py-3">
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Berjalan</span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-800 truncate">{{ $ord->service_type ?? $ord->title }}</div>
                                        <div class="text-[11px] text-gray-400">Pemesan: {{ $ord->penyuruh->name ?? '-' }}</div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-sm font-extrabold text-emerald-600">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                                        <form action="{{ route('orders.complete', $ord->slug) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold text-[#0471fc] border border-[#0471fc]/30 px-2.5 py-0.5 rounded-full hover:bg-[#0471fc]/5 transition-colors cursor-pointer">Selesai</button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="py-6 text-center text-xs text-gray-400">Belum ada tugas aktif.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>
            </div>

            {{-- Tab: Tugas (Mitra) --}}
            <div class="tab-content" data-tab-panel="tugas">
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4">Semua Tugas Mitra</h3>
                    <ul class="divide-y divide-gray-100">
                        @forelse($myOrders as $ord)
                            <li class="flex items-center gap-3 py-3">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $ord->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $ord->status === 'completed' ? 'Selesai' : 'Berjalan' }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 truncate">{{ $ord->service_type ?? $ord->title }}</div>
                                    <div class="text-[11px] text-gray-400">Pemesan: {{ $ord->penyuruh->name ?? '-' }}</div>
                                </div>
                                <span class="text-sm font-extrabold text-emerald-600">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-400">Belum ada tugas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Tab: Riwayat (Mitra) --}}
            <div class="tab-content" data-tab-panel="riwayat">
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4">Riwayat Komisi & Pencairan Mitra</h3>
                    <ul class="divide-y divide-gray-100">
                        @forelse($completedOrders as $ord)
                            <li class="flex items-center gap-3 py-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm font-bold flex-shrink-0">↓</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800">Komisi: {{ $ord->service_type ?? $ord->title }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                                </div>
                                <span class="text-sm font-extrabold text-emerald-600">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-400">Belum ada riwayat.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- =========================================================== --}}
        {{-- VIEW: ADMIN                                                 --}}
        {{-- =========================================================== --}}
        <div class="role-view" id="view-admin" style="{{ $activeRole !== 'admin' ? 'display:none;' : '' }}">

            <div class="tab-content active" data-tab-panel="ringkasan">

                {{-- Admin Stats --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                    <div class="bg-white rounded-[18px] border border-gray-100 shadow-sm p-4 sm:p-5">
                        <div class="text-[11px] text-gray-500 font-bold uppercase mb-1">Total Pengguna</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $adminStats['total_users'] }}</div>
                        <div class="text-[11px] text-gray-400 mt-1">Semua Role</div>
                    </div>
                    <div class="bg-white rounded-[18px] border border-gray-100 shadow-sm p-4 sm:p-5">
                        <div class="text-[11px] text-[#0471fc] font-bold uppercase mb-1">Penyuruh</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $adminStats['total_penyuruh'] }}</div>
                        <div class="text-[11px] text-gray-400 mt-1">Pemberi Tugas</div>
                    </div>
                    <div class="bg-white rounded-[18px] border border-gray-100 shadow-sm p-4 sm:p-5">
                        <div class="text-[11px] text-emerald-600 font-bold uppercase mb-1">Mitra</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $adminStats['total_disuruh'] }}</div>
                        <div class="text-[11px] text-gray-400 mt-1">Pelaksana</div>
                    </div>
                    <div class="bg-gradient-to-br from-[#0471fc] to-[#38b6ff] rounded-[18px] p-4 sm:p-5 text-white shadow-sm">
                        <div class="text-[11px] font-bold uppercase text-white/70 mb-1">Total Transaksi</div>
                        <div class="text-xl font-extrabold">Rp {{ number_format($adminStats['total_turnover'], 0, ',', '.') }}</div>
                        <div class="text-[11px] text-white/70 mt-1">{{ $adminStats['completed_count'] }} Order Selesai</div>
                    </div>
                </div>

                {{-- User Management --}}
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <div>
                            <div class="text-[10px] font-bold text-[#0471fc] uppercase tracking-wider mb-0.5">Kontrol Penuh Platform</div>
                            <h2 class="text-base font-extrabold text-gray-900">Manajemen Seluruh Akun</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Kelola, monitor, dan ubah hak peran akun penyuruh maupun mitra.</p>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
                            <input type="text" id="admin-search" placeholder="Cari nama atau email.." onkeyup="filterUserList()" class="bg-gray-50 border border-gray-200 rounded-full pl-9 pr-4 py-2 text-xs font-medium w-full sm:w-56 focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all">
                        </div>
                    </div>

                    <div class="flex gap-2 mb-4">
                        <button type="button" class="admin-filter-tab text-xs font-bold px-3 py-1.5 rounded-full bg-[#0471fc] text-white cursor-pointer active" data-filter="all">Semua {{ $allUsers->count() }}</button>
                        <button type="button" class="admin-filter-tab text-xs font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 cursor-pointer" data-filter="penyuruh">Penyuruh {{ $adminStats['total_penyuruh'] }}</button>
                        <button type="button" class="admin-filter-tab text-xs font-bold px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 cursor-pointer" data-filter="disuruh">Mitra {{ $adminStats['total_disuruh'] }}</button>
                    </div>

                    <div class="overflow-x-auto -mx-5 sm:-mx-6 px-5 sm:px-6">
                        <table class="w-full text-sm" id="userAccountsTable">
                            <thead>
                                <tr class="border-b border-gray-100 text-left">
                                    <th class="pb-3 text-[11px] font-bold text-gray-500 uppercase">Nama</th>
                                    <th class="pb-3 text-[11px] font-bold text-gray-500 uppercase">Email & HP</th>
                                    <th class="pb-3 text-[11px] font-bold text-gray-500 uppercase">Role</th>
                                    <th class="pb-3 text-[11px] font-bold text-gray-500 uppercase">Aktivitas</th>
                                    <th class="pb-3 text-[11px] font-bold text-gray-500 uppercase">Status</th>
                                    <th class="pb-3 text-[11px] font-bold text-gray-500 uppercase">Kontrol</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($allUsers as $u)
                                    <tr data-role="{{ $u->role }}" data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email ?? '') }}" class="hover:bg-gray-50/50">
                                        <td class="py-3">
                                            <div class="flex items-center gap-2.5">
                                                @if($u->avatar)
                                                    <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                                @else
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0 {{ $u->role === 'admin' ? 'bg-purple-500' : ($u->role === 'disuruh' ? 'bg-emerald-500' : 'bg-[#0471fc]') }}">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-bold text-gray-800">{{ $u->name }}</div>
                                                    <div class="text-[10px] text-gray-400">#{{ $u->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-xs text-gray-700">{{ $u->email ?? '-' }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $u->phone ?? 'Belum ada' }}</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : ($u->role === 'disuruh' ? 'bg-emerald-100 text-emerald-700' : 'bg-[#0471fc]/10 text-[#0471fc]') }}">{{ strtoupper($u->role) }}</span>
                                        </td>
                                        <td class="py-3 text-xs text-gray-500">
                                            @if($u->role === 'penyuruh')
                                                <b>{{ $u->orders_created_count }}</b> order
                                            @elseif($u->role === 'disuruh')
                                                <b>{{ $u->orders_taken_count }}</b> tugas
                                            @else
                                                Akses Penuh
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Aktif</span>
                                        </td>
                                        <td class="py-3">
                                            <form action="{{ route('users.update-role', $u->slug) }}" method="POST" class="m-0">
                                                @csrf
                                                <select name="role" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 rounded-lg px-2 py-1 text-xs font-medium focus:ring-2 focus:ring-[#0471fc]/30 outline-none cursor-pointer" aria-label="Ubah role {{ $u->name }}">
                                                    <option value="penyuruh" {{ $u->role === 'penyuruh' ? 'selected' : '' }}>Penyuruh</option>
                                                    <option value="disuruh" {{ $u->role === 'disuruh' ? 'selected' : '' }}>Yang Disuruh</option>
                                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tab: Tugas (Admin) --}}
            <div class="tab-content" data-tab-panel="tugas">
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4">Monitoring Seluruh Pesanan Platform</h3>
                    <ul class="divide-y divide-gray-100">
                        @forelse($openOrders->merge($completedOrders) as $ord)
                            <li class="flex items-center gap-3 py-3">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $ord->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $ord->status === 'completed' ? 'Selesai' : 'Aktif' }}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800 truncate">{{ $ord->service_type ?? $ord->title }}</div>
                                    <div class="text-[11px] text-gray-400">Penyuruh: {{ $ord->penyuruh->name ?? '-' }} · Mitra: {{ $ord->mitra->name ?? 'Belum ada' }}</div>
                                </div>
                                <span class="text-sm font-extrabold text-gray-700">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-400">Belum ada pesanan di platform.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Tab: Riwayat (Admin) --}}
            <div class="tab-content" data-tab-panel="riwayat">
                <div class="bg-white rounded-[20px] border border-gray-100 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4">Log Transaksi Platform</h3>
                    <ul class="divide-y divide-gray-100">
                        @forelse($completedOrders as $ord)
                            <li class="flex items-center gap-3 py-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm font-bold flex-shrink-0">✓</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-800">Order #PS-{{ $ord->id }} · {{ $ord->service_type ?? $ord->title }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                                </div>
                                <span class="text-sm font-extrabold text-emerald-600">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-gray-400">Belum ada transaksi selesai.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </main>

    {{-- ============================================================= --}}
    {{-- MODAL: TOP UP SALDO                                           --}}
    {{-- ============================================================= --}}
    <div id="topup-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="bg-white rounded-[24px] w-full max-w-md p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-extrabold text-gray-900">Isi Saldo Dompet PenSuruh</h3>
                <button type="button" onclick="closeTopUpModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 text-sm font-bold cursor-pointer transition-colors" aria-label="Tutup">✕</button>
            </div>
            <p class="text-xs text-gray-500 mb-4">Pilih nominal pengisian saldo (simulasi):</p>
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" class="nominal-btn text-xs font-bold py-2.5 rounded-xl border border-gray-200 hover:border-[#0471fc] hover:bg-[#0471fc]/5 transition-all cursor-pointer" onclick="selectTopUpNominal(this, 50000)">Rp 50.000</button>
                <button type="button" class="nominal-btn text-xs font-bold py-2.5 rounded-xl border-2 border-[#0471fc] bg-[#0471fc]/5 text-[#0471fc] cursor-pointer selected" onclick="selectTopUpNominal(this, 100000)">Rp 100.000</button>
                <button type="button" class="nominal-btn text-xs font-bold py-2.5 rounded-xl border border-gray-200 hover:border-[#0471fc] hover:bg-[#0471fc]/5 transition-all cursor-pointer" onclick="selectTopUpNominal(this, 200000)">Rp 200.000</button>
                <button type="button" class="nominal-btn text-xs font-bold py-2.5 rounded-xl border border-gray-200 hover:border-[#0471fc] hover:bg-[#0471fc]/5 transition-all cursor-pointer" onclick="selectTopUpNominal(this, 300000)">Rp 300.000</button>
                <button type="button" class="nominal-btn text-xs font-bold py-2.5 rounded-xl border border-gray-200 hover:border-[#0471fc] hover:bg-[#0471fc]/5 transition-all cursor-pointer" onclick="selectTopUpNominal(this, 500000)">Rp 500.000</button>
                <button type="button" class="nominal-btn text-xs font-bold py-2.5 rounded-xl border border-gray-200 hover:border-[#0471fc] hover:bg-[#0471fc]/5 transition-all cursor-pointer" onclick="selectTopUpNominal(this, 1000000)">Rp 1.000.000</button>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 mb-1" for="topup-method">Metode Pembayaran</label>
                <select id="topup-method" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#0471fc]/30 focus:border-[#0471fc] outline-none transition-all">
                    <option>QRIS (Instan)</option>
                    <option>Transfer Bank (BCA / BRI / Mandiri)</option>
                    <option>Minimarket (Indomaret / Alfamart)</option>
                </select>
            </div>
            <button type="button" class="w-full bg-[#0471fc] hover:bg-[#0360d6] text-white font-bold text-sm py-3 rounded-full transition-all shadow-sm cursor-pointer" onclick="confirmTopUp()">Konfirmasi Isi Saldo</button>
        </div>
    </div>

    {{-- ============================================================= --}}
    {{-- INLINE SCRIPTS (dashboard logic)                              --}}
    {{-- ============================================================= --}}
    <script>
        // Tab switching (scoped to active role view)
        function switchDashboardTab(tab) {
            const currentView = document.querySelector('.role-view:not([style*="display:none"])') ||
                                document.querySelector('.role-view');
            if (!currentView) return;
            currentView.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            const target = currentView.querySelector(`[data-tab-panel="${tab}"]`);
            if (target) target.classList.add('active');

            // Update tab button states
            document.querySelectorAll('.dashboard-tab-btn').forEach(b => {
                const isActive = b.dataset.tab === tab;
                b.classList.toggle('active', isActive);
                // Mobile styles
                if (b.closest('.md\\:hidden')) {
                    b.className = b.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').trim();
                    b.classList.add('dashboard-tab-btn', 'text-[12px]', 'font-bold', 'px-3', 'py-1', 'rounded-full', 'whitespace-nowrap');
                    if (isActive) {
                        b.classList.add('bg-[#0471fc]', 'text-white');
                    } else {
                        b.classList.add('bg-gray-100', 'text-gray-600');
                    }
                }
            });
        }

        document.querySelectorAll('.dashboard-tab-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                switchDashboardTab(this.dataset.tab);
            });
        });

        // Service tile selection
        function selectService(el) {
            document.querySelectorAll('.service-tile').forEach(t => {
                t.classList.remove('selected', 'ring-2', 'ring-[#0471fc]', 'border-[#0471fc]');
            });
            el.classList.add('selected', 'ring-2', 'ring-[#0471fc]', 'border-[#0471fc]');
            const service = el.dataset.service;
            const price = parseInt(el.dataset.price || 0);
            const sel = document.getElementById('order-service');
            if (sel) {
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === service) { sel.selectedIndex = i; break; }
                }
            }
            document.getElementById('order-price-hidden').value = price;
            updateOrderTotal();
        }

        // Order total price
        function updateOrderTotal() {
            const price = parseInt(document.getElementById('order-price-hidden')?.value || 0);
            const voucher = parseInt(document.getElementById('order-voucher')?.value || 0);
            const total = Math.max(0, price - voucher);
            const el = document.getElementById('order-total-price');
            if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
            const detail = document.getElementById('order-summary-detail');
            const sel = document.getElementById('order-service');
            if (detail && sel) detail.textContent = sel.value;
        }

        function onServiceSelectChange(sel) {
            const opt = sel.options[sel.selectedIndex];
            const price = opt.dataset.price || 0;
            document.getElementById('order-price-hidden').value = price;
            updateOrderTotal();
        }

        // Balance toggle
        function toggleBalance() {
            document.querySelectorAll('.wallet-balance-amount').forEach(el => {
                if (el.textContent === '••••••') {
                    el.textContent = el.dataset.value;
                } else {
                    el.textContent = '••••••';
                }
            });
        }

        // Top Up Modal
        function openTopUpModal() {
            const modal = document.getElementById('topup-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeTopUpModal() {
            const modal = document.getElementById('topup-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        function selectTopUpNominal(btn, amount) {
            document.querySelectorAll('.nominal-btn').forEach(b => {
                b.classList.remove('selected', 'border-[#0471fc]', 'bg-[#0471fc]/5', 'text-[#0471fc]', 'border-2');
                b.classList.add('border', 'border-gray-200');
            });
            btn.classList.remove('border', 'border-gray-200');
            btn.classList.add('selected', 'border-2', 'border-[#0471fc]', 'bg-[#0471fc]/5', 'text-[#0471fc]');
        }
        function confirmTopUp() {
            alert('Top up berhasil (simulasi front-end).');
            closeTopUpModal();
        }

        // Mitra online toggle
        function toggleMitraOnline(btn) {
            const statusText = document.getElementById('mitra-status-text');
            if (btn.textContent.includes('Nonaktifkan')) {
                btn.textContent = 'Aktifkan';
                btn.classList.remove('text-red-500', 'border-red-200');
                btn.classList.add('text-emerald-600', 'border-emerald-200');
                if (statusText) statusText.textContent = '🔴 Offline — Tidak Menerima Tugas';
            } else {
                btn.textContent = 'Nonaktifkan';
                btn.classList.remove('text-emerald-600', 'border-emerald-200');
                btn.classList.add('text-red-500', 'border-red-200');
                if (statusText) statusText.textContent = '🟢 Online — Siap Menerima Tugas';
            }
        }

        // Penyuruh filter tasks
        function filterCustomerTasks(btn, status) {
            btn.closest('.flex').querySelectorAll('.filter-tab').forEach(b => {
                b.classList.remove('bg-[#0471fc]', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            btn.classList.add('bg-[#0471fc]', 'text-white');
            document.querySelectorAll('#penyuruh-task-list .task-item-compact').forEach(li => {
                li.style.display = (status === 'all' || li.dataset.status === status) ? '' : 'none';
            });
        }

        // Admin user search
        function filterUserList() {
            const val = document.getElementById('admin-search')?.value.toLowerCase() || '';
            document.querySelectorAll('#userAccountsTable tbody tr').forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                row.style.display = (name.includes(val) || email.includes(val)) ? '' : 'none';
            });
        }

        // Admin role filter tabs
        document.querySelectorAll('.admin-filter-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.flex').querySelectorAll('.admin-filter-tab').forEach(b => {
                    b.classList.remove('bg-[#0471fc]', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-600');
                });
                this.classList.remove('bg-gray-100', 'text-gray-600');
                this.classList.add('bg-[#0471fc]', 'text-white');
                const filter = this.dataset.filter;
                document.querySelectorAll('#userAccountsTable tbody tr').forEach(row => {
                    row.style.display = (filter === 'all' || row.dataset.role === filter) ? '' : 'none';
                });
            });
        });

        // Location shortcut
        function setPickup(loc) {
            const el = document.getElementById('order-pickup');
            if (el) el.value = loc;
        }

        // Tab content visibility
        document.querySelectorAll('.tab-content').forEach(c => {
            if (!c.classList.contains('active')) c.style.display = 'none';
        });
        document.querySelectorAll('.tab-content.active').forEach(c => c.style.display = '');

        // Patch switchDashboardTab to toggle display
        const _origSwitch = switchDashboardTab;
        switchDashboardTab = function(tab) {
            _origSwitch(tab);
            const currentView = document.querySelector('.role-view:not([style*="display:none"])');
            if (!currentView) return;
            currentView.querySelectorAll('.tab-content').forEach(c => {
                c.style.display = c.classList.contains('active') ? '' : 'none';
            });
        };
    </script>

</body>
</html>
