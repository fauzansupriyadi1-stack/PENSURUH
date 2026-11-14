<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard ({{ ucfirst($activeRole ?? 'Penyuruh') }}) — PenSuruh</title>
    <link rel="icon" type="image/png" href="/images/sources/hero_courier_clean.jpg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#ebeef5] text-gray-900 min-h-screen p-3 sm:p-6 lg:p-8 flex items-center justify-center antialiased">

    <!-- MAIN DASHBOARD CANVAS (White Rounded Container like reference) -->
    <div class="w-full max-w-[1240px] bg-[#fbfbfd] rounded-[24px] sm:rounded-[36px] p-4 sm:p-7 lg:p-9 shadow-[0_20px_50px_rgba(4,113,252,0.06)] border border-gray-100 flex flex-col gap-6 sm:gap-7">

        <!-- ================= TOP HEADER BAR ================= -->
        <header class="flex flex-col md:flex-row items-center justify-between gap-4 pb-3 border-b border-gray-100">
            <!-- Left: User Profile & Greeting -->
            <div class="flex items-center gap-3.5 w-full md:w-auto">
                <div class="relative">
                    @if(isset($user) && $user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-11 h-11 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-[#0471fc]/30 shadow-sm">
                    @else
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-[#0471fc]/10 text-[#0471fc] font-bold text-lg flex items-center justify-center border-2 border-[#0471fc]/20 shadow-sm">
                            {{ substr($user->name ?? 'F', 0, 1) }}
                        </div>
                    @endif
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-400 block leading-tight">Selamat Datang</span>
                        <!-- Role Badge -->
                        @if($activeRole === 'penyuruh')
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-[#0471fc]">Penyuruh (Customer)</span>
                        @elseif($activeRole === 'disuruh')
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Yang Disuruh (Mitra)</span>
                        @else
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">Administrator</span>
                        @endif
                    </div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight leading-snug">
                        Halo, {{ $user->name ?? 'Fauzan Supriyadi' }}
                    </h1>
                </div>
            </div>

            <!-- Center: Navigation Pills (Ringkasan / Tugas Suruh / Riwayat) -->
            <nav class="flex items-center gap-1.5 sm:gap-2 bg-gray-100/90 p-1.5 rounded-full text-xs font-bold overflow-x-auto max-w-full">
                <button id="navTabOverview" onclick="switchMainTab('overview')" class="px-4 py-1.5 rounded-full bg-[#0471fc] text-white shadow-xs transition-all cursor-pointer">
                    Ringkasan
                </button>
                <button id="navTabTasks" onclick="switchMainTab('tasks')" class="px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 transition-colors cursor-pointer flex items-center gap-1.5">
                    <span>Tugas Suruh</span>
                    @if($activeRole === 'disuruh' || $activeRole === 'admin')
                        <span class="bg-[#f3c903] text-gray-900 text-[10px] font-extrabold px-1.5 py-0.2 rounded-full">
                            {{ $openOrders->count() }}
                        </span>
                    @elseif($activeRole === 'penyuruh')
                        <span class="bg-blue-100 text-[#0471fc] text-[10px] font-extrabold px-1.5 py-0.2 rounded-full">
                            {{ $myOrders->count() }}
                        </span>
                    @endif
                </button>
                <button id="navTabHistory" onclick="switchMainTab('history')" class="px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 transition-colors cursor-pointer flex items-center gap-1.5">
                    <span>Riwayat</span>
                    @if($activeRole === 'admin')
                        <span class="bg-purple-100 text-purple-700 text-[10px] font-extrabold px-1.5 py-0.2 rounded-full">
                            {{ $completedOrders->count() }}
                        </span>
                    @endif
                </button>
            </nav>

            <!-- Right: Role Switcher Simulator + Actions -->
            <div class="flex items-center gap-2.5 w-full md:w-auto justify-end flex-wrap">
                <!-- Role Switcher Quick Pill (Simulator) -->
                <div class="flex items-center bg-gray-100 p-1 rounded-xl text-[11px] font-bold text-gray-600 border border-gray-200/60">
                    <span class="text-[10px] text-gray-400 px-2 uppercase font-semibold">Mode:</span>
                    <a href="{{ route('dashboard', ['role' => 'penyuruh']) }}" class="px-2.5 py-1 rounded-lg transition-all {{ $activeRole === 'penyuruh' ? 'bg-white text-[#0471fc] shadow-xs' : 'text-gray-500 hover:text-gray-900' }}">
                        Penyuruh
                    </a>
                    <a href="{{ route('dashboard', ['role' => 'disuruh']) }}" class="px-2.5 py-1 rounded-lg transition-all {{ $activeRole === 'disuruh' ? 'bg-white text-emerald-600 shadow-xs' : 'text-gray-500 hover:text-gray-900' }}">
                        Yang Disuruh
                    </a>
                    <a href="{{ route('dashboard', ['role' => 'admin']) }}" class="px-2.5 py-1 rounded-lg transition-all {{ $activeRole === 'admin' ? 'bg-white text-purple-600 shadow-xs' : 'text-gray-500 hover:text-gray-900' }}">
                        Admin
                    </a>
                </div>

                <a href="/" class="text-xs font-bold text-[#0471fc] bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-xl transition-colors cursor-pointer" title="Halaman Utama">
                    Landing Page
                </a>

                <form action="/logout" method="POST" class="inline m-0">
                    @csrf
                    <button type="submit" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white border border-gray-200/80 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 transition-all cursor-pointer shadow-xs" title="Keluar">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Message Alerts -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-black">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs font-semibold rounded-2xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center text-[10px] font-black">!</span>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">✕</button>
            </div>
        @endif

        <!-- ================= TAB 1: OVERVIEW / RINGKASAN ================= -->
        <div id="sectionOverview" class="flex flex-col gap-6 sm:gap-7">

            <!-- JIKA ROLE == ADMIN: TAMPILKAN KONTROL PENUH SELURUH AKUN DENGAN FILTER -->
            @if($activeRole === 'admin')
                <!-- 4 STATS CARDS UNTUK ADMIN -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <div class="bg-white rounded-[22px] p-5 border border-gray-100 shadow-xs flex flex-col justify-between">
                        <span class="text-xs font-bold text-gray-400">Total Akun Pengguna</span>
                        <div class="flex items-baseline justify-between mt-2">
                            <span class="text-2xl sm:text-3xl font-black text-gray-900">{{ $adminStats['total_users'] }}</span>
                            <span class="text-[10px] font-bold text-[#0471fc] bg-blue-50 px-2 py-0.5 rounded-full">Semua Role</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-[22px] p-5 border border-gray-100 shadow-xs flex flex-col justify-between">
                        <span class="text-xs font-bold text-[#0471fc]">Akun Penyuruh (Customer)</span>
                        <div class="flex items-baseline justify-between mt-2">
                            <span class="text-2xl sm:text-3xl font-black text-[#0471fc]">{{ $adminStats['total_penyuruh'] }}</span>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">Pemberi Tugas</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-[22px] p-5 border border-gray-100 shadow-xs flex flex-col justify-between">
                        <span class="text-xs font-bold text-emerald-600">Akun Yang Disuruh (Mitra)</span>
                        <div class="flex items-baseline justify-between mt-2">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-600">{{ $adminStats['total_disuruh'] }}</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Pelaksana</span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-[#0471fc] to-[#38b6ff] text-white rounded-[22px] p-5 shadow-xs flex flex-col justify-between">
                        <span class="text-xs font-bold text-white/80">Total Transaksi Selesai</span>
                        <div class="flex items-baseline justify-between mt-2">
                            <span class="text-xl sm:text-2xl font-black text-white">Rp {{ number_format($adminStats['total_turnover'], 0, ',', '.') }}</span>
                            <span class="text-[10px] font-black text-gray-900 bg-[#f3c903] px-2 py-0.5 rounded-full">{{ $adminStats['completed_count'] }} Order</span>
                        </div>
                    </div>
                </div>

                <!-- KONTROL PENUH AKUN PENGGUNA (SESUAI REQUEST USER) -->
                <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-purple-700 uppercase tracking-wider block">Kontrol Penuh Platform</span>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Manajemen Seluruh Akun</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Kelola, monitor, dan ubah hak peran akun penyuruh maupun mitra yang disuruh.</p>
                        </div>

                        <!-- SEARCH BOX -->
                        <div class="relative w-full md:w-64">
                            <input 
                                type="text" 
                                id="userSearchInput" 
                                onkeyup="filterUserList()" 
                                placeholder="Cari nama atau email..." 
                                class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]"
                            >
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- FILTER TABS: SEMUA AKUN / PENYURUH / YANG DISURUH (KUNCI REQUEST USER) -->
                    <div class="flex items-center gap-2 mb-5 overflow-x-auto pb-1">
                        <button 
                            id="filterBtnAll" 
                            onclick="setAccountRoleFilter('all')" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-[#0471fc] text-white shadow-xs cursor-pointer flex items-center gap-1.5"
                        >
                            <span>Semua Akun</span>
                            <span class="bg-white/20 text-white text-[10px] px-1.5 py-0.2 rounded-full">{{ $allUsers->count() }}</span>
                        </button>

                        <button 
                            id="filterBtnPenyuruh" 
                            onclick="setAccountRoleFilter('penyuruh')" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:text-gray-900 cursor-pointer flex items-center gap-1.5"
                        >
                            <span>Akun Penyuruh (Customer)</span>
                            <span class="bg-blue-100 text-[#0471fc] text-[10px] px-1.5 py-0.2 rounded-full">{{ $adminStats['total_penyuruh'] }}</span>
                        </button>

                        <button 
                            id="filterBtnDisuruh" 
                            onclick="setAccountRoleFilter('disuruh')" 
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:text-gray-900 cursor-pointer flex items-center gap-1.5"
                        >
                            <span>Akun Yang Disuruh (Mitra)</span>
                            <span class="bg-emerald-100 text-emerald-700 text-[10px] px-1.5 py-0.2 rounded-full">{{ $adminStats['total_disuruh'] }}</span>
                        </button>
                    </div>

                    <!-- TABEL KONTROL AKUN -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-700" id="userAccountsTable">
                            <thead class="bg-gray-50 text-[11px] text-gray-400 uppercase font-bold border-b border-gray-100">
                                <tr>
                                    <th class="py-3 px-3">Nama Pengguna</th>
                                    <th class="py-3 px-3">Email &amp; No. HP</th>
                                    <th class="py-3 px-3">Peran (Role)</th>
                                    <th class="py-3 px-3">Aktivitas Tugas</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3 text-right">Kontrol Role</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($allUsers as $u)
                                    <tr class="user-row hover:bg-gray-50/70 transition-colors" data-role="{{ $u->role }}" data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}">
                                        <!-- User Profile -->
                                        <td class="py-3.5 px-3">
                                            <div class="flex items-center gap-3">
                                                @if($u->avatar)
                                                    <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-[#0471fc] font-bold text-xs flex items-center justify-center border border-blue-100">
                                                        {{ substr($u->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="font-bold text-gray-900 block leading-tight">{{ $u->name }}</span>
                                                    <span class="text-[10px] text-gray-400">ID: #{{ $u->id }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact -->
                                        <td class="py-3.5 px-3">
                                            <span class="font-medium text-gray-800 block">{{ $u->email }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $u->phone ?? 'Belum ada No. HP' }}</span>
                                        </td>

                                        <!-- Current Role Badge -->
                                        <td class="py-3.5 px-3">
                                            @if($u->role === 'penyuruh')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-[#0471fc] border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#0471fc]"></span>
                                                    Penyuruh
                                                </span>
                                            @elseif($u->role === 'disuruh')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Yang Disuruh
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    Admin
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Activity Stats -->
                                        <td class="py-3.5 px-3">
                                            @if($u->role === 'penyuruh')
                                                <span class="text-xs font-semibold text-gray-700"><b>{{ $u->orders_created_count }}</b> order dibuat</span>
                                            @elseif($u->role === 'disuruh')
                                                <span class="text-xs font-semibold text-gray-700"><b>{{ $u->orders_taken_count }}</b> tugas diambil</span>
                                            @else
                                                <span class="text-xs text-gray-400">Akses Penuh</span>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="py-3.5 px-3">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        </td>

                                        <!-- Action: Change Role -->
                                        <td class="py-3.5 px-3 text-right">
                                            <form action="{{ route('users.update-role', $u->id) }}" method="POST" class="inline-flex items-center gap-1.5 m-0">
                                                @csrf
                                                <select name="role" onchange="this.form.submit()" class="text-[11px] font-bold rounded-lg border border-gray-200 bg-white px-2 py-1 text-gray-700 focus:outline-none focus:border-[#0471fc] cursor-pointer shadow-xs">
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

            <!-- JIKA ROLE BUKAN ADMIN (PENYURUH / DISURUH): TAMPILKAN FINTECH DASHBOARD DOMPET -->
            @else
                <!-- TOP 2 CARDS -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
                    <!-- CARD 1: SALDO AKTIF / DOMPET (Cols 7/12) -->
                    <div class="lg:col-span-7 bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-7 shadow-xs border border-gray-100/80 flex flex-col justify-between relative overflow-hidden">
                        <div class="flex items-center justify-between gap-2 mb-6">
                            <div class="inline-flex items-center bg-gray-100 p-1 rounded-full text-[11px] font-bold text-gray-600">
                                <span class="bg-[#0471fc] text-white px-3 py-1 rounded-full shadow-xs">IDR</span>
                                <span class="px-2.5 py-1 text-gray-500">POIN</span>
                                <span class="px-2.5 py-1 text-gray-500">KOIN</span>
                            </div>

                            <div class="text-right">
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold block">Nomor Akun</span>
                                <div class="flex items-center gap-1.5 font-bold text-xs text-gray-700">
                                    <span>PS-90340222748</span>
                                    <button onclick="copyAccountNo()" class="text-gray-400 hover:text-[#0471fc] cursor-pointer" title="Salin">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <span class="text-xs font-semibold text-gray-400 block mb-1">Saldo Aktif Dompet</span>
                            <div class="flex items-center gap-3">
                                <h2 id="balanceAmount" class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight">
                                    Rp 1.450.000
                                </h2>
                                <button onclick="toggleBalance()" class="text-gray-400 hover:text-gray-600 cursor-pointer" title="Sembunyikan Saldo">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2.5">
                                <button class="bg-[#0471fc] hover:bg-[#0060db] text-white font-bold text-xs px-4 py-2.5 rounded-full shadow-xs transition-all active:scale-95 flex items-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Top Up Saldo</span>
                                </button>

                                <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs px-4 py-2.5 rounded-full transition-all active:scale-95 flex items-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Tarik Saldo</span>
                                </button>

                                <button class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm transition-all cursor-pointer">•••</button>
                            </div>

                            <!-- Quick Suruh Circle Button -->
                            <button onclick="switchMainTab('tasks'); openNewOrderModal();" class="w-12 h-12 rounded-full bg-[#f3c903] hover:bg-[#dfb700] text-gray-900 shadow-md flex items-center justify-center transition-all transform hover:scale-105 active:scale-95 cursor-pointer flex-shrink-0" title="Buat Order Suruh">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7V17"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- CARD 2: REWARDS & PRIORITAS (Cols 5/12) -->
                    <div class="lg:col-span-5 bg-gradient-to-br from-[#0471fc] to-[#38b6ff] rounded-[24px] sm:rounded-[28px] p-6 sm:p-7 text-white shadow-md relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-center justify-between mb-5 relative z-10">
                            <div class="inline-flex items-center gap-1.5 bg-black/20 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/20">
                                <span class="text-[11px] font-extrabold tracking-wider uppercase text-white">PRIORITAS</span>
                                <span class="w-4 h-4 rounded-full bg-[#f3c903] text-gray-900 font-bold text-xs flex items-center justify-center">+</span>
                            </div>
                            <button class="text-white/70 hover:text-white cursor-pointer">•••</button>
                        </div>

                        <div class="mb-5 relative z-10">
                            <span class="text-xs font-semibold text-white/80 block mb-1">Total Poin &amp; Hemat</span>
                            <div class="flex items-baseline gap-2.5 flex-wrap">
                                <h3 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Rp 385.000</h3>
                                <span class="bg-[#f3c903] text-gray-900 text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">+40% bulan ini</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 relative z-10">
                            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 border border-white/20 flex flex-col justify-between">
                                <div class="flex items-center justify-between text-white/70 text-[11px] mb-1">
                                    <span>Poin Suruh</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div class="flex items-center gap-1.5 font-black text-base text-white">
                                    <span class="text-[#f3c903]">★</span>
                                    <span>4.780</span>
                                </div>
                            </div>

                            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-3 border border-white/20 flex flex-col justify-between">
                                <div class="flex items-center justify-between text-white/70 text-[11px] mb-1">
                                    <span>Voucher Aktif</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div class="flex items-center gap-1.5 font-black text-base text-white">
                                    <svg class="w-4 h-4 text-[#f3c903]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                    <span>8 Kupon</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM 3 CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5 sm:gap-6">
                    <!-- BOTTOM 1: PENGELUARAN -->
                    <div class="lg:col-span-4 bg-white rounded-[24px] sm:rounded-[28px] p-6 shadow-xs border border-gray-100/80 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2 text-gray-700 font-bold text-sm">
                                <svg class="w-4 h-4 text-[#0471fc]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Pengeluaran</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">April 2026</span>
                        </div>

                        <div class="flex flex-col items-center justify-center my-3 relative">
                            <div class="relative w-36 h-36 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-100" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path class="text-[#0471fc]" stroke-dasharray="65, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                    <path class="text-[#f3c903]" stroke-dasharray="20, 100" stroke-dashoffset="-65" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                </svg>
                                <div class="absolute text-center flex flex-col items-center">
                                    <span class="text-xs text-gray-400 font-semibold leading-none">Total</span>
                                    <span class="text-lg font-black text-gray-900 tracking-tight mt-0.5">Rp 480rb</span>
                                    <span class="text-[10px] text-emerald-500 font-bold mt-0.5">+12%</span>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-gray-400 mt-2">Paling sering: Jasa Antar (65%)</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-[11px] pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-[#0471fc]"></span><span>Antar Paket: <b>Rp 312rb</b></span></div>
                            <div class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-[#f3c903]"></span><span>Bersih-bersih: <b>Rp 96rb</b></span></div>
                            <div class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span>Beli Makan: <b>Rp 48rb</b></span></div>
                            <div class="flex items-center gap-1.5 text-gray-600"><span class="w-2 h-2 rounded-full bg-gray-300"></span><span>Lainnya: <b>Rp 24rb</b></span></div>
                        </div>
                    </div>

                    <!-- BOTTOM 2: AKTIVITAS SURUH -->
                    <div class="lg:col-span-4 bg-white rounded-[24px] sm:rounded-[28px] p-6 shadow-xs border border-gray-100/80 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2 text-gray-700 font-bold text-sm">
                                <svg class="w-4 h-4 text-[#0471fc]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Aktivitas Suruh</span>
                            </div>
                            <span class="text-xs font-bold text-[#0471fc] bg-blue-50 px-2.5 py-1 rounded-full">24 Tugas</span>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">21 Selesai</h4>
                            <span class="text-xs font-semibold text-emerald-600">+18% dari bulan lalu</span>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div>
                                <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1"><span>Antar Paket &amp; Dokumen</span><span class="font-bold text-gray-900">12 tugas</span></div>
                                <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-[#0471fc] h-2 rounded-full" style="width: 58%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1"><span>Bersih Rumah &amp; Kost</span><span class="font-bold text-gray-900">6 tugas</span></div>
                                <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-[#f3c903] h-2 rounded-full" style="width: 28%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-semibold text-gray-600 mb-1"><span>Titip Belanja &amp; Antre</span><span class="font-bold text-gray-900">3 tugas</span></div>
                                <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-emerald-400 h-2 rounded-full" style="width: 14%"></div></div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold text-gray-500">
                            <span>Sedang Berjalan: <b>2 tugas</b></span>
                            <button onclick="switchMainTab('tasks')" class="text-[#0471fc] font-bold hover:underline cursor-pointer">Buka Tugas Suruh →</button>
                        </div>
                    </div>

                    <!-- BOTTOM 3: TRANSAKSI TERAKHIR -->
                    <div class="lg:col-span-4 bg-white rounded-[24px] sm:rounded-[28px] p-6 shadow-xs border border-gray-100/80 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2 text-gray-700 font-bold text-sm">
                                <svg class="w-4 h-4 text-[#0471fc]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Transaksi Terakhir</span>
                            </div>
                            <button class="text-gray-400 hover:text-gray-600 cursor-pointer">•••</button>
                        </div>

                        <div class="flex flex-col gap-3.5">
                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0471fc] flex items-center justify-center flex-shrink-0 font-bold text-sm">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    </div>
                                    <div><span class="text-xs font-bold text-gray-900 block leading-tight">Antar Dokumen Cepat</span><span class="text-[10px] text-gray-400">Hari ini, 10:15 WIB</span></div>
                                </div>
                                <span class="text-xs font-bold text-red-500 whitespace-nowrap">-Rp 25.000</span>
                            </div>

                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 font-bold text-sm">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <div><span class="text-xs font-bold text-gray-900 block leading-tight">Top Up Saldo</span><span class="text-[10px] text-gray-400">Kemarin, 14:20 WIB</span></div>
                                </div>
                                <span class="text-xs font-bold text-emerald-600 whitespace-nowrap">+Rp 200.000</span>
                            </div>

                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 font-bold text-sm">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <div><span class="text-xs font-bold text-gray-900 block leading-tight">Bersih Kamar Kost</span><span class="text-[10px] text-gray-400">10 Nov 2026</span></div>
                                </div>
                                <span class="text-xs font-bold text-red-500 whitespace-nowrap">-Rp 60.000</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 text-center">
                            <button class="text-xs font-bold text-[#0471fc] hover:underline cursor-pointer">Lihat Seluruh Mutasi</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- ================= TAB 2: TUGAS SURUH ================= -->
        <div id="sectionTasks" class="hidden flex flex-col gap-6">

            <!-- KONDISI A: ROLE PENYURUH -->
            @if($activeRole === 'penyuruh')
                <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-[#0471fc] uppercase tracking-wider block">Panel Penyuruh (Customer)</span>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Daftar Order Suruh Saya</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Pantau apakah order yang kamu buat sudah ada yang ambil atau masih menunggu pesuruh.</p>
                        </div>
                        <button onclick="openNewOrderModal()" class="bg-[#0471fc] hover:bg-[#0060db] active:scale-95 text-white font-bold text-xs sm:text-sm px-5 py-2.5 rounded-full shadow-md flex items-center gap-2 cursor-pointer transition-all">
                            <span class="text-base leading-none">+</span>
                            <span>Buat Order Suruh Baru</span>
                        </button>
                    </div>

                    @if($myOrders->isEmpty())
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-sm font-semibold text-gray-500">Belum ada order suruh yang kamu buat.</p>
                            <p class="text-xs text-gray-400 mt-1">Klik tombol "+ Buat Order Suruh Baru" di atas untuk mulai menyuruh mitra!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($myOrders as $order)
                                <div class="bg-gray-50/70 hover:bg-gray-50 border border-gray-200/70 rounded-2xl p-5 flex flex-col justify-between gap-4 transition-all hover:shadow-xs">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">{{ $order->service_type }}</span>
                                            <h3 class="text-sm font-bold text-gray-900 leading-snug">{{ $order->title }}</h3>
                                        </div>
                                        <span class="text-xs font-black text-[#0471fc] bg-blue-50 px-2.5 py-1 rounded-full whitespace-nowrap">
                                            Rp {{ number_format($order->price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-600 flex flex-col gap-1.5 bg-white p-3 rounded-xl border border-gray-100">
                                        @if($order->pickup_location)
                                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-[#0471fc]"></span><span class="text-gray-400 text-[11px]">Jemput:</span><span class="font-medium text-gray-800 truncate">{{ $order->pickup_location }}</span></div>
                                        @endif
                                        @if($order->destination_location)
                                            <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-[#f3c903]"></span><span class="text-gray-400 text-[11px]">Tujuan:</span><span class="font-medium text-gray-800 truncate">{{ $order->destination_location }}</span></div>
                                        @endif
                                        @if($order->description)
                                            <p class="text-[11px] text-gray-500 mt-1 italic line-clamp-2">"{{ $order->description }}"</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200/60">
                                        @if($order->status === 'menunggu')
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                                <span>Belum Ada yang Ambil (Menunggu Pesuruh)</span>
                                            </div>
                                            <span class="text-[11px] text-gray-400">Order Terbuka</span>
                                        @elseif($order->status === 'diambil')
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-[#0471fc] text-xs font-bold">
                                                <span class="w-2 h-2 rounded-full bg-[#0471fc]"></span>
                                                <span>Sudah Diambil: <b>{{ $order->disuruh->name ?? 'Mitra Pesuruh' }}</b></span>
                                            </div>
                                            <span class="text-[11px] text-emerald-600 font-bold">Sedang Dikerjakan</span>
                                        @elseif($order->status === 'selesai')
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                                                <span class="text-xs font-black">✓</span>
                                                <span>Tugas Selesai dikerjakan</span>
                                            </div>
                                            <span class="text-[11px] text-gray-400">Arsip</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            <!-- KONDISI B: ROLE DISURUH (MITRA) -->
            @elseif($activeRole === 'disuruh')
                <div class="flex flex-col gap-6">
                    <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                            <div>
                                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Panel Yang Disuruh (Mitra)</span>
                                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Order Terbuka Tersedia</h2>
                                <p class="text-xs text-gray-400 mt-0.5">Daftar order dari para penyuruh yang belum ada yang ambil. Klik "Ambil Order" untuk mulai bertugas.</p>
                            </div>
                            <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-full border border-emerald-200">
                                {{ $openOrders->count() }} Order Siap Diambil
                            </span>
                        </div>

                        @if($openOrders->isEmpty())
                            <div class="text-center py-10 text-gray-400">
                                <p class="text-sm font-semibold">Saat ini belum ada order baru yang terbuka.</p>
                                <p class="text-xs mt-1">Semua order sudah diambil oleh mitra lain atau belum ada penyuruh yang membuat order.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($openOrders as $order)
                                    <div class="bg-white border-2 border-dashed border-[#0471fc]/30 hover:border-[#0471fc] rounded-2xl p-5 flex flex-col justify-between gap-4 transition-all hover:shadow-md">
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-50 text-[#0471fc]">
                                                    {{ $order->service_type }}
                                                </span>
                                                <span class="text-xs font-extrabold text-emerald-600">
                                                    Rp {{ number_format($order->price, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            <h3 class="text-sm font-bold text-gray-900 leading-snug mb-1">{{ $order->title }}</h3>
                                            <span class="text-[11px] text-gray-400 block mb-3">Penyuruh: <b class="text-gray-700">{{ $order->penyuruh->name ?? 'Pengguna Jasa' }}</b></span>

                                            <div class="text-xs text-gray-600 flex flex-col gap-1.5 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                                                @if($order->pickup_location)
                                                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#0471fc]"></span><span class="text-[11px] truncate">Jemput: {{ $order->pickup_location }}</span></div>
                                                @endif
                                                @if($order->destination_location)
                                                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#f3c903]"></span><span class="text-[11px] truncate">Antar: {{ $order->destination_location }}</span></div>
                                                @endif
                                                @if($order->description)
                                                    <p class="text-[10px] text-gray-400 italic line-clamp-2 mt-0.5">"{{ $order->description }}"</p>
                                                @endif
                                            </div>
                                        </div>

                                        <form action="{{ route('orders.take', $order->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="w-full bg-[#0471fc] hover:bg-[#0060db] text-white font-bold text-xs py-2.5 rounded-xl shadow-xs transition-all active:scale-95 cursor-pointer">
                                                Ambil Order Ini →
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- TUGAS YANG SEDANG SAYA KERJAKAN -->
                    <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                        <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                            <div>
                                <h3 class="text-lg font-black text-gray-900">Tugas yang Sedang Saya Kerjakan</h3>
                                <p class="text-xs text-gray-400">Order yang sudah kamu ambil dan belum diselesaikan.</p>
                            </div>
                            <span class="text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">{{ $myTakenOrders->count() }} Aktif</span>
                        </div>

                        @if($myTakenOrders->isEmpty())
                            <p class="text-xs text-gray-400 py-4 text-center">Kamu belum mengambil tugas apapun. Pilih salah satu order terbuka di atas!</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($myTakenOrders as $order)
                                    <div class="bg-blue-50/50 border border-blue-200 rounded-2xl p-4 flex flex-col justify-between gap-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <span class="text-[10px] font-bold text-[#0471fc] uppercase">{{ $order->service_type }}</span>
                                                <h4 class="text-sm font-bold text-gray-900">{{ $order->title }}</h4>
                                                <span class="text-[11px] text-gray-500">Penyuruh: <b>{{ $order->penyuruh->name ?? 'User' }}</b></span>
                                            </div>
                                            <span class="text-xs font-bold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                                        </div>

                                        <form action="{{ route('orders.complete', $order->id) }}" method="POST" class="m-0 pt-2 border-t border-blue-100">
                                            @csrf
                                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2 rounded-xl transition-all cursor-pointer">
                                                Tandai Tugas Selesai ✓
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            <!-- KONDISI C: ROLE ADMIN -->
            @elseif($activeRole === 'admin')
                <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-purple-700 uppercase tracking-wider block">Panel Administrator</span>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Monitoring Order Aktif &amp; Terbuka</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Admin dapat melihat semua order terbuka yang menunggu diambil serta order yang sedang dikerjakan.</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold">
                            <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full">Menunggu: {{ $allOrders->where('status', 'menunggu')->count() }}</span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Diambil: {{ $allOrders->where('status', 'diambil')->count() }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-700">
                            <thead class="bg-gray-50 text-[11px] text-gray-400 uppercase font-bold border-b border-gray-100">
                                <tr>
                                    <th class="py-3 px-3">ID</th>
                                    <th class="py-3 px-3">Judul Tugas</th>
                                    <th class="py-3 px-3">Penyuruh</th>
                                    <th class="py-3 px-3">Yang Disuruh</th>
                                    <th class="py-3 px-3">Biaya</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($allOrders->whereIn('status', ['menunggu', 'diambil']) as $order)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="py-3 px-3 font-bold text-gray-400">#{{ $order->id }}</td>
                                        <td class="py-3 px-3">
                                            <span class="font-bold text-gray-900 block">{{ $order->title }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $order->service_type }}</span>
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-gray-800">{{ $order->penyuruh->name ?? '-' }}</td>
                                        <td class="py-3 px-3 font-semibold">
                                            @if($order->disuruh)
                                                <span class="text-[#0471fc]">{{ $order->disuruh->name }}</span>
                                            @else
                                                <span class="text-amber-600 italic">Belum Diambil</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 font-bold text-gray-900">Rp {{ number_format($order->price, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3">
                                            @if($order->status === 'menunggu')
                                                <span class="bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full text-[10px] font-bold">Terbuka (Menunggu)</span>
                                            @elseif($order->status === 'diambil')
                                                <span class="bg-blue-100 text-[#0471fc] px-2.5 py-0.5 rounded-full text-[10px] font-bold">Sedang Dikerjakan</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3">
                                            @if($order->status === 'diambil')
                                                <form action="{{ route('orders.complete', $order->id) }}" method="POST" class="inline m-0">
                                                    @csrf
                                                    <button type="submit" class="text-[11px] font-bold text-emerald-600 hover:underline cursor-pointer">Selesaikan</button>
                                                </form>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-400">Tidak ada order aktif saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        <!-- ================= TAB 3: RIWAYAT (SESUAI REQUEST USER) ================= -->
        <div id="sectionHistory" class="hidden flex flex-col gap-6">

            <!-- JIKA ROLE == ADMIN: MENAMPILKAN SELURUH RIWAYAT PEKERJAAN YANG SUDAH DIKERJAKAN -->
            @if($activeRole === 'admin')
                <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-purple-700 uppercase tracking-wider block">Riwayat Platform Admin</span>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Semua Riwayat Pekerjaan Selesai</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Daftar seluruh pekerjaan yang telah selesai dikerjakan oleh para mitra pesuruh untuk penyuruh.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold px-3.5 py-1.5 rounded-full">
                                {{ $completedOrders->count() }} Tugas Telah Selesai
                            </span>
                        </div>
                    </div>

                    @if($completedOrders->isEmpty())
                        <div class="text-center py-12 text-gray-400">
                            <p class="text-sm font-semibold">Belum ada riwayat pekerjaan yang selesai.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-gray-700">
                                <thead class="bg-gray-50 text-[11px] text-gray-400 uppercase font-bold border-b border-gray-100">
                                    <tr>
                                        <th class="py-3 px-3">No. Order</th>
                                        <th class="py-3 px-3">Pekerjaan</th>
                                        <th class="py-3 px-3">Penyuruh (Customer)</th>
                                        <th class="py-3 px-3">Yang Mengerjakan (Mitra)</th>
                                        <th class="py-3 px-3">Upah / Biaya</th>
                                        <th class="py-3 px-3">Waktu Selesai</th>
                                        <th class="py-3 px-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($completedOrders as $item)
                                        <tr class="hover:bg-gray-50/70 transition-colors">
                                            <td class="py-3.5 px-3 font-bold text-gray-400">#{{ $item->id }}</td>
                                            <td class="py-3.5 px-3">
                                                <span class="font-bold text-gray-900 block leading-tight">{{ $item->title }}</span>
                                                <span class="text-[10px] text-gray-400 font-semibold">{{ $item->service_type }}</span>
                                                @if($item->pickup_location || $item->destination_location)
                                                    <div class="text-[10px] text-gray-500 mt-0.5 truncate max-w-xs">
                                                        {{ $item->pickup_location ?? '-' }} → {{ $item->destination_location ?? 'Tujuan Sesuai Pesanan' }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <div class="font-semibold text-gray-900">{{ $item->penyuruh->name ?? 'User' }}</div>
                                                <span class="text-[10px] text-gray-400">{{ $item->penyuruh->email ?? '' }}</span>
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <div class="font-semibold text-emerald-700">{{ $item->disuruh->name ?? 'Mitra' }}</div>
                                                <span class="text-[10px] text-gray-400">{{ $item->disuruh->email ?? '' }}</span>
                                            </td>
                                            <td class="py-3.5 px-3 font-bold text-gray-900">
                                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3.5 px-3 text-gray-500 text-[11px]">
                                                {{ $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->translatedFormat('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="py-3.5 px-3 text-right">
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-[10px] font-black uppercase">
                                                    <span>✓</span> Selesai
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            <!-- JIKA ROLE == PENYURUH: RIWAYAT ORDER PRIBADI YANG SELESAI -->
            @elseif($activeRole === 'penyuruh')
                <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-[#0471fc] uppercase tracking-wider block">Riwayat Penyuruh</span>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Riwayat Tugas yang Telah Selesai</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Daftar order suruh kamu yang sudah selesai dikerjakan oleh mitra pesuruh.</p>
                        </div>
                    </div>

                    @php $myFinished = $myOrders->where('status', 'selesai'); @endphp
                    @if($myFinished->isEmpty())
                        <div class="text-center py-10 text-gray-400">
                            <p class="text-sm font-semibold">Belum ada tugas yang selesai.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($myFinished as $item)
                                <div class="bg-gray-50 border border-gray-200/70 rounded-2xl p-4 flex flex-col justify-between gap-3">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#0471fc] uppercase">{{ $item->service_type }}</span>
                                            <h4 class="text-sm font-bold text-gray-900">{{ $item->title }}</h4>
                                            <span class="text-[11px] text-gray-500">Dikerjakan oleh: <b>{{ $item->disuruh->name ?? 'Mitra' }}</b></span>
                                        </div>
                                        <span class="text-xs font-bold text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200/60 text-[11px] text-gray-400">
                                        <span>{{ $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->translatedFormat('d M Y') : 'Selesai' }}</span>
                                        <span class="text-emerald-600 font-bold">✓ Selesai</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            <!-- JIKA ROLE == DISURUH (MITRA): RIWAYAT TUGAS YANG SUDAH SAYA SELESAIKAN -->
            @elseif($activeRole === 'disuruh')
                <div class="bg-white rounded-[24px] sm:rounded-[28px] p-6 sm:p-8 shadow-xs border border-gray-100/80">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <div>
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Riwayat Mitra</span>
                            <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Pekerjaan yang Telah Saya Selesaikan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Daftar penghasilan dan riwayat tugas suruh yang sudah kamu selesaikan dengan sukses.</p>
                        </div>
                    </div>

                    @php 
                        $myCompletedJobs = \App\Models\Order::with('penyuruh')->where('disuruh_id', $user->id)->where('status', 'selesai')->latest()->get(); 
                    @endphp
                    @if($myCompletedJobs->isEmpty())
                        <div class="text-center py-10 text-gray-400">
                            <p class="text-sm font-semibold">Belum ada pekerjaan yang kamu selesaikan.</p>
                            <p class="text-xs mt-1">Ambil order terbuka di tab "Tugas Suruh" untuk mulai mendapatkan penghasilan!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($myCompletedJobs as $job)
                                <div class="bg-emerald-50/40 border border-emerald-200/70 rounded-2xl p-4 flex flex-col justify-between gap-3">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase">{{ $job->service_type }}</span>
                                            <h4 class="text-sm font-bold text-gray-900">{{ $job->title }}</h4>
                                            <span class="text-[11px] text-gray-500">Penyuruh: <b>{{ $job->penyuruh->name ?? 'Customer' }}</b></span>
                                        </div>
                                        <span class="text-xs font-black text-emerald-700">+Rp {{ number_format($job->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-2 border-t border-emerald-100 text-[11px] text-gray-400">
                                        <span>{{ $job->completed_at ? \Carbon\Carbon::parse($job->completed_at)->translatedFormat('d M Y') : 'Selesai' }}</span>
                                        <span class="text-emerald-700 font-bold">Bayaran Masuk Dompet ✓</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        </div>

    </div>

    <!-- ================= MODAL BUAT ORDER BARU (UNTUK PENYURUH) ================= -->
    <div id="newOrderModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-[24px] max-w-lg w-full p-6 shadow-2xl border border-gray-100 flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-lg font-black text-gray-900">Buat Order Suruh Baru</h3>
                <button onclick="closeNewOrderModal()" class="text-gray-400 hover:text-gray-600 font-bold text-sm cursor-pointer">✕</button>
            </div>

            <form action="{{ route('orders.create') }}" method="POST" class="flex flex-col gap-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Judul Tugas Suruh</label>
                    <input type="text" name="title" required placeholder="Contoh: Antar Dokumen ke Kantor Pos" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kategori Layanan</label>
                        <select name="service_type" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]">
                            <option value="Antar Barang">Antar Barang</option>
                            <option value="Bersih-bersih">Bersih-bersih</option>
                            <option value="Titip Belanja">Titip Belanja</option>
                            <option value="Antre">Antre &amp; Urusan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ongkos Tawaran (Rp)</label>
                        <input type="number" name="price" required value="25000" min="5000" step="5000" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi Penjemputan / Mulai</label>
                    <input type="text" name="pickup_location" placeholder="Contoh: Jl. Margonda No. 12" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi Tujuan (Opsional)</label>
                    <input type="text" name="destination_location" placeholder="Contoh: Gedung Rektorat Lt 3" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Instruksi Detail untuk Pesuruh</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan apa saja yang perlu dilakukan secara rinci..." class="w-full px-3.5 py-2 rounded-xl border border-gray-200 bg-gray-50 text-xs text-gray-900 focus:outline-none focus:border-[#0471fc]"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeNewOrderModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-[#0471fc] hover:bg-[#0060db] text-white shadow-xs cursor-pointer">Publikasikan Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Helper for interactive tabs & realtime account filtering -->
    <script>
        let isBalanceHidden = false;
        const rawBalance = "Rp 1.450.000";

        function toggleBalance() {
            const el = document.getElementById('balanceAmount');
            if (!el) return;
            if (isBalanceHidden) {
                el.innerText = rawBalance;
                isBalanceHidden = false;
            } else {
                el.innerText = "••••••••";
                isBalanceHidden = true;
            }
        }

        function copyAccountNo() {
            navigator.clipboard.writeText("PS-90340222748");
            alert("Nomor akun disalin ke clipboard!");
        }

        // Switch Antara Tab: Ringkasan / Tugas Suruh / Riwayat
        function switchMainTab(tab) {
            const sectionOverview = document.getElementById('sectionOverview');
            const sectionTasks = document.getElementById('sectionTasks');
            const sectionHistory = document.getElementById('sectionHistory');
            const navTabOverview = document.getElementById('navTabOverview');
            const navTabTasks = document.getElementById('navTabTasks');
            const navTabHistory = document.getElementById('navTabHistory');

            // Sembunyikan semua section
            sectionOverview.classList.add('hidden');
            sectionTasks.classList.add('hidden');
            sectionHistory.classList.add('hidden');

            // Reset class tombol nav
            [navTabOverview, navTabTasks, navTabHistory].forEach(btn => {
                if (btn) btn.className = 'px-4 py-1.5 rounded-full text-gray-600 hover:text-gray-900 transition-colors cursor-pointer flex items-center gap-1.5';
            });

            if (tab === 'overview') {
                sectionOverview.classList.remove('hidden');
                navTabOverview.className = 'px-4 py-1.5 rounded-full bg-[#0471fc] text-white shadow-xs transition-all cursor-pointer flex items-center gap-1.5';
            } else if (tab === 'tasks') {
                sectionTasks.classList.remove('hidden');
                navTabTasks.className = 'px-4 py-1.5 rounded-full bg-[#0471fc] text-white shadow-xs transition-all cursor-pointer flex items-center gap-1.5';
            } else if (tab === 'history') {
                sectionHistory.classList.remove('hidden');
                navTabHistory.className = 'px-4 py-1.5 rounded-full bg-[#0471fc] text-white shadow-xs transition-all cursor-pointer flex items-center gap-1.5';
            }
        }

        // Filter Akun Pengguna di Tab Ringkasan Admin (Semua / Penyuruh / Yang Disuruh)
        let currentRoleFilter = 'all';

        function setAccountRoleFilter(role) {
            currentRoleFilter = role;
            const btnAll = document.getElementById('filterBtnAll');
            const btnPenyuruh = document.getElementById('filterBtnPenyuruh');
            const btnDisuruh = document.getElementById('filterBtnDisuruh');

            if (!btnAll || !btnPenyuruh || !btnDisuruh) return;

            // Reset styling filter buttons
            btnAll.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:text-gray-900 cursor-pointer flex items-center gap-1.5';
            btnPenyuruh.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:text-gray-900 cursor-pointer flex items-center gap-1.5';
            btnDisuruh.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all bg-gray-100 text-gray-600 hover:text-gray-900 cursor-pointer flex items-center gap-1.5';

            if (role === 'all') {
                btnAll.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all bg-[#0471fc] text-white shadow-xs cursor-pointer flex items-center gap-1.5';
            } else if (role === 'penyuruh') {
                btnPenyuruh.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all bg-[#0471fc] text-white shadow-xs cursor-pointer flex items-center gap-1.5';
            } else if (role === 'disuruh') {
                btnDisuruh.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all bg-emerald-600 text-white shadow-xs cursor-pointer flex items-center gap-1.5';
            }

            filterUserList();
        }

        function filterUserList() {
            const query = (document.getElementById('userSearchInput')?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.user-row');

            rows.forEach(row => {
                const rowRole = row.getAttribute('data-role');
                const rowName = row.getAttribute('data-name');
                const rowEmail = row.getAttribute('data-email');

                const matchesRole = (currentRoleFilter === 'all') || (rowRole === currentRoleFilter);
                const matchesSearch = !query || rowName.includes(query) || rowEmail.includes(query);

                if (matchesRole && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openNewOrderModal() {
            document.getElementById('newOrderModal').classList.remove('hidden');
        }

        function closeNewOrderModal() {
            document.getElementById('newOrderModal').classList.add('hidden');
        }
    </script>
</body>
</html>
