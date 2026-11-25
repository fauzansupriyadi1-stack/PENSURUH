<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PenSuruh - Dashboard</title>
  <link rel="icon" type="image/png" href="/images/sources/hero_courier_clean.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/dashboard.css?v={{ filemtime(public_path('css/dashboard.css')) }}">
</head>
<body id="dashboard-page" data-active-role="{{ $activeRole ?? 'penyuruh' }}">

  @php
    $penyuruhCompleted = $myOrders->where('status', 'selesai');
    $mitraCompleted = $completedOrders->where('disuruh_id', $user->id ?? 0);
    $mitraAllTasks = $myTakenOrders->merge($mitraCompleted);
    $pesananBadgeCount = $activeRole === 'penyuruh'
        ? $myOrders->count()
        : ($activeRole === 'disuruh' ? $openOrders->count() : $allOrders->count());
    $riwayatBadgeCount = $activeRole === 'penyuruh'
        ? $penyuruhCompleted->count()
        : ($activeRole === 'disuruh' ? $mitraCompleted->count() : $completedOrders->count());
  @endphp

  <!-- ═══════════════════════════════════════════ -->
  <!-- HEADER (Responsive Desktop + Mobile)        -->
  <!-- ═══════════════════════════════════════════ -->
  <header class="dashboard-header">
    <div class="header-inner">
      <div class="header-left">
        <a href="/" class="brand-logo-text" aria-label="Beranda PenSuruh">
          <span class="brand-blue">Pen</span><span class="brand-yellow">Suruh</span>
        </a>

        <div class="header-user-block">
          @if(isset($user) && $user->avatar)
            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="user-avatar" style="object-fit:cover;">
          @else
            <div class="user-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}</div>
          @endif

          <div class="user-greeting">
            <small>Selamat Datang</small>
            <strong>
              <span class="user-name-truncate">{{ $user->name ?? 'Pengguna' }}</span>
              @if($activeRole === 'penyuruh')
                <span class="role-badge customer" id="current-role-badge">Penyuruh</span>
              @elseif($activeRole === 'disuruh')
                <span class="role-badge mitra" id="current-role-badge">Mitra</span>
              @else
                <span class="role-badge admin" id="current-role-badge">Admin</span>
              @endif
            </strong>
          </div>
        </div>

        <!-- Desktop Navigation Tabs -->
        <nav class="tab-nav desktop-only-nav" aria-label="Navigasi dashboard">
          <a href="#" class="tab-btn dashboard-tab-btn active" data-tab="ringkasan">Beranda</a>
          <a href="#" class="tab-btn dashboard-tab-btn" data-tab="tugas">
            Pesanan <span class="badge-count">{{ $pesananBadgeCount }}</span>
          </a>
          <a href="#" class="tab-btn dashboard-tab-btn" data-tab="riwayat">
            Riwayat <span class="badge-count">{{ $riwayatBadgeCount }}</span>
          </a>
        </nav>
      </div>

      <div class="header-right">
        @if($user && $user->role === 'admin')
        <!-- Admin Role Switcher -->
        <div class="mode-switcher desktop-only-mode" role="group" aria-label="Pilih mode role">
          <a href="{{ route('dashboard.switch-role', 'penyuruh') }}" class="mode-link {{ $activeRole === 'penyuruh' ? 'active' : '' }}" data-role="penyuruh">Penyuruh</a>
          <a href="{{ route('dashboard.switch-role', 'disuruh') }}" class="mode-link {{ $activeRole === 'disuruh' ? 'active' : '' }}" data-role="disuruh">Mitra</a>
          <a href="{{ route('dashboard.switch-role', 'admin') }}" class="mode-link {{ $activeRole === 'admin' ? 'active' : '' }}" data-role="admin">Admin</a>
        </div>
        @endif

        <a href="/" class="btn-header-link" title="Kembali ke Landing Page">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <span class="hide-mobile">Beranda Web</span>
        </a>

        <form action="/logout" method="POST" style="margin:0;">
          @csrf
          <button type="submit" class="btn-logout" title="Keluar akun">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="hide-mobile">Keluar</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Tablet & Mobile Subbar (Selalu tampil di <= 1080px) -->
    <div class="mobile-subbar">
      <div class="mobile-subbar-inner">
        <nav class="tab-nav" aria-label="Navigasi dashboard mobile">
          <a href="#" class="tab-btn dashboard-tab-btn active" data-tab="ringkasan">Beranda</a>
          <a href="#" class="tab-btn dashboard-tab-btn" data-tab="tugas">
            Pesanan <span class="badge-count">{{ $pesananBadgeCount }}</span>
          </a>
          <a href="#" class="tab-btn dashboard-tab-btn" data-tab="riwayat">
            Riwayat <span class="badge-count">{{ $riwayatBadgeCount }}</span>
          </a>
        </nav>

        @if($user && $user->role === 'admin')
        <div class="mode-switcher" role="group" aria-label="Pilih mode role mobile">
          <a href="{{ route('dashboard.switch-role', 'penyuruh') }}" class="mode-link {{ $activeRole === 'penyuruh' ? 'active' : '' }}" data-role="penyuruh">Penyuruh</a>
          <a href="{{ route('dashboard.switch-role', 'disuruh') }}" class="mode-link {{ $activeRole === 'disuruh' ? 'active' : '' }}" data-role="disuruh">Mitra</a>
          <a href="{{ route('dashboard.switch-role', 'admin') }}" class="mode-link {{ $activeRole === 'admin' ? 'active' : '' }}" data-role="admin">Admin</a>
        </div>
        @endif
      </div>
    </div>
  </header>

  <!-- Flash Messages (Floating Popup Toast) -->
  @if(session('success'))
    <div class="flash-alert" role="status" aria-live="polite">
      <div class="flash-alert-box success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('success') }}</span>
        <button type="button" class="flash-alert-close" onclick="dismissFlashAlert(this)" aria-label="Tutup notifikasi">✕</button>
      </div>
    </div>
  @endif
  @if(session('error'))
    <div class="flash-alert" role="alert">
      <div class="flash-alert-box error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ session('error') }}</span>
        <button type="button" class="flash-alert-close" onclick="dismissFlashAlert(this)" aria-label="Tutup notifikasi">✕</button>
      </div>
    </div>
  @endif
  @if($errors->any())
    <div class="flash-alert" role="alert">
      <div class="flash-alert-box error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
        <button type="button" class="flash-alert-close" onclick="dismissFlashAlert(this)" aria-label="Tutup notifikasi">✕</button>
      </div>
    </div>
  @endif

  <main class="main-content">

    <!-- ═══════════════════════════════════════════ -->
    <!-- VIEW: PENYURUH (Customer)                   -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="role-view" id="view-penyuruh" style="{{ $activeRole !== 'penyuruh' ? 'display:none;' : '' }}">

      <!-- Tab: Beranda -->
      <div class="tab-content active" data-tab-panel="ringkasan">

        <!-- Wallet Bar -->
        <section class="gopay-bar" aria-label="Dompet PenSuruh">
          <div class="gopay-wallet-info">
            <div class="gopay-balance-box">
              <div class="gopay-balance-label">
                <span>Saldo Dompet PenSuruh</span>
                <span style="font-family:monospace; opacity:0.85;">PS-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="gopay-balance-value">
                <span class="wallet-balance-amount" id="penyuruh-saldo" data-value="Rp 1.450.000">Rp 1.450.000</span>
                <button type="button" class="eye-toggle" onclick="toggleBalance()" aria-label="Sembunyikan saldo">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>

            <div class="gopay-mini-stats">
              <div class="gopay-mini-stat">
                <small>Poin Suruh</small>
                <strong class="yellow-highlight">★ 4.780</strong>
              </div>
              <div class="gopay-mini-stat">
                <small>Voucher Aktif</small>
                <strong id="penyuruh-voucher-count">8 Kupon</strong>
              </div>
            </div>
          </div>

          <div class="gopay-actions">
            <button type="button" class="btn-gopay" onclick="openTopUpModal()">+ Isi Saldo</button>
            <button type="button" class="btn-gopay-ghost" onclick="switchDashboardTab('tugas')">Pesanan Saya</button>
            <button type="button" class="btn-gopay-ghost" onclick="switchDashboardTab('riwayat')">Mutasi</button>
          </div>
        </section>

        <!-- Quick Service Grid -->
        <section aria-label="Pilih layanan jasa suruh">
          <div class="section-heading">
            <h2>Mau suruh apa hari ini?</h2>
            <small>Pilih layanan untuk langsung memesan pesuruh terdekat</small>
          </div>

          <div class="service-grid">
            <button type="button" class="service-tile selected" data-service="Antar Paket & Dokumen" data-price="15000" data-min="15000" onclick="selectService(this)">
              <div class="service-icon-circle blue" aria-hidden="true">📦</div>
              <span class="service-tile-name">Antar Paket</span>
              <span class="service-tile-price">Mulai Rp 15rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Beli Makan & Minum" data-price="12000" data-min="12000" onclick="selectService(this)">
              <div class="service-icon-circle red" aria-hidden="true">🍜</div>
              <span class="service-tile-name">Beli Makan</span>
              <span class="service-tile-price">Mulai Rp 12rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Titip Belanja & Antre" data-price="18000" data-min="18000" onclick="selectService(this)">
              <div class="service-icon-circle yellow" aria-hidden="true">🛒</div>
              <span class="service-tile-name">Titip Belanja</span>
              <span class="service-tile-price">Mulai Rp 18rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Bersih Rumah & Kost" data-price="45000" data-min="45000" onclick="selectService(this)">
              <div class="service-icon-circle teal" aria-hidden="true">🧹</div>
              <span class="service-tile-name">Bersih Kost</span>
              <span class="service-tile-price">Mulai Rp 45rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Jemput / Antar Orang" data-price="14000" data-min="14000" onclick="selectService(this)">
              <div class="service-icon-circle green" aria-hidden="true">🛵</div>
              <span class="service-tile-name">Antar Jemput</span>
              <span class="service-tile-price">Mulai Rp 14rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Suruh Custom / Lainnya" data-price="25000" data-min="5000" onclick="selectService(this)">
              <div class="service-icon-circle purple" aria-hidden="true">📝</div>
              <span class="service-tile-name">Suruh Custom</span>
              <span class="service-tile-price">Min Rp 5rb</span>
            </button>
          </div>
        </section>

        <!-- Order Form + Active Order Tracker -->
        <div class="penyuruh-workspace">

          <!-- Form Pesan Pesuruh -->
          <section class="card" id="order-form-card" aria-label="Form pemesanan pesuruh">
            <div class="card-header">
              <h2 class="card-title">
                <span class="card-title-icon">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                Pesan Pesuruh Sekarang
              </h2>
              <span style="font-size: 0.75rem; color: var(--accent-green); font-weight: 600;">● 14 Mitra Online</span>
            </div>

            <form id="orderForm" action="{{ route('orders.create') }}" method="POST">
              @csrf
              <input type="hidden" name="service_type" id="order-service-hidden" value="Antar Paket & Dokumen">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="order-service">Jenis Layanan</label>
                  <select id="order-service" class="form-input" onchange="onServiceSelectChange(this)">
                    <option value="Antar Paket & Dokumen" data-price="15000" data-min="15000" selected>Antar Paket &amp; Dokumen (Mulai Rp 15.000)</option>
                    <option value="Beli Makan & Minum" data-price="12000" data-min="12000">Beli Makan &amp; Minum (Mulai Rp 12.000)</option>
                    <option value="Titip Belanja & Antre" data-price="18000" data-min="18000">Titip Belanja &amp; Antre (Mulai Rp 18.000)</option>
                    <option value="Bersih Rumah & Kost" data-price="45000" data-min="45000">Bersih Rumah &amp; Kost (Mulai Rp 45.000)</option>
                    <option value="Jemput / Antar Orang" data-price="14000" data-min="14000">Jemput / Antar Orang (Mulai Rp 14.000)</option>
                    <option value="Suruh Custom / Lainnya" data-price="25000" data-min="5000">Suruh Custom / Lainnya (Min Rp 5.000)</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label" for="order-title">Judul Singkat Tugas</label>
                  <input type="text" id="order-title" name="title" class="form-input" value="Antar Paket & Dokumen" placeholder="Contoh: Antar Map ke Kampus" required>
                </div>
              </div>

              <!-- Custom Order Options (Hanya muncul saat pilih Suruh Custom / Lainnya) -->
              <div id="custom-order-fields" style="display:none; background:#eff6ff; border:1.5px dashed #93c5fd; border-radius:14px; padding:14px 16px; margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; gap:8px; flex-wrap:wrap;">
                  <span style="font-size:0.75rem; font-weight:700; color:var(--accent-blue); text-transform:uppercase; letter-spacing:0.04em;">Mode Suruh Custom</span>
                  <span style="font-size:0.75rem; color:var(--text-secondary);">Atur kategori &amp; estimasi waktu sesuai kebutuhanmu</span>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                  <label class="form-label" for="custom-service-name">Nama Kategori Custom <span style="font-weight:400; color:var(--text-muted);">(opsional)</span></label>
                  <input type="text" id="custom-service-name" class="form-input" style="background:#ffffff;" placeholder="Contoh: Servis Laptop / Antre Loket" oninput="syncCustomServiceType()">
                </div>
              </div>

              <!-- Harga Penawaran Custom (Berlaku untuk semua layanan dengan batas minimum masing-masing) -->
              <div class="form-group" id="price-customization-box" style="background:#f8fafc; border:1px solid var(--border); border-radius:14px; padding:14px 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap:8px; flex-wrap:wrap;">
                  <label class="form-label" for="order-offer-price" style="margin-bottom:0;">Ongkos / Harga Penawaran (Rp)</label>
                  <span id="price-min-badge" style="font-size:0.75rem; font-weight:700; color:var(--accent-blue); background:#eff6ff; border:1px solid #bfdbfe; padding:2px 10px; border-radius:9999px;">Minimal Rp 15.000</span>
                </div>
                <input type="number" id="order-offer-price" name="offer_price" class="form-input" style="background:#ffffff; font-weight:700; color:var(--accent-blue);" min="15000" step="1000" value="15000" required oninput="updateOrderTotal()">
                <div id="price-min-warning" style="display:none; font-size:0.75rem; font-weight:600; color:var(--accent-red); margin-top:6px;"></div>
                <div class="location-chips" id="price-quick-chips" style="margin-top:8px;">
                  <button type="button" class="location-chip" onclick="setOfferPrice(15000)">Rp 15rb</button>
                  <button type="button" class="location-chip" onclick="setOfferPrice(20000)">Rp 20rb</button>
                  <button type="button" class="location-chip" onclick="setOfferPrice(30000)">Rp 30rb</button>
                  <button type="button" class="location-chip" onclick="setOfferPrice(50000)">Rp 50rb</button>
                </div>
                <div style="font-size:0.6875rem; color:var(--text-secondary); margin-top:6px;">Bisa ubah harga lebih tinggi agar mitra lebih cepat mengambil tugasmu.</div>
              </div>

              <div class="form-group">
                <label class="form-label" for="order-pickup">Lokasi Jemput / Lokasi Tugas</label>
                <input type="text" id="order-pickup" name="pickup_location" class="form-input" placeholder="Contoh: Kost Kejawan Putih Tambak No. 12" required>
                <div class="location-chips">
                  <button type="button" class="location-chip" onclick="setPickup('Kampus PENS Sukolilo')">Kampus PENS</button>
                  <button type="button" class="location-chip" onclick="setPickup('Kost Kejawan Putih')">Kost Kejawan</button>
                  <button type="button" class="location-chip" onclick="setPickup('Galaxy Mall Surabaya')">Galaxy Mall</button>
                  <button type="button" class="location-chip" onclick="setPickup('Stasiun Gubeng')">Stasiun Gubeng</button>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="order-dropoff">Lokasi Tujuan <span style="font-weight:400; color:var(--text-muted);">(opsional)</span></label>
                <input type="text" id="order-dropoff" name="destination_location" class="form-input" placeholder="Contoh: Gedung D4 PENS / Jl. Arif Rahman Hakim">
              </div>

              <div class="form-group">
                <label class="form-label" for="order-notes">Detail Tugas untuk Pesuruh</label>
                <textarea id="order-notes" name="description" class="form-input" rows="2" placeholder="Contoh: Tolong antarkan map biru ke ruang TU lantai 2." required></textarea>
              </div>

              <div class="form-row">
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label" for="order-voucher">Pakai Voucher</label>
                  <select id="order-voucher" name="voucher_discount" class="form-input" onchange="updateOrderTotal()">
                    <option value="0">Tanpa Voucher</option>
                    <option value="5000">HEMAT5RB (-Rp 5.000)</option>
                    <option value="10000">SURUHKILAT (-Rp 10.000)</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label" for="order-custom-duration">Estimasi Waktu</label>
                  <div id="order-duration-fixed" class="form-input" style="background: var(--bg-gray); color: var(--text-secondary);">15 - 25 menit</div>
                  <input type="text" id="order-custom-duration" class="form-input" style="display:none;" placeholder="Contoh: 1 - 2 jam / Sore ini">
                </div>
              </div>

              <input type="hidden" name="price" id="order-price-hidden" value="15000">

              <div class="order-summary-box">
                <div>
                  <div class="total-label">Total Estimasi Ongkos</div>
                  <small id="order-summary-detail" style="color: var(--text-muted); font-size: 0.75rem;">Antar Paket & Dokumen</small>
                </div>
                <div class="total-price" id="order-total-price">Rp 15.000</div>
              </div>

              <button type="submit" class="btn-auth" id="btn-submit-order">Cari Pesuruh Sekarang</button>
            </form>
          </section>

          <!-- Right Column: Active Order Tracker + Reorder -->
          <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Pesanan Aktif -->
            <section class="card active-order-card" aria-label="Pesanan aktif">
              <div class="card-header">
                <h3 class="card-title">
                  <span class="card-title-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  </span>
                  Pesanan Aktif
                </h3>
                @php $aktif = $myOrders->whereIn('status', ['menunggu', 'diambil'])->first(); @endphp
                @if($aktif && $aktif->status === 'diambil')
                  <span class="order-status-pill">Mitra Menuju Lokasi</span>
                @elseif($aktif)
                  <span class="order-status-pill searching">Menunggu Mitra</span>
                @else
                  <span class="order-status-pill" style="background:#f1f5f9; color:#64748b;">Tidak Ada</span>
                @endif
              </div>

              @if($aktif)
                <div>
                  <div style="font-weight: 700; font-size: 0.9375rem; color: var(--text-primary);">{{ $aktif->title ?? $aktif->service_type }}</div>
                  <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 2px;">{{ $aktif->pickup_location ?? 'Lokasi Jemput' }} → {{ $aktif->destination_location ?? 'Lokasi Tugas' }}</div>
                  <div class="order-progress">
                    <div class="order-progress-step done"><div class="bar"></div>Dipesan</div>
                    <div class="order-progress-step {{ $aktif->status === 'diambil' ? 'done' : '' }}"><div class="bar"></div>Diambil</div>
                    <div class="order-progress-step {{ $aktif->status === 'diambil' ? 'done' : '' }}"><div class="bar"></div>Dikerjakan</div>
                    <div class="order-progress-step"><div class="bar"></div>Selesai</div>
                  </div>
                  @if($aktif->disuruh)
                    <div class="mitra-info-box">
                      <div class="mitra-avatar">{{ strtoupper(substr($aktif->disuruh->name ?? 'M', 0, 2)) }}</div>
                      <div style="flex:1; min-width:0;">
                        <div style="font-weight:700; font-size:0.875rem;">{{ $aktif->disuruh->name }}</div>
                        <div style="font-size:0.75rem; color:var(--text-secondary);">★ 4.9 · Sedang mengerjakan tugas</div>
                      </div>
                    </div>
                  @endif
                </div>
              @else
                <div style="text-align:center; padding: 24px 0; color: var(--text-muted);">
                  <div style="width: 44px; height: 44px; margin: 0 auto 10px; border-radius: 50%; background: #eff6ff; color: var(--accent-blue); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                  <div style="font-size:0.875rem;">Belum ada pesanan aktif.<br>Pesan pesuruh sekarang!</div>
                </div>
              @endif
            </section>

            <!-- Pesan Lagi -->
            <section class="card" aria-label="Pesan lagi tugas favorit">
              <div class="card-header">
                <h3 class="card-title">
                  <span class="card-title-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                  </span>
                  Pesan Lagi
                </h3>
                <a href="#" style="font-size: 0.75rem; font-weight: 600;" onclick="switchDashboardTab('tugas'); return false;">Lihat Semua</a>
              </div>

              @forelse($myOrders->take(3) as $ord)
                <div class="reorder-card">
                  <div style="min-width:0; flex:1;">
                    <div style="font-weight:600; font-size:0.875rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $ord->title ?? $ord->service_type }}</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $ord->pickup_location ?? '-' }} · Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</div>
                  </div>
                  <button
                    type="button"
                    class="btn btn-outline"
                    style="padding:6px 14px; font-size:0.75rem;"
                    data-service="{{ $ord->service_type ?? 'Antar Paket & Dokumen' }}"
                    data-title="{{ $ord->title ?? $ord->service_type }}"
                    data-price="{{ $ord->price ?? 25000 }}"
                    data-pickup="{{ $ord->pickup_location ?? '' }}"
                    data-dropoff="{{ $ord->destination_location ?? '' }}"
                    data-notes="{{ $ord->description ?? '' }}"
                    onclick="quickReorder(this)">Pesan</button>
                </div>
              @empty
                <div style="font-size:0.875rem; color:var(--text-muted); text-align:center; padding:16px 0;">Belum ada riwayat pesanan.</div>
              @endforelse
            </section>

          </div>
        </div>
      </div>

      <!-- Tab: Pesanan (Penyuruh) -->
      <div class="tab-content" data-tab-panel="tugas">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Daftar Pesanan Suruh Kamu</h3>
          </div>

          <div class="filter-tabs">
            <button type="button" class="filter-tab active" onclick="filterCustomerTasks(this, 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="filterCustomerTasks(this, 'aktif')">Aktif</button>
            <button type="button" class="filter-tab" onclick="filterCustomerTasks(this, 'done')">Selesai</button>
          </div>

          <ul class="task-list-compact" id="penyuruh-task-list">
            @forelse($myOrders as $ord)
              <li class="task-item-compact" data-status="{{ $ord->status === 'selesai' ? 'done' : 'aktif' }}">
                <span class="task-status-badge {{ $ord->status === 'selesai' ? 'done' : ($ord->status === 'diambil' ? 'aktif' : 'pending') }}">
                  {{ $ord->status === 'selesai' ? 'Selesai' : ($ord->status === 'diambil' ? 'Diambil' : 'Menunggu') }}
                </span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->title ?? $ord->service_type }}</div>
                  <div class="task-compact-meta">{{ $ord->created_at->format('d M Y') }} · Mitra: {{ $ord->disuruh->name ?? 'Belum ada' }}</div>
                </div>
                <span class="task-compact-price">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada pesanan.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Tab: Riwayat (Penyuruh) -->
      <div class="tab-content" data-tab-panel="riwayat">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Riwayat Mutasi Dompet</h3>
          </div>
          <ul class="transaction-list" id="penyuruh-history-list">
            @forelse($penyuruhCompleted as $ord)
              <li class="transaction-item">
                <div class="transaction-icon send" aria-hidden="true">↑</div>
                <div class="transaction-info">
                  <div class="transaction-name">{{ $ord->title ?? $ord->service_type }}</div>
                  <div class="transaction-date">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <span class="transaction-amount debit">-Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada riwayat transaksi.</li>
            @endforelse
          </ul>
        </div>
      </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- VIEW: YANG DISURUH (Mitra Pelaksana)        -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="role-view" id="view-disuruh" style="{{ $activeRole !== 'disuruh' ? 'display:none;' : '' }}">

      <div class="tab-content active" data-tab-panel="ringkasan">

        <!-- Mitra Online Toggle -->
        <section class="mitra-status-bar" aria-label="Status mitra online">
          <div>
            <div style="font-size:0.75rem; color:var(--text-secondary); font-weight:700; text-transform:uppercase; letter-spacing:0.04em;">Status Akun Mitra</div>
            <div style="font-size:1.0625rem; font-weight:700; margin-top:2px;" id="mitra-status-text">Online — Siap Menerima Tugas Suruh</div>
          </div>
          <button type="button" class="btn-toggle-online" id="btn-mitra-online" onclick="toggleMitraOnline(this)">Nonaktifkan Mode Terima Order</button>
        </section>

        <!-- Mitra Earnings + Rating -->
        <section class="wallet-section" aria-label="Pendapatan mitra">
          <div class="wallet-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; gap:8px; flex-wrap:wrap;">
              <span class="wallet-label">Pendapatan Tersedia (Bisa Ditarik)</span>
              <span style="font-size:0.75rem; color:var(--text-muted); font-family:monospace;">MTR-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="wallet-balance">
              <span class="wallet-balance-amount" data-value="Rp 875.000">Rp 875.000</span>
              <button type="button" class="eye-toggle" onclick="toggleBalance()" aria-label="Sembunyikan pendapatan">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="wallet-actions">
              <button type="button" class="btn btn-primary" onclick="alert('Penarikan dana mitra ke rekening diproses (Demo).')">Tarik Pendapatan</button>
              <button type="button" class="btn btn-outline" onclick="switchDashboardTab('riwayat')">Lihat Slip Komisi</button>
            </div>
          </div>

          <div class="priority-card">
            <div class="priority-label">PERFORMA MITRA</div>
            <div style="margin-bottom:4px; font-size:0.8125rem; color:rgba(255,255,255,0.72);">Pendapatan Minggu Ini</div>
            <div class="priority-balance">
              Rp {{ number_format($mitraCompleted->sum('price'), 0, ',', '.') }}
              <span class="priority-change">{{ $mitraCompleted->count() }} Tugas Selesai</span>
            </div>
            <div class="priority-stats">
              <div class="priority-stat">
                <div class="priority-stat-label">Rating Pelanggan</div>
                <div class="priority-stat-value"><span class="star">★</span> 4.92</div>
              </div>
              <div class="priority-stat">
                <div class="priority-stat-label">Tingkat Terima</div>
                <div class="priority-stat-value">96%</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Order Masuk + Tugas Berjalan -->
        <div class="grid-2">
          <!-- Order Masuk -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Orderan Masuk di Sekitarmu</h3>
              <span style="font-size:0.75rem; color:var(--accent-blue); font-weight:600;">Radius 3 km</span>
            </div>

            @forelse($openOrders->take(5) as $ord)
              <div class="incoming-order-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:6px;">
                  <strong style="font-size:0.9375rem;">{{ $ord->title ?? $ord->service_type }}</strong>
                  <span style="font-weight:800; color:var(--accent-green); white-space:nowrap;">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                </div>
                <div style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:10px;">
                  {{ $ord->pickup_location ?? 'Lokasi Tugas' }} → {{ $ord->destination_location ?? 'Tujuan' }}
                </div>
                <form action="{{ route('orders.take', $ord->slug) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-primary" style="width:100%;">Ambil Order Ini</button>
                </form>
              </div>
            @empty
              <div style="text-align:center; padding:28px; color:var(--text-muted); font-size:0.875rem;">
                Tidak ada orderan masuk saat ini.<br>Tetap online untuk menerima tugas!
              </div>
            @endforelse
          </div>

          <!-- Tugas Yang Harus Dikerjakan -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Tugas Yang Harus Dikerjakan</h3>
            </div>
            <ul class="task-list-compact" id="mitra-active-tasks">
              @forelse($myTakenOrders as $ord)
                <li class="task-item-compact">
                  <span class="task-status-badge aktif">Berjalan</span>
                  <div class="task-compact-info">
                    <div class="task-compact-title">{{ $ord->title ?? $ord->service_type }}</div>
                    <div class="task-compact-meta">Pemesan: {{ $ord->penyuruh->name ?? '-' }}</div>
                  </div>
                  <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                    <span class="task-compact-price">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                    <form action="{{ route('orders.complete', $ord->slug) }}" method="POST" style="margin:0;">
                      @csrf
                      <button type="submit" class="btn btn-outline" style="padding:4px 12px; font-size:0.75rem;">Selesai</button>
                    </form>
                  </div>
                </li>
              @empty
                <li style="padding:24px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada tugas aktif.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      <!-- Tab: Tugas (Mitra) -->
      <div class="tab-content" data-tab-panel="tugas">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Semua Tugas Mitra</h3>
          </div>
          <ul class="task-list-compact">
            @forelse($mitraAllTasks as $ord)
              <li class="task-item-compact">
                <span class="task-status-badge {{ $ord->status === 'selesai' ? 'done' : 'aktif' }}">{{ $ord->status === 'selesai' ? 'Selesai' : 'Berjalan' }}</span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->title ?? $ord->service_type }}</div>
                  <div class="task-compact-meta">Pemesan: {{ $ord->penyuruh->name ?? '-' }}</div>
                </div>
                <span class="task-compact-price">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted);">Belum ada tugas.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Tab: Riwayat (Mitra) -->
      <div class="tab-content" data-tab-panel="riwayat">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Riwayat Komisi & Pencairan Mitra</h3>
          </div>
          <ul class="transaction-list">
            @forelse($mitraCompleted as $ord)
              <li class="transaction-item">
                <div class="transaction-icon receive" aria-hidden="true">+</div>
                <div class="transaction-info">
                  <div class="transaction-name">Komisi Tugas: {{ $ord->title ?? $ord->service_type }}</div>
                  <div class="transaction-date">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <span class="transaction-amount credit">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted);">Belum ada riwayat.</li>
            @endforelse
          </ul>
        </div>
      </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- VIEW: ADMIN (Kontrol Penuh)                 -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="role-view" id="view-admin" style="{{ $activeRole !== 'admin' ? 'display:none;' : '' }}">

      <div class="tab-content active" data-tab-panel="ringkasan">

        <!-- Admin Stat Cards -->
        <div class="stat-cards">
          <div class="stat-card">
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-value">{{ $adminStats['total_users'] }}</div>
            <div class="stat-sub">Semua Role</div>
          </div>
          <div class="stat-card">
            <div class="stat-label" style="color:var(--accent-blue);">Akun Penyuruh</div>
            <div class="stat-value">{{ $adminStats['total_penyuruh'] }}</div>
            <div class="stat-sub">Pemberi Tugas</div>
          </div>
          <div class="stat-card">
            <div class="stat-label" style="color:var(--accent-green);">Akun Mitra</div>
            <div class="stat-value">{{ $adminStats['total_disuruh'] }}</div>
            <div class="stat-sub">Pelaksana</div>
          </div>
          <div class="stat-card highlight">
            <div class="stat-label">Transaksi Selesai</div>
            <div class="stat-value">Rp {{ number_format($adminStats['total_turnover'], 0, ',', '.') }}</div>
            <div class="stat-sub">{{ $adminStats['completed_count'] }} Order Selesai</div>
          </div>
        </div>

        <!-- User Management -->
        <div class="management-section">
          <div class="management-header">
            <div>
              <div class="management-label">KONTROL PENUH PLATFORM</div>
              <h2 class="management-title">Manajemen Seluruh Akun</h2>
              <p class="management-desc">Kelola, monitor, dan ubah hak peran akun penyuruh maupun mitra.</p>
            </div>
            <div class="search-box">
              <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="admin-search" placeholder="Cari nama atau email.." onkeyup="filterUserList()">
            </div>
          </div>

          <!-- Filter Tabs -->
          <div class="filter-tabs">
            <button type="button" class="filter-tab admin-filter-tab active" data-filter="all">Semua <span class="count">{{ $allUsers->count() }}</span></button>
            <button type="button" class="filter-tab admin-filter-tab" data-filter="penyuruh">Penyuruh <span class="count">{{ $adminStats['total_penyuruh'] }}</span></button>
            <button type="button" class="filter-tab admin-filter-tab" data-filter="disuruh">Mitra <span class="count">{{ $adminStats['total_disuruh'] }}</span></button>
          </div>

          <!-- Responsive Scrollable User Table -->
          <div class="table-responsive">
            <table class="user-table" id="userAccountsTable">
              <thead>
                <tr>
                  <th>Nama Pengguna</th>
                  <th>Email &amp; No. HP</th>
                  <th>Peran</th>
                  <th>Aktivitas Tugas</th>
                  <th>Status</th>
                  <th>Kontrol Role</th>
                </tr>
              </thead>
              <tbody>
                @foreach($allUsers as $u)
                  <tr data-role="{{ $u->role }}" data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email ?? '') }}">
                    <td>
                      <div class="user-info-cell">
                        @if($u->avatar)
                          <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="user-avatar-sm" style="object-fit:cover;">
                        @else
                          <div class="user-avatar-sm {{ $u->role === 'admin' ? 'purple' : ($u->role === 'disuruh' ? 'green' : '') }}">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                        @endif
                        <div>
                          <div class="user-name-cell">{{ $u->name }}</div>
                          <div class="user-id-cell">ID: #{{ $u->id }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="user-email-cell">{{ $u->email ?? '-' }}</div>
                      <div class="user-email-cell sub">{{ $u->phone ?? 'Belum ada No. HP' }}</div>
                    </td>
                    <td>
                      <span class="role-pill {{ $u->role === 'admin' ? 'admin' : ($u->role === 'disuruh' ? 'mitra' : 'customer') }}">{{ strtoupper($u->role) }}</span>
                    </td>
                    <td>
                      @if($u->role === 'penyuruh')
                        <span style="color:var(--text-secondary);"><b>{{ $u->orders_created_count }}</b> order dibuat</span>
                      @elseif($u->role === 'disuruh')
                        <span style="color:var(--text-secondary);"><b>{{ $u->orders_taken_count }}</b> tugas diambil</span>
                      @else
                        <span style="color:var(--text-secondary);">Akses Penuh</span>
                      @endif
                    </td>
                    <td><span class="status-dot">Aktif</span></td>
                    <td>
                      <form action="{{ route('users.update-role', $u->slug) }}" method="POST" style="margin:0;">
                        @csrf
                        <select name="role" onchange="this.form.submit()" class="role-select" aria-label="Ubah role {{ $u->name }}">
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

      <!-- Tab: Tugas (Admin) -->
      <div class="tab-content" data-tab-panel="tugas">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Monitoring Seluruh Pesanan Platform</h3>
          </div>
          <ul class="task-list-compact">
            @forelse($allOrders as $ord)
              <li class="task-item-compact">
                <span class="task-status-badge {{ $ord->status === 'selesai' ? 'done' : ($ord->status === 'diambil' ? 'aktif' : 'pending') }}">
                  {{ $ord->status === 'selesai' ? 'Selesai' : ($ord->status === 'diambil' ? 'Diambil' : 'Menunggu') }}
                </span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->title ?? $ord->service_type }}</div>
                  <div class="task-compact-meta">Penyuruh: {{ $ord->penyuruh->name ?? '-' }} · Mitra: {{ $ord->disuruh->name ?? 'Belum ada' }}</div>
                </div>
                <span class="task-compact-price">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted);">Belum ada pesanan di platform.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Tab: Riwayat (Admin) -->
      <div class="tab-content" data-tab-panel="riwayat">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Log Transaksi Platform</h3>
          </div>
          <ul class="transaction-list">
            @forelse($completedOrders as $ord)
              <li class="transaction-item">
                <div class="transaction-icon receive" aria-hidden="true">✓</div>
                <div class="transaction-info">
                  <div class="transaction-name">Order #PS-{{ $ord->id }} · {{ $ord->title ?? $ord->service_type }}</div>
                  <div class="transaction-date">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <span class="transaction-amount credit">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted);">Belum ada transaksi selesai.</li>
            @endforelse
          </ul>
        </div>
      </div>

    </div>

  </main>

  <!-- ═══════════════════════════════════════════ -->
  <!-- MODAL: TOP UP SALDO                         -->
  <!-- ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="topup-modal" role="dialog" aria-modal="true" aria-labelledby="topup-modal-title">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="topup-modal-title" style="font-size:1.125rem; font-weight:700;">Isi Saldo Dompet PenSuruh</h3>
        <button type="button" class="modal-close" onclick="closeTopUpModal()" aria-label="Tutup">✕</button>
      </div>
      <p style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:14px;">Pilih nominal pengisian saldo (simulasi front-end):</p>
      <div class="nominal-grid">
        <button type="button" class="nominal-btn" onclick="selectTopUpNominal(this, 50000)">Rp 50.000</button>
        <button type="button" class="nominal-btn selected" onclick="selectTopUpNominal(this, 100000)">Rp 100.000</button>
        <button type="button" class="nominal-btn" onclick="selectTopUpNominal(this, 200000)">Rp 200.000</button>
        <button type="button" class="nominal-btn" onclick="selectTopUpNominal(this, 300000)">Rp 300.000</button>
        <button type="button" class="nominal-btn" onclick="selectTopUpNominal(this, 500000)">Rp 500.000</button>
        <button type="button" class="nominal-btn" onclick="selectTopUpNominal(this, 1000000)">Rp 1.000.000</button>
      </div>
      <div class="form-group">
        <label class="form-label" for="topup-method">Metode Pembayaran</label>
        <select id="topup-method" class="form-input">
          <option>QRIS (Instan)</option>
          <option>Transfer Bank (BCA / BRI / Mandiri)</option>
          <option>Minimarket (Indomaret / Alfamart)</option>
        </select>
      </div>
      <button type="button" class="btn-auth" onclick="confirmTopUp()">Konfirmasi Isi Saldo</button>
    </div>
  </div>

  <script>
    function dismissFlashAlert(el) {
      const alertEl = el?.closest ? el.closest('.flash-alert') : el;
      if (!alertEl) return;
      alertEl.classList.add('hiding');
      setTimeout(function() { alertEl.remove(); }, 280);
    }

    document.querySelectorAll('.flash-alert').forEach(function(alertEl) {
      setTimeout(function() { dismissFlashAlert(alertEl); }, 4000);
    });

    let customerBalance = 1450000;
    let selectedTopUpNominal = 100000;
    let customTitleEdited = false;

    const titleInput = document.getElementById('order-title');
    if (titleInput) {
      titleInput.addEventListener('input', function() {
        customTitleEdited = this.value.trim().length > 0;
      });
    }

    // Tab switching (sinkron desktop + mobile nav)
    function switchDashboardTab(tabName) {
      document.querySelectorAll('.dashboard-tab-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.tab === tabName);
      });

      const role = document.getElementById('dashboard-page')?.dataset.activeRole || 'penyuruh';
      const activeView = document.getElementById('view-' + role);
      if (!activeView) return;

      activeView.querySelectorAll('.tab-content').forEach(function(content) {
        const isTarget = content.dataset.tabPanel === tabName;
        content.classList.toggle('active', isTarget);
        content.style.display = isTarget ? 'block' : 'none';
      });
    }

    document.querySelectorAll('.dashboard-tab-btn').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        switchDashboardTab(this.dataset.tab);
      });
    });

    // Balance toggle
    function toggleBalance() {
      document.querySelectorAll('.wallet-balance-amount').forEach(function(el) {
        if (el.dataset.hidden === 'true') {
          el.textContent = el.dataset.value;
          el.dataset.hidden = 'false';
        } else {
          el.dataset.value = el.textContent;
          el.textContent = '••••••••';
          el.dataset.hidden = 'true';
        }
      });
    }

    // Top Up Modal
    function openTopUpModal() {
      document.getElementById('topup-modal')?.classList.add('open');
    }
    function closeTopUpModal() {
      document.getElementById('topup-modal')?.classList.remove('open');
    }
    document.getElementById('topup-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeTopUpModal();
    });

    function selectTopUpNominal(el, val) {
      selectedTopUpNominal = val;
      document.querySelectorAll('.nominal-btn').forEach(function(b) { b.classList.remove('selected'); });
      el.classList.add('selected');
    }

    function confirmTopUp() {
      customerBalance += selectedTopUpNominal;
      const formatted = 'Rp ' + customerBalance.toLocaleString('id-ID');
      const saldoEl = document.getElementById('penyuruh-saldo');
      if (saldoEl) {
        saldoEl.dataset.value = formatted;
        if (saldoEl.dataset.hidden !== 'true') saldoEl.textContent = formatted;
      }
      closeTopUpModal();
    }

    // Mitra Online Toggle
    function toggleMitraOnline(btn) {
      const text = document.getElementById('mitra-status-text');
      const isOffline = btn.classList.toggle('offline');
      if (isOffline) {
        btn.textContent = 'Aktifkan Mode Terima Order';
        if (text) text.textContent = 'Offline — Tidak Menerima Tugas';
      } else {
        btn.textContent = 'Nonaktifkan Mode Terima Order';
        if (text) text.textContent = 'Online — Siap Menerima Tugas Suruh';
      }
    }

    // Filter customer tasks
    function filterCustomerTasks(btn, status) {
      btn.closest('.filter-tabs').querySelectorAll('.filter-tab').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      document.querySelectorAll('#penyuruh-task-list .task-item-compact').forEach(function(li) {
        li.style.display = (status === 'all' || li.dataset.status === status) ? '' : 'none';
      });
    }

    // Admin search & filter
    function filterUserList() {
      const val = document.getElementById('admin-search')?.value.toLowerCase() || '';
      document.querySelectorAll('#userAccountsTable tbody tr').forEach(function(row) {
        const name = row.dataset.name || '';
        const email = row.dataset.email || '';
        row.style.display = (name.includes(val) || email.includes(val)) ? '' : 'none';
      });
    }

    document.querySelectorAll('.admin-filter-tab').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.admin-filter-tab').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('#userAccountsTable tbody tr').forEach(function(row) {
          row.style.display = (filter === 'all' || row.dataset.role === filter) ? '' : 'none';
        });
      });
    });

    // Location & Service Selection
    const CUSTOM_SERVICE_VALUE = 'Suruh Custom / Lainnya';

    function setPickup(loc) {
      const el = document.getElementById('order-pickup');
      if (el) el.value = loc;
    }

    function isCustomServiceSelected() {
      const sel = document.getElementById('order-service');
      return sel && sel.value === CUSTOM_SERVICE_VALUE;
    }

    function getSelectedServiceMeta() {
      const sel = document.getElementById('order-service');
      const opt = sel?.options[sel.selectedIndex];
      return {
        name: sel ? sel.value : 'Antar Paket & Dokumen',
        defaultPrice: parseInt(opt?.dataset.price || 15000, 10),
        minPrice: parseInt(opt?.dataset.min || 5000, 10),
      };
    }

    function syncCustomServiceType() {
      const hiddenService = document.getElementById('order-service-hidden');
      const sel = document.getElementById('order-service');
      const customName = document.getElementById('custom-service-name')?.value.trim() || '';
      if (!hiddenService || !sel) return;

      if (sel.value === CUSTOM_SERVICE_VALUE && customName) {
        hiddenService.value = 'Custom: ' + customName;
      } else {
        hiddenService.value = sel.value;
      }

      const detail = document.getElementById('order-summary-detail');
      if (detail) detail.textContent = hiddenService.value;
    }

    function renderPriceQuickChips(minPrice) {
      const container = document.getElementById('price-quick-chips');
      if (!container) return;
      const presets = minPrice <= 5000
        ? [5000, 15000, 25000, 50000]
        : [minPrice, minPrice + 5000, minPrice + 15000, minPrice + 35000];

      container.innerHTML = presets.map(function(val) {
        return '<button type="button" class="location-chip" onclick="setOfferPrice(' + val + ')">Rp ' + (val / 1000) + 'rb</button>';
      }).join('');
    }

    function applyServicePricingMeta() {
      const meta = getSelectedServiceMeta();
      const priceInput = document.getElementById('order-offer-price');
      const minBadge = document.getElementById('price-min-badge');

      if (priceInput) {
        priceInput.min = meta.minPrice;
        priceInput.value = meta.defaultPrice;
        priceInput.placeholder = 'Minimal Rp ' + meta.minPrice.toLocaleString('id-ID');
      }
      if (minBadge) {
        minBadge.textContent = 'Minimal Rp ' + meta.minPrice.toLocaleString('id-ID');
      }
      renderPriceQuickChips(meta.minPrice);
    }

    function toggleCustomMode(isCustom) {
      const customPanel = document.getElementById('custom-order-fields');
      const durationFixed = document.getElementById('order-duration-fixed');
      const durationCustom = document.getElementById('order-custom-duration');

      if (customPanel) customPanel.style.display = isCustom ? 'block' : 'none';
      if (durationFixed) durationFixed.style.display = isCustom ? 'none' : 'block';
      if (durationCustom) durationCustom.style.display = isCustom ? 'block' : 'none';

      if (titleInput) {
        titleInput.placeholder = isCustom
          ? 'Contoh: Bantu rakit meja belajar / Antre berkas'
          : 'Contoh: Antar Map ke Kampus';
      }

      syncCustomServiceType();
    }

    function setOfferPrice(amount) {
      const priceInput = document.getElementById('order-offer-price');
      if (priceInput) {
        priceInput.value = amount;
      }
      updateOrderTotal();
    }

    function onServiceSelectChange(sel) {
      const service = sel.value;
      document.querySelectorAll('.service-tile').forEach(function(t) {
        t.classList.toggle('selected', t.dataset.service === service);
      });
      if (!customTitleEdited && titleInput) {
        titleInput.value = service === CUSTOM_SERVICE_VALUE ? '' : service;
      }
      applyServicePricingMeta();
      toggleCustomMode(service === CUSTOM_SERVICE_VALUE);
      updateOrderTotal();
    }

    function selectService(el) {
      document.querySelectorAll('.service-tile').forEach(function(t) { t.classList.remove('selected'); });
      el.classList.add('selected');
      const service = el.dataset.service;
      const sel = document.getElementById('order-service');
      if (sel) sel.value = service;
      if (!customTitleEdited && titleInput) {
        titleInput.value = service === CUSTOM_SERVICE_VALUE ? '' : service;
      }
      applyServicePricingMeta();
      toggleCustomMode(service === CUSTOM_SERVICE_VALUE);
      updateOrderTotal();
      document.getElementById('order-form-card')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function updateOrderTotal() {
      const meta = getSelectedServiceMeta();
      const priceInput = document.getElementById('order-offer-price');
      const warningEl = document.getElementById('price-min-warning');

      const rawInput = parseInt(priceInput?.value || '0', 10);
      const isBelowMin = isNaN(rawInput) || rawInput < meta.minPrice;

      if (warningEl && priceInput) {
        if (isBelowMin) {
          warningEl.style.display = 'block';
          warningEl.textContent = 'Ongkos minimal untuk layanan ini adalah Rp ' + meta.minPrice.toLocaleString('id-ID') + '.';
          priceInput.style.borderColor = 'var(--accent-red)';
        } else {
          warningEl.style.display = 'none';
          priceInput.style.borderColor = '';
        }
      }

      const validOffer = isNaN(rawInput) ? meta.minPrice : Math.max(meta.minPrice, rawInput);
      const voucher = parseInt(document.getElementById('order-voucher')?.value || 0, 10);
      const total = Math.max(5000, validOffer - voucher);

      const hiddenPrice = document.getElementById('order-price-hidden');
      if (hiddenPrice) hiddenPrice.value = total;

      const el = document.getElementById('order-total-price');
      if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');

      syncCustomServiceType();
    }

    document.getElementById('orderForm')?.addEventListener('submit', function(e) {
      const meta = getSelectedServiceMeta();
      const priceInput = document.getElementById('order-offer-price');
      const rawInput = parseInt(priceInput?.value || '0', 10);

      if (isNaN(rawInput) || rawInput < meta.minPrice) {
        e.preventDefault();
        updateOrderTotal();
        priceInput?.focus();
        return;
      }

      syncCustomServiceType();
      updateOrderTotal();

      if (isCustomServiceSelected()) {
        const customDur = document.getElementById('order-custom-duration')?.value.trim();
        const notes = document.getElementById('order-notes');
        if (customDur && notes && !notes.value.includes('[Estimasi Waktu:')) {
          notes.value = notes.value.trim() + '\n[Estimasi Waktu: ' + customDur + ']';
        }
      }
    });

    function quickReorder(btn) {
      const sel = document.getElementById('order-service');
      const rawService = btn.dataset.service || '';
      if (sel && rawService) {
        const hasExactOption = Array.from(sel.options).some(o => o.value === rawService);
        if (hasExactOption) {
          sel.value = rawService;
        } else {
          sel.value = CUSTOM_SERVICE_VALUE;
          const customInput = document.getElementById('custom-service-name');
          if (customInput) customInput.value = rawService.replace(/^Custom:\s*/i, '');
        }
        onServiceSelectChange(sel);
        if (btn.dataset.price) {
          const meta = getSelectedServiceMeta();
          const savedPrice = parseInt(btn.dataset.price, 10) || meta.defaultPrice;
          setOfferPrice(Math.max(meta.minPrice, savedPrice));
        }
      }
      if (titleInput && btn.dataset.title) {
        titleInput.value = btn.dataset.title;
        customTitleEdited = true;
      }
      setPickup(btn.dataset.pickup || '');
      const drop = document.getElementById('order-dropoff');
      if (drop) drop.value = btn.dataset.dropoff || '';
      const notes = document.getElementById('order-notes');
      if (notes) notes.value = btn.dataset.notes || '';
      document.getElementById('order-form-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  </script>
</body>
</html>
