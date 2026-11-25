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
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
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
                <span class="wallet-balance-amount" id="penyuruh-saldo" data-value="Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</span>
                <button type="button" class="eye-toggle" onclick="toggleBalance()" aria-label="Sembunyikan saldo">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>

            <div class="gopay-mini-stats">
              <div class="gopay-mini-stat">
                <small>Poin Suruh</small>
                <strong class="yellow-highlight">★ {{ number_format($myOrders->where('status', 'selesai')->count() * 100, 0, ',', '.') }}</strong>
              </div>
              <div class="gopay-mini-stat">
                <small>Voucher Aktif</small>
                <strong id="penyuruh-voucher-count">2 Kupon</strong>
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
              <input type="hidden" name="idempotency_key" id="order-idempotency-key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
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
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap:8px; flex-wrap:wrap;">
                  <label class="form-label" for="order-pickup" style="margin-bottom:0;">Lokasi Jemput / Lokasi Tugas</label>
                  <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <button type="button" class="location-chip" style="background:#0471fc; color:#ffffff; border-color:#0471fc;" onclick="openPickupMapModal()">
                      Pilih di Peta
                    </button>
                    <button type="button" class="location-chip" onclick="useMyCurrentLocationForPickup()">
                      Lokasi Saya (GPS)
                    </button>
                  </div>
                </div>
                <input type="text" id="order-pickup" name="pickup_location" class="form-input" placeholder="Contoh: Kost Kejawan Putih Tambak No. 12" required>
                <input type="hidden" name="pickup_lat" id="order-pickup-lat">
                <input type="hidden" name="pickup_lng" id="order-pickup-lng">
                <div id="pickup-coord-badge" style="display:none; margin-top:6px; font-size:0.75rem; font-weight:600; color:#059669; background:#ecfdf5; border:1px solid #a7f3d0; padding:5px 10px; border-radius:10px; align-items:center; justify-content:space-between; gap:8px;">
                  <span id="pickup-coord-text">Titik peta tersimpan</span>
                  <button type="button" onclick="openPickupMapModal()" style="font-size:0.75rem; font-weight:700; color:var(--accent-blue); text-decoration:underline;">Ubah Titik</button>
                </div>
                <div class="location-chips">
                  <button type="button" class="location-chip" onclick="setPickup('Kampus PENS Sukolilo', -7.275847, 112.793755)">Kampus PENS</button>
                  <button type="button" class="location-chip" onclick="setPickup('Kost Kejawan Putih', -7.279520, 112.799850)">Kost Kejawan</button>
                  <button type="button" class="location-chip" onclick="setPickup('Galaxy Mall Surabaya', -7.275619, 112.780792)">Galaxy Mall</button>
                  <button type="button" class="location-chip" onclick="setPickup('Stasiun Gubeng', -7.265389, 112.752068)">Stasiun Gubeng</button>
                </div>
              </div>

              <div class="form-group">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap:8px; flex-wrap:wrap;">
                  <label class="form-label" for="order-dropoff" style="margin-bottom:0;">Lokasi Tujuan <span style="font-weight:400; color:var(--text-muted);">(opsional)</span></label>
                  <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <button type="button" class="location-chip" style="background:#0471fc; color:#ffffff; border-color:#0471fc;" onclick="openMapPickerModal('dropoff')">
                      Pilih di Peta
                    </button>
                    <button type="button" class="location-chip" onclick="useMyCurrentLocationFor('dropoff')">
                      Lokasi Saya (GPS)
                    </button>
                  </div>
                </div>
                <input type="text" id="order-dropoff" name="destination_location" class="form-input" placeholder="Contoh: Gedung D4 PENS / Jl. Arif Rahman Hakim">
                <input type="hidden" name="destination_lat" id="order-dropoff-lat">
                <input type="hidden" name="destination_lng" id="order-dropoff-lng">
                <div id="dropoff-coord-badge" style="display:none; margin-top:6px; font-size:0.75rem; font-weight:600; color:#059669; background:#ecfdf5; border:1px solid #a7f3d0; padding:5px 10px; border-radius:10px; align-items:center; justify-content:space-between; gap:8px;">
                  <span id="dropoff-coord-text">Titik tujuan tersimpan</span>
                  <button type="button" onclick="openMapPickerModal('dropoff')" style="font-size:0.75rem; font-weight:700; color:var(--accent-blue); text-decoration:underline;">Ubah Titik</button>
                </div>
                <div class="location-chips">
                  <button type="button" class="location-chip" onclick="setDropoff('Kampus PENS Gedung D4', -7.275847, 112.793755)">Kampus PENS</button>
                  <button type="button" class="location-chip" onclick="setDropoff('Kost Kejawan Putih', -7.279520, 112.799850)">Kost Kejawan</button>
                  <button type="button" class="location-chip" onclick="setDropoff('Galaxy Mall Surabaya', -7.275619, 112.780792)">Galaxy Mall</button>
                  <button type="button" class="location-chip" onclick="setDropoff('Stasiun Gubeng', -7.265389, 112.752068)">Stasiun Gubeng</button>
                </div>
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
                  <button
                    type="button"
                    class="btn btn-outline"
                    style="width:100%; margin-top:10px;"
                    data-title="{{ $aktif->title ?? $aktif->service_type }}"
                    data-pickup="{{ $aktif->pickup_location ?? 'Lokasi Jemput' }}"
                    data-dropoff="{{ $aktif->destination_location ?? '' }}"
                    data-lat="{{ $aktif->pickup_lat ?? '' }}"
                    data-lng="{{ $aktif->pickup_lng ?? '' }}"
                    data-dest-lat="{{ $aktif->destination_lat ?? '' }}"
                    data-dest-lng="{{ $aktif->destination_lng ?? '' }}"
                    onclick="openRouteMapModal(this)">Lihat Peta &amp; Rute Perjalanan</button>
                  @if($aktif->status === 'menunggu')
                    <form action="{{ Route::has('orders.cancel') ? route('orders.cancel', $aktif->slug ?? $aktif->id) : url('/orders/' . ($aktif->slug ?? $aktif->id) . '/cancel') }}" method="POST" style="margin-top: 12px;" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                      @csrf
                      <button type="submit" class="btn-logout" style="width:100%; justify-content:center; padding:9px 14px; font-size:0.8125rem;">Batalkan Pesanan</button>
                    </form>
                  @elseif($aktif->status === 'diambil')
                    <form action="{{ Route::has('orders.complete') ? route('orders.complete', $aktif->slug ?? $aktif->id) : url('/orders/' . ($aktif->slug ?? $aktif->id) . '/complete') }}" method="POST" style="margin-top: 12px;" onsubmit="return confirm('Konfirmasi bahwa mitra telah menyelesaikan tugas ini?');">
                      @csrf
                      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:9px 14px; font-size:0.8125rem; background:#059669; border-color:#059669;">✓ Konfirmasi Tugas Selesai</button>
                    </form>
                    <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-secondary); text-align:center;">
                      Tugas sedang dikerjakan mitra. Klik konfirmasi setelah tugas selesai.
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
            <button type="button" class="filter-tab" onclick="filterCustomerTasks(this, 'cancelled')">Dibatalkan</button>
          </div>

          <ul class="task-list-compact" id="penyuruh-task-list">
            @forelse($myOrders as $ord)
              <li class="task-item-compact" data-status="{{ $ord->status === 'selesai' ? 'done' : ($ord->status === 'dibatalkan' ? 'cancelled' : 'aktif') }}">
                <span class="task-status-badge {{ $ord->status === 'selesai' ? 'done' : ($ord->status === 'diambil' ? 'aktif' : ($ord->status === 'dibatalkan' ? 'cancelled' : 'pending')) }}" style="{{ $ord->status === 'dibatalkan' ? 'background:#fef2f2; color:#dc2626;' : '' }}">
                  {{ $ord->status === 'selesai' ? 'Selesai' : ($ord->status === 'diambil' ? 'Diambil' : ($ord->status === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu')) }}
                </span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->title ?? $ord->service_type }}</div>
                  <div class="task-compact-meta">{{ $ord->created_at->format('d M Y') }} · Mitra: {{ $ord->disuruh->name ?? 'Belum ada' }}</div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                  <span class="task-compact-price">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                  @if($ord->status === 'menunggu')
                    <form action="{{ Route::has('orders.cancel') ? route('orders.cancel', $ord->slug ?? $ord->id) : url('/orders/' . ($ord->slug ?? $ord->id) . '/cancel') }}" method="POST" style="margin:0;" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                      @csrf
                      <button type="submit" class="btn-logout" style="padding:3px 10px; font-size:0.7rem;">Batalkan</button>
                    </form>
                  @elseif($ord->status === 'diambil')
                    <form action="{{ Route::has('orders.complete') ? route('orders.complete', $ord->slug ?? $ord->id) : url('/orders/' . ($ord->slug ?? $ord->id) . '/complete') }}" method="POST" style="margin:0;" onsubmit="return confirm('Konfirmasi bahwa tugas ini sudah selesai dikerjakan?');">
                      @csrf
                      <button type="submit" class="btn btn-primary" style="padding:3px 10px; font-size:0.7rem; background:#059669; border-color:#059669;">Selesaikan</button>
                    </form>
                  @endif
                </div>
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
            <div class="search-box" style="min-width:210px;">
              <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="penyuruh-history-search" placeholder="Cari riwayat pesanan.." onkeyup="applyListFilter('penyuruh-history-list')">
            </div>
          </div>

          <div class="filter-tabs" data-filter-target="penyuruh-history-list">
            <button type="button" class="filter-tab active" onclick="setListStatusFilter(this, 'penyuruh-history-list', 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'penyuruh-history-list', 'credit')">Masuk (Top Up / Refund)</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'penyuruh-history-list', 'debit')">Keluar (Order)</button>
          </div>

          <ul class="transaction-list" id="penyuruh-history-list" data-active-status="all">
            @forelse($myWalletTransactions->where('status', 'SUCCESS') as $trx)
              @php $isCredit = in_array($trx->type, ['TOPUP', 'REFUND', 'ORDER_EARNING']); @endphp
              <li class="transaction-item" data-status="{{ $isCredit ? 'credit' : 'debit' }}" data-search="{{ strtolower(($trx->description ?? '') . ' ' . $trx->invoice_number . ' ' . $trx->payment_method) }}">
                <div class="transaction-icon {{ $isCredit ? 'receive' : 'send' }}" aria-hidden="true">{{ $isCredit ? '↓' : '↑' }}</div>
                <div class="transaction-info">
                  <div class="transaction-name">{{ $trx->description ?? $trx->type }}</div>
                  <div class="transaction-date">
                    {{ ($trx->paid_at ?? $trx->created_at)->format('d M Y, H:i') }} WIB ·
                    <strong style="color:{{ $isCredit ? '#059669' : '#0471fc' }};">{{ $trx->payment_method }}</strong>
                  </div>
                </div>
                <span class="transaction-amount {{ $isCredit ? 'credit' : 'debit' }}" style="color:{{ $isCredit ? '#059669' : 'var(--accent-red)' }};">
                  {{ $isCredit ? '+' : '-' }}Rp {{ number_format($trx->amount ?? 0, 0, ',', '.') }}
                </span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada riwayat mutasi dompet.</li>
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
              <span class="wallet-balance-amount" data-value="Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</span>
              <button type="button" class="eye-toggle" onclick="toggleBalance()" aria-label="Sembunyikan pendapatan">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <div class="wallet-actions">
              <button type="button" class="btn btn-primary" onclick="openWithdrawModal()">Tarik Pendapatan</button>
              <button type="button" class="btn btn-outline" onclick="switchDashboardTab('riwayat')">Lihat Slip Komisi</button>
            </div>
          </div>

          <div class="priority-card">
            <div class="priority-label">PERFORMA MITRA</div>
            <div style="margin-bottom:4px; font-size:0.8125rem; color:rgba(255,255,255,0.72);">Total Pendapatan Selesai</div>
            <div class="priority-balance">
              Rp {{ number_format($mitraCompleted->sum('price'), 0, ',', '.') }}
              <span class="priority-change">{{ $mitraCompleted->count() }} Tugas Selesai</span>
            </div>
            <div class="priority-stats">
              <div class="priority-stat">
                <div class="priority-stat-label">Tugas Diambil</div>
                <div class="priority-stat-value">{{ $mitraAllTasks->count() }} Tugas</div>
              </div>
              <div class="priority-stat">
                <div class="priority-stat-label">Tingkat Selesai</div>
                <div class="priority-stat-value">{{ $mitraAllTasks->count() > 0 ? round(($mitraCompleted->count() / $mitraAllTasks->count()) * 100) . '%' : '0%' }}</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Order Masuk + Tugas Berjalan -->
        <div class="grid-2">
          <!-- Order Masuk -->
          <div class="card">
            <div class="card-header" style="flex-wrap:wrap; gap:8px;">
              <div>
                <h3 class="card-title">Orderan Masuk di Sekitarmu</h3>
                <div id="mitra-nearest-status-badge" style="font-size:0.75rem; color:#059669; font-weight:700; margin-top:2px; display:flex; align-items:center; gap:6px;">
                  <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span>
                  <span>📍 Mendeteksi orderan terdekat...</span>
                </div>
              </div>
              <button type="button" class="location-chip" onclick="detectMitraLocationAndSort(true)" style="background:#ffffff; border-color:#0471fc; color:#0471fc; font-weight:600; font-size:0.75rem;">
                🔄 Perbarui GPS &amp; Jarak
              </button>
            </div>

            <div class="incoming-orders-container" id="mitra-incoming-orders-list">
              @forelse($openOrders as $ord)
                <div class="incoming-order-card"
                     data-order-id="{{ $ord->id }}"
                     data-lat="{{ $ord->pickup_lat ?? '' }}"
                     data-lng="{{ $ord->pickup_lng ?? '' }}"
                     data-created="{{ $ord->created_at ? $ord->created_at->timestamp : 0 }}">
                  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:6px;">
                    <div>
                      <strong style="font-size:0.9375rem;">{{ $ord->title ?? $ord->service_type }}</strong>
                      <span class="order-dist-badge" style="display:none; margin-left:6px; font-size:0.6875rem; font-weight:700; color:#0471fc; background:#eff6ff; border:1px solid #bfdbfe; padding:2px 8px; border-radius:9999px;"></span>
                    </div>
                    <span style="font-weight:800; color:var(--accent-green); white-space:nowrap;">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                  </div>
                  <div style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:10px;">
                    {{ $ord->pickup_location ?? 'Lokasi Tugas' }} → {{ $ord->destination_location ?? 'Tujuan' }}
                    @if($ord->pickup_lat && $ord->pickup_lng)
                      <span style="display:inline-block; margin-left:4px; font-size:0.6875rem; font-weight:700; color:#059669; background:#ecfdf5; padding:1px 7px; border-radius:9999px;">Pin Peta Ada</span>
                    @endif
                  </div>
                  <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button
                      type="button"
                      class="btn btn-outline"
                      style="flex:1; min-width:135px;"
                      data-title="{{ $ord->title ?? $ord->service_type }}"
                      data-pickup="{{ $ord->pickup_location ?? 'Lokasi Tugas' }}"
                      data-dropoff="{{ $ord->destination_location ?? '' }}"
                      data-lat="{{ $ord->pickup_lat ?? '' }}"
                      data-lng="{{ $ord->pickup_lng ?? '' }}"
                      data-dest-lat="{{ $ord->destination_lat ?? '' }}"
                      data-dest-lng="{{ $ord->destination_lng ?? '' }}"
                      onclick="openRouteMapModal(this)">Lihat Lokasi &amp; Rute</button>
                    <form action="{{ Route::has('orders.take') ? route('orders.take', $ord->slug ?? $ord->id) : url('/orders/' . ($ord->slug ?? $ord->id) . '/take') }}" method="POST" style="flex:1; min-width:135px; margin:0;">
                      @csrf
                      <button type="submit" class="btn btn-primary" style="width:100%;">Ambil Order Ini</button>
                    </form>
                  </div>
                </div>
              @empty
                <div style="text-align:center; padding:28px; color:var(--text-muted); font-size:0.875rem;">
                  Tidak ada orderan masuk saat ini.<br>Tetap online untuk menerima tugas!
                </div>
              @endforelse
            </div>
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
                    <div class="task-compact-meta">Pemesan: {{ $ord->penyuruh->name ?? '-' }} · {{ $ord->pickup_location ?? 'Lokasi Tugas' }}</div>
                  </div>
                  <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                    <span class="task-compact-price">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                    <div style="display:flex; gap:6px; align-items:center;">
                      <button
                        type="button"
                        class="btn btn-outline"
                        style="padding:4px 10px; font-size:0.75rem;"
                        data-title="{{ $ord->title ?? $ord->service_type }}"
                        data-pickup="{{ $ord->pickup_location ?? 'Lokasi Tugas' }}"
                        data-dropoff="{{ $ord->destination_location ?? '' }}"
                        data-lat="{{ $ord->pickup_lat ?? '' }}"
                        data-lng="{{ $ord->pickup_lng ?? '' }}"
                        data-dest-lat="{{ $ord->destination_lat ?? '' }}"
                        data-dest-lng="{{ $ord->destination_lng ?? '' }}"
                        onclick="openRouteMapModal(this)">Rute</button>
                      <span style="font-size:0.6875rem; color:var(--text-muted); background:var(--bg-gray); border:1px solid var(--border); padding:3px 8px; border-radius:6px; white-space:nowrap;">Menunggu Pemesan Selesai</span>
                    </div>
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
            <div class="search-box" style="min-width:210px;">
              <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="mitra-task-search" placeholder="Cari tugas atau pemesan.." onkeyup="applyListFilter('mitra-task-list')">
            </div>
          </div>

          <div class="filter-tabs" data-filter-target="mitra-task-list">
            <button type="button" class="filter-tab active" onclick="setListStatusFilter(this, 'mitra-task-list', 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'mitra-task-list', 'diambil')">Berjalan</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'mitra-task-list', 'selesai')">Selesai</button>
          </div>

          <ul class="task-list-compact" id="mitra-task-list" data-active-status="all">
            @forelse($mitraAllTasks as $ord)
              <li class="task-item-compact" data-status="{{ $ord->status }}" data-search="{{ strtolower(($ord->title ?? $ord->service_type) . ' ' . ($ord->penyuruh->name ?? '')) }}">
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
            <h3 class="card-title">Riwayat Komisi &amp; Pencairan Mitra</h3>
            <div class="search-box" style="min-width:210px;">
              <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="mitra-history-search" placeholder="Cari komisi tugas.." onkeyup="applyListFilter('mitra-history-list')">
            </div>
          </div>

          <div class="filter-tabs" data-filter-target="mitra-history-list">
            <button type="button" class="filter-tab active" onclick="setListStatusFilter(this, 'mitra-history-list', 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'mitra-history-list', 'selesai')">Selesai</button>
          </div>

          <ul class="transaction-list" id="mitra-history-list" data-active-status="all">
            @forelse($mitraCompleted as $ord)
              <li class="transaction-item" data-status="{{ $ord->status }}" data-search="{{ strtolower(($ord->title ?? $ord->service_type) . ' ' . ($ord->penyuruh->name ?? '')) }}">
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
                  <th>Detail Data</th>
                  <th>Kontrol Role &amp; Akun</th>
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
                    <td>
                      @if($u->is_suspended)
                        <span class="status-dot" style="background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; font-weight:700;">Ditangguhkan</span>
                      @else
                        <span class="status-dot">Aktif</span>
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn btn-outline" style="padding:5px 12px; font-size:0.75rem;" onclick="openAdminUserDetail({{ $u->id }})">Lihat Detail</button>
                    </td>
                    <td>
                      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <form action="{{ Route::has('users.update-role') ? route('users.update-role', $u->slug ?? $u->id) : url('/dashboard/users/' . ($u->slug ?? $u->id) . '/role') }}" method="POST" style="margin:0;">
                          @csrf
                          <select name="role" onchange="this.form.submit()" class="role-select" aria-label="Ubah role {{ $u->name }}">
                            <option value="penyuruh" {{ $u->role === 'penyuruh' ? 'selected' : '' }}>Penyuruh</option>
                            <option value="disuruh" {{ $u->role === 'disuruh' ? 'selected' : '' }}>Yang Disuruh</option>
                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                          </select>
                        </form>
                        @if($u->id !== $user->id)
                          <form action="{{ Route::has('users.toggle-suspend') ? route('users.toggle-suspend', $u->slug ?? $u->id) : url('/dashboard/users/' . ($u->slug ?? $u->id) . '/suspend') }}" method="POST" style="margin:0;" onsubmit="return confirm('{{ $u->is_suspended ? 'Aktifkan kembali akun ' . $u->name . '?' : 'Yakin ingin melakukan suspend pada akun ' . $u->name . '?' }}');">
                            @csrf
                            <button type="submit" class="{{ $u->is_suspended ? 'btn btn-primary' : 'btn-logout' }}" style="padding:4px 10px; font-size:0.75rem; {{ $u->is_suspended ? 'background:#059669; border-color:#059669;' : '' }}">
                              {{ $u->is_suspended ? 'Aktifkan' : 'Suspend' }}
                            </button>
                          </form>
                        @endif
                      </div>
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
            <div class="search-box" style="min-width:220px;">
              <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="admin-orders-search" placeholder="Cari pesanan, penyuruh, mitra.." onkeyup="applyListFilter('admin-orders-list')">
            </div>
          </div>

          <div class="filter-tabs" data-filter-target="admin-orders-list" style="display:flex; flex-wrap:wrap; align-items:center; gap:6px;">
            <button type="button" class="filter-tab active" onclick="setListStatusFilter(this, 'admin-orders-list', 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'admin-orders-list', 'menunggu')">Menunggu</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'admin-orders-list', 'diambil')">Diambil</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'admin-orders-list', 'selesai')">Selesai</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'admin-orders-list', 'dibatalkan')">Dibatalkan</button>
            <button type="button" class="filter-tab" id="btn-admin-sort-nearest" onclick="toggleAdminOrderSort(this)" style="margin-left:auto; font-weight:700; color:var(--accent-blue); background:#eff6ff; border-color:#bfdbfe;">
              📍 Urutkan Terdekat
            </button>
          </div>

          <ul class="task-list-compact" id="admin-orders-list" data-active-status="all">
            @forelse($allOrders as $ord)
              <li class="task-item-compact"
                  data-lat="{{ $ord->pickup_lat ?? '' }}"
                  data-lng="{{ $ord->pickup_lng ?? '' }}"
                  data-created="{{ $ord->created_at ? $ord->created_at->timestamp : 0 }}"
                  data-status="{{ $ord->status }}"
                  data-search="{{ strtolower('#ps-' . $ord->id . ' ' . ($ord->title ?? $ord->service_type) . ' ' . ($ord->penyuruh->name ?? '') . ' ' . ($ord->disuruh->name ?? '')) }}">
                <span class="task-status-badge {{ $ord->status === 'selesai' ? 'done' : ($ord->status === 'diambil' ? 'aktif' : ($ord->status === 'dibatalkan' ? 'cancelled' : 'pending')) }}" style="{{ $ord->status === 'dibatalkan' ? 'background:#fef2f2; color:#dc2626;' : '' }}">
                  {{ $ord->status === 'selesai' ? 'Selesai' : ($ord->status === 'diambil' ? 'Diambil' : ($ord->status === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu')) }}
                </span>
                <div class="task-compact-info">
                  <div class="task-compact-title">
                    {{ $ord->title ?? $ord->service_type }}
                    <span class="admin-dist-badge" style="display:none; font-size:0.6875rem; font-weight:700; color:#0471fc; background:#eff6ff; border:1px solid #bfdbfe; padding:2px 7px; border-radius:9999px; margin-left:6px;"></span>
                  </div>
                  <div class="task-compact-meta">Penyuruh: {{ $ord->penyuruh->name ?? '-' }} · Mitra: {{ $ord->disuruh->name ?? 'Belum ada' }} · {{ $ord->pickup_location ?? '-' }}</div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                  <span class="task-compact-price">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                  @if($ord->status === 'diambil')
                    <div style="display:flex; gap:4px; align-items:center;">
                      <form action="{{ Route::has('orders.complete') ? route('orders.complete', $ord->slug ?? $ord->id) : url('/orders/' . ($ord->slug ?? $ord->id) . '/complete') }}" method="POST" style="margin:0;" onsubmit="return confirm('Admin: tandai pesanan ini telah selesai?');">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="padding:3px 8px; font-size:0.7rem; background:#059669; border-color:#059669;">Selesaikan</button>
                      </form>
                      <form action="{{ Route::has('orders.cancel') ? route('orders.cancel', $ord->slug ?? $ord->id) : url('/orders/' . ($ord->slug ?? $ord->id) . '/cancel') }}" method="POST" style="margin:0;" onsubmit="return confirm('Admin: yakin ingin membatalkan pesanan ini?');">
                        @csrf
                        <button type="submit" class="btn-logout" style="padding:3px 8px; font-size:0.7rem;">Batalkan</button>
                      </form>
                    </div>
                  @elseif($ord->status === 'menunggu')
                    <form action="{{ Route::has('orders.cancel') ? route('orders.cancel', $ord->slug ?? $ord->id) : url('/orders/' . ($ord->slug ?? $ord->id) . '/cancel') }}" method="POST" style="margin:0;" onsubmit="return confirm('Admin: yakin ingin membatalkan pesanan ini?');">
                      @csrf
                      <button type="submit" class="btn-logout" style="padding:3px 10px; font-size:0.7rem;">Batalkan (Admin)</button>
                    </form>
                  @endif
                </div>
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
            <div class="search-box" style="min-width:220px;">
              <span class="search-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="admin-history-search" placeholder="Cari #PS-ID, judul, atau nama.." onkeyup="applyListFilter('admin-history-list')">
            </div>
          </div>

          <div class="filter-tabs" data-filter-target="admin-history-list">
            <button type="button" class="filter-tab active" onclick="setListStatusFilter(this, 'admin-history-list', 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'admin-history-list', 'selesai')">Selesai</button>
            <button type="button" class="filter-tab" onclick="setListStatusFilter(this, 'admin-history-list', 'dibatalkan')">Dibatalkan</button>
          </div>

          <ul class="transaction-list" id="admin-history-list" data-active-status="all">
            @forelse($allOrders->whereIn('status', ['selesai', 'dibatalkan']) as $ord)
              <li class="transaction-item" data-status="{{ $ord->status }}" data-search="{{ strtolower('#ps-' . $ord->id . ' ' . ($ord->title ?? $ord->service_type) . ' ' . ($ord->penyuruh->name ?? '') . ' ' . ($ord->disuruh->name ?? '')) }}">
                <div class="transaction-icon {{ $ord->status === 'dibatalkan' ? 'send' : 'receive' }}" aria-hidden="true">{{ $ord->status === 'dibatalkan' ? '✕' : '✓' }}</div>
                <div class="transaction-info">
                  <div class="transaction-name">Order #PS-{{ $ord->id }} · {{ $ord->title ?? $ord->service_type }}</div>
                  <div class="transaction-date">
                    {{ $ord->updated_at->format('d M Y, H:i') }} WIB ·
                    Penyuruh: {{ $ord->penyuruh->name ?? '-' }} ·
                    Mitra: {{ $ord->disuruh->name ?? '-' }} ·
                    <strong style="color:{{ $ord->status === 'dibatalkan' ? '#dc2626' : '#059669' }};">{{ $ord->status === 'dibatalkan' ? 'Dibatalkan' : 'Selesai' }}</strong>
                  </div>
                </div>
                <span class="transaction-amount {{ $ord->status === 'dibatalkan' ? '' : 'credit' }}" style="{{ $ord->status === 'dibatalkan' ? 'color:var(--text-muted); text-decoration:line-through;' : '' }}">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:28px; text-align:center; color:var(--text-muted);">Belum ada riwayat transaksi.</li>
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

  <!-- ═══════════════════════════════════════════ -->
  <!-- MODAL: PILIH LOKASI JEMPUT DI PETA          -->
  <!-- ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="pickup-map-modal" role="dialog" aria-modal="true" aria-labelledby="pickup-map-title">
    <div class="modal-box" style="max-width:640px;">
      <div class="modal-header">
        <h3 id="pickup-map-title" style="font-size:1.0625rem; font-weight:700;">Tentukan Titik Jemput di Peta</h3>
        <button type="button" class="modal-close" onclick="closePickupMapModal()" aria-label="Tutup">✕</button>
      </div>

      <div style="display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
        <input type="text" id="pickup-map-search" class="form-input" style="flex:1; min-width:180px;" placeholder="Cari alamat atau tempat (mis. Kampus PENS, Gubeng)..." onkeydown="if(event.key==='Enter'){event.preventDefault(); searchPickupAddress();}">
        <button type="button" class="btn btn-outline" onclick="searchPickupAddress()">Cari</button>
        <button type="button" class="btn btn-outline" onclick="centerPickupMapToGPS()">GPS Saya</button>
      </div>

      <div id="pickup-leaflet-map" style="width:100%; height:300px; border-radius:14px; border:1px solid var(--border); overflow:hidden; background:#e2e8f0; margin-bottom:12px;"></div>

      <div style="background:var(--bg-gray); border:1px solid var(--border); border-radius:12px; padding:10px 14px; margin-bottom:14px;">
        <div style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Alamat / Titik Terpilih (klik atau geser pin di peta):</div>
        <div id="pickup-map-selected-address" style="font-size:0.875rem; font-weight:600; color:var(--text-primary); margin-top:2px;">-</div>
        <div id="pickup-map-selected-coords" style="font-size:0.75rem; color:var(--accent-blue); font-family:monospace; margin-top:2px;">-</div>
      </div>

      <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-outline" style="flex:1;" onclick="closePickupMapModal()">Batal</button>
        <button type="button" class="btn btn-primary" style="flex:2;" onclick="confirmPickupMapSelection()">Gunakan Titik Ini</button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════ -->
  <!-- MODAL: LIHAT LOKASI & RUTE ORDER (MITRA)    -->
  <!-- ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="route-map-modal" role="dialog" aria-modal="true" aria-labelledby="route-map-title">
    <div class="modal-box" style="max-width:680px;">
      <div class="modal-header">
        <div>
          <h3 id="route-map-title" style="font-size:1.0625rem; font-weight:700;">Lokasi Order &amp; Rute</h3>
          <div id="route-map-subtitle" style="font-size:0.75rem; color:var(--text-secondary);">Memuat lokasi order...</div>
        </div>
        <button type="button" class="modal-close" onclick="closeRouteMapModal()" aria-label="Tutup">✕</button>
      </div>

      <div id="route-stats-bar" style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:10px 14px; margin-bottom:10px;">
        <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary);" id="route-status-text">Mencari rute dari posisimu ke titik jemput...</div>
        <button type="button" class="location-chip" style="background:#ffffff;" onclick="refreshMitraGPSRoute()">Perbarui GPS Saya</button>
      </div>

      <div id="route-leaflet-map" style="width:100%; height:320px; border-radius:14px; border:1px solid var(--border); overflow:hidden; background:#e2e8f0; margin-bottom:12px;"></div>

      <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:12px;">
        Tips: Jika GPS perangkat tidak aktif, klik di mana saja pada peta untuk menentukan titik awal kamu dan lihat rute ke lokasi order.
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a id="route-gmaps-link" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="flex:2; min-width:180px; text-align:center;">Buka Rute di Google Maps</a>
        <button type="button" class="btn btn-outline" style="flex:1; min-width:100px;" onclick="closeRouteMapModal()">Tutup</button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════ -->
  <!-- MODAL: DETAIL PENGGUNA & HISTORY (ADMIN)    -->
  <!-- ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="admin-user-detail-modal" role="dialog" aria-modal="true" aria-labelledby="admin-detail-name">
    <div class="modal-box" style="max-width:680px; max-height:90vh; overflow-y:auto;">
      <div class="modal-header">
        <div>
          <div style="font-size:0.6875rem; font-weight:700; color:var(--accent-blue); text-transform:uppercase; letter-spacing:0.04em;">Detail Akun &amp; Riwayat Transaksi</div>
          <h3 id="admin-detail-name" style="font-size:1.125rem; font-weight:800; margin-top:2px;">-</h3>
          <div id="admin-detail-meta" style="font-size:0.75rem; color:var(--text-secondary);">-</div>
        </div>
        <button type="button" class="modal-close" onclick="closeAdminUserDetail()" aria-label="Tutup">✕</button>
      </div>

      <!-- Ringkasan Saldo & Aktivitas -->
      <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-bottom:16px;" id="admin-detail-stats-grid">
        <div style="background:linear-gradient(135deg, #0471fc 0%, #0257c7 100%); color:#ffffff; border-radius:14px; padding:14px;">
          <div style="font-size:0.6875rem; opacity:0.85; font-weight:600;" id="admin-detail-balance-label">Estimasi Saldo</div>
          <div style="font-size:1.25rem; font-weight:800; margin-top:2px;" id="admin-detail-balance">Rp 0</div>
        </div>
        <div style="background:var(--bg-gray); border:1px solid var(--border); border-radius:14px; padding:14px;">
          <div style="font-size:0.6875rem; color:var(--text-secondary); font-weight:600;" id="admin-detail-turnover-label">Total Transaksi Selesai</div>
          <div style="font-size:1.25rem; font-weight:800; color:var(--accent-green); margin-top:2px;" id="admin-detail-turnover">Rp 0</div>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; margin-bottom:16px;">
        <div style="background:var(--bg-gray); border-radius:10px; padding:10px; text-align:center;">
          <div style="font-size:0.6875rem; color:var(--text-muted); font-weight:600;">Aktif</div>
          <div style="font-size:1rem; font-weight:800; color:var(--accent-blue);" id="admin-detail-active-count">0</div>
        </div>
        <div style="background:var(--bg-gray); border-radius:10px; padding:10px; text-align:center;">
          <div style="font-size:0.6875rem; color:var(--text-muted); font-weight:600;">Selesai</div>
          <div style="font-size:1rem; font-weight:800; color:var(--accent-green);" id="admin-detail-completed-count">0</div>
        </div>
        <div style="background:var(--bg-gray); border-radius:10px; padding:10px; text-align:center;">
          <div style="font-size:0.6875rem; color:var(--text-muted); font-weight:600;">Dibatalkan</div>
          <div style="font-size:1rem; font-weight:800; color:var(--accent-red);" id="admin-detail-cancelled-count">0</div>
        </div>
      </div>

      <!-- Filter Riwayat User di Modal Admin -->
      <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
        <div style="font-size:0.875rem; font-weight:700;">Riwayat Order &amp; Aktivitas</div>
        <div class="filter-tabs" style="margin-bottom:0;" id="admin-modal-filter-tabs">
          <button type="button" class="filter-tab active" style="padding:4px 10px; font-size:0.75rem;" onclick="filterAdminModalHistory(this, 'all')">Semua</button>
          <button type="button" class="filter-tab" style="padding:4px 10px; font-size:0.75rem;" onclick="filterAdminModalHistory(this, 'selesai')">Selesai</button>
          <button type="button" class="filter-tab" style="padding:4px 10px; font-size:0.75rem;" onclick="filterAdminModalHistory(this, 'aktif')">Aktif</button>
          <button type="button" class="filter-tab" style="padding:4px 10px; font-size:0.75rem;" onclick="filterAdminModalHistory(this, 'dibatalkan')">Batal</button>
        </div>
      </div>

      <ul class="task-list-compact" id="admin-detail-history-list" style="max-height:260px; overflow-y:auto; border:1px solid var(--border-light); border-radius:12px; padding:0 12px;">
      </ul>

      <div style="margin-top:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div id="admin-detail-suspend-container"></div>
        <button type="button" class="btn btn-outline" onclick="closeAdminUserDetail()">Tutup</button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════ -->
  <!-- MODAL: ISI SALDO QRIS (DOKU PAYMENT GATEWAY) -->
  <!-- ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="qris-topup-modal" role="dialog" aria-modal="true" aria-labelledby="qris-topup-title">
    <div class="modal-box" style="max-width:520px;">
      <div class="modal-header">
        <div>
          <div style="font-size:0.6875rem; font-weight:700; color:var(--accent-blue); text-transform:uppercase; letter-spacing:0.04em;">DOKU Payment Gateway</div>
          <h3 id="qris-topup-title" style="font-size:1.125rem; font-weight:800; margin-top:2px;">Isi Saldo via QRIS</h3>
        </div>
        <button type="button" class="modal-close" onclick="closeTopUpModal()" aria-label="Tutup">✕</button>
      </div>

      <div id="topup-form-view">
        <p style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:14px;">
          Pilih atau tentukan nominal pengisian saldo dompetmu. Pembayaran mendukung scan QRIS dari semua aplikasi m-banking dan e-wallet.
        </p>

        <!-- Pilihan Nominal Cepat -->
        <div style="margin-bottom:14px;">
          <label class="form-label" style="margin-bottom:6px;">Pilihan Nominal Cepat</label>
          <div class="location-chips" id="topup-quick-chips" style="gap:8px;">
            <button type="button" class="location-chip" onclick="setTopUpAmount(20000, this)">Rp 20.000</button>
            <button type="button" class="location-chip" onclick="setTopUpAmount(50000, this)">Rp 50.000</button>
            <button type="button" class="location-chip active" style="background:#0471fc; color:#ffffff; border-color:#0471fc;" onclick="setTopUpAmount(100000, this)">Rp 100.000</button>
            <button type="button" class="location-chip" onclick="setTopUpAmount(250000, this)">Rp 250.000</button>
            <button type="button" class="location-chip" onclick="setTopUpAmount(500000, this)">Rp 500.000</button>
          </div>
        </div>

        <!-- Input Nominal -->
        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label" for="topup-amount-input">Nominal Isi Saldo (Rp)</label>
          <input type="number" id="topup-amount-input" class="form-input" style="font-size:1.125rem; font-weight:700; color:var(--accent-blue);" min="10000" step="5000" value="100000" placeholder="Minimal Rp 10.000">
          <small style="color:var(--text-muted); font-size:0.75rem;">Minimal pengisian: Rp 10.000</small>
        </div>

        <!-- Info Metode Pembayaran -->
        <div style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px; margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <span style="font-size:0.75rem; font-weight:700; color:var(--text-primary);">Metode Pembayaran:</span>
            <span style="font-size:0.75rem; font-weight:800; color:#dc2626; background:#fee2e2; padding:2px 8px; border-radius:6px;">QRIS Real-Time</span>
          </div>
          <div style="font-size:0.75rem; color:var(--text-secondary); line-height:1.4;">
            Mendukung <strong>BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay, LinkAja</strong>, dan seluruh aplikasi yang memiliki fitur scan QRIS.
          </div>
        </div>

        <button type="button" class="btn btn-primary" id="btn-proceed-qris" style="width:100%; justify-content:center; padding:11px 16px; font-size:0.9375rem;" onclick="processDokuQrisTopUp()">
          Bayar via QRIS Sekarang
        </button>
      </div>

      <!-- Tampilan Pembayaran QRIS Aktif -->
      <div id="topup-payment-view" style="display:none; text-align:center; padding:10px 0;">
        <div id="topup-payment-loading" style="padding:20px 0;">
          <div style="width:40px; height:40px; border:3px solid #bfdbfe; border-top-color:#0471fc; border-radius:50%; margin:0 auto 12px; animation:spin 0.8s linear infinite;"></div>
          <div style="font-weight:700; font-size:0.9375rem;">Menghubungkan ke Gateway DOKU...</div>
          <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Membuat tagihan QRIS resmi</div>
        </div>

        <div id="topup-payment-ready" style="display:none;">
          <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:12px; padding:12px; margin-bottom:14px; text-align:left;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="font-size:0.75rem; color:#059669; font-weight:700;">Nomor Tagihan:</span>
              <span id="topup-inv-display" style="font-family:monospace; font-weight:700; font-size:0.8125rem;">-</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
              <span style="font-size:0.75rem; color:#059669; font-weight:700;">Total Pembayaran:</span>
              <span id="topup-amount-display" style="font-size:1.125rem; font-weight:800; color:#059669;">Rp 0</span>
            </div>
          </div>

          <!-- Kotak Kode QRIS -->
          <div style="background:#ffffff; border:2px solid #e2e8f0; border-radius:16px; padding:16px; margin-bottom:14px; box-shadow:0 4px 12px rgba(0,0,0,0.04);">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:10px; margin-bottom:12px;">
              <span style="font-weight:900; font-size:1rem; letter-spacing:0.06em; color:#dc2626;">QRIS</span>
              <span style="font-size:0.6875rem; font-weight:700; color:#64748b; background:#f1f5f9; padding:2px 8px; border-radius:6px;">PENSURUH · DOKU</span>
            </div>
            <div id="qris-qr-container" style="width:196px; height:196px; margin:0 auto; padding:8px; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; display:flex; align-items:center; justify-content:center;"></div>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:10px; font-weight:500;">
              Scan kode QRIS di atas menggunakan <strong>BCA, Mandiri, BRI, GoPay, OVO, DANA, atau ShopeePay</strong>
            </div>
          </div>

          <a href="#" id="topup-doku-link" target="_blank" class="btn btn-primary" style="width:100%; justify-content:center; padding:11px 18px; font-size:0.875rem; text-decoration:none; margin-bottom:10px; display:none;">
            🚀 Buka Halaman Checkout DOKU
          </a>

          <div style="display:flex; align-items:center; justify-content:center; gap:6px; font-size:0.75rem; color:#059669; font-weight:600; margin-top:8px;" id="topup-polling-status">
            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span>
            <span>Menunggu pembayaran QRIS...</span>
          </div>

          <div style="margin-top:14px; padding-top:12px; border-top:1px dashed var(--border);">
            <div style="font-size:0.6875rem; color:var(--text-muted); margin-bottom:6px;">Simulasi Pembayaran (Testing Localhost):</div>
            <a href="#" id="topup-simulate-btn" class="btn btn-outline" style="width:100%; justify-content:center; font-size:0.75rem; padding:7px 12px; color:#0471fc;">
              ⚡ Simulasi Bayar QRIS Berhasil
            </a>
          </div>
        </div>

        <div id="topup-payment-success" style="display:none; padding:16px 0;">
          <div style="width:52px; height:52px; background:#ecfdf5; border:2px solid #10b981; border-radius:50%; color:#059669; font-size:1.75rem; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">✓</div>
          <h4 style="font-size:1.125rem; font-weight:800; color:#059669; margin-bottom:4px;">Isi Saldo Berhasil!</h4>
          <p id="topup-success-msg" style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:16px;">
            Saldo dompetmu telah bertambah secara otomatis.
          </p>
          <button type="button" class="btn btn-primary" onclick="window.location.reload()" style="width:100%; justify-content:center;">
            Selesai
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════ -->
  <!-- MODAL: TARIK PENDAPATAN MITRA               -->
  <!-- ═══════════════════════════════════════════ -->
  <div class="modal-overlay" id="withdraw-modal" role="dialog" aria-modal="true" aria-labelledby="withdraw-title">
    <div class="modal-box" style="max-width:460px;">
      <div class="modal-header">
        <div>
          <div style="font-size:0.6875rem; font-weight:700; color:var(--accent-green); text-transform:uppercase; letter-spacing:0.04em;">Pencairan Dana Mitra</div>
          <h3 id="withdraw-title" style="font-size:1.125rem; font-weight:800; margin-top:2px;">Tarik Pendapatan</h3>
        </div>
        <button type="button" class="modal-close" onclick="closeWithdrawModal()" aria-label="Tutup">✕</button>
      </div>

      <form action="{{ Route::has('wallet.withdraw') ? route('wallet.withdraw') : url('/wallet/withdraw') }}" method="POST">
        @csrf
        <div style="background:#f8fafc; border:1px solid var(--border); border-radius:12px; padding:12px; margin-bottom:14px;">
          <div style="font-size:0.75rem; color:var(--text-muted);">Saldo Tersedia untuk Ditarik:</div>
          <div style="font-size:1.25rem; font-weight:800; color:var(--accent-blue); margin-top:2px;">Rp {{ number_format($user->balance ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="withdraw-amount">Nominal Penarikan (Rp)</label>
          <input type="number" id="withdraw-amount" name="amount" class="form-input" min="10000" max="{{ (int) ($user->balance ?? 0) }}" step="5000" placeholder="Minimal Rp 10.000" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="withdraw-bank">Bank / E-Wallet Tujuan</label>
          <select id="withdraw-bank" name="bank_name" class="form-input" required>
            <option value="BCA">Bank BCA</option>
            <option value="Mandiri">Bank Mandiri</option>
            <option value="BRI">Bank BRI</option>
            <option value="BNI">Bank BNI</option>
            <option value="GoPay">GoPay</option>
            <option value="OVO">OVO</option>
            <option value="DANA">DANA</option>
            <option value="ShopeePay">ShopeePay</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="withdraw-account">Nomor Rekening / No. HP E-Wallet</label>
          <input type="text" id="withdraw-account" name="account_number" class="form-input" placeholder="Contoh: 1234567890" required>
        </div>

        <div style="display:flex; gap:10px; margin-top:16px;">
          <button type="button" class="btn btn-outline" style="flex:1;" onclick="closeWithdrawModal()">Batal</button>
          <button type="submit" class="btn btn-primary" style="flex:2; justify-content:center;">Proses Penarikan</button>
        </div>
      </form>
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

    let customerBalance = {{ (float) ($user->balance ?? 0) }};
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

    // Unified filter + search untuk tab Pesanan & Riwayat
    const LIST_SEARCH_INPUTS = {
      'penyuruh-history-list': 'penyuruh-history-search',
      'mitra-task-list': 'mitra-task-search',
      'mitra-history-list': 'mitra-history-search',
      'admin-orders-list': 'admin-orders-search',
      'admin-history-list': 'admin-history-search'
    };

    function setListStatusFilter(btn, listId, status) {
      btn.closest('.filter-tabs').querySelectorAll('.filter-tab').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      const list = document.getElementById(listId);
      if (list) list.dataset.activeStatus = status;
      applyListFilter(listId);
    }

    function applyListFilter(listId) {
      const list = document.getElementById(listId);
      if (!list) return;
      const activeStatus = list.dataset.activeStatus || 'all';
      const searchId = LIST_SEARCH_INPUTS[listId];
      const q = searchId ? (document.getElementById(searchId)?.value.toLowerCase().trim() || '') : '';

      list.querySelectorAll('li[data-status]').forEach(function(li) {
        const statusMatch = activeStatus === 'all' || li.dataset.status === activeStatus;
        const searchText = li.dataset.search || li.textContent.toLowerCase();
        const searchMatch = !q || searchText.includes(q);
        li.style.display = (statusMatch && searchMatch) ? '' : 'none';
      });
    }

    // Admin User Detail & History Modal
    const adminUserDetailsMap = @json($userDetailsMap ?? []);
    let currentAdminModalUser = null;

    function openAdminUserDetail(userId) {
      const data = adminUserDetailsMap[userId];
      if (!data) return;
      currentAdminModalUser = data;

      const roleLabel = data.role === 'disuruh' ? 'MITRA' : data.role.toUpperCase();
      const isSuspended = !!data.is_suspended;
      document.getElementById('admin-detail-name').textContent = data.name + ' (' + roleLabel + ')' + (isSuspended ? ' - DITANGGUHKAN' : '');
      document.getElementById('admin-detail-meta').textContent =
        'ID #' + data.id + ' · ' + data.email + ' · ' + data.phone + ' · Gabung ' + data.joined_at;

      const suspendBox = document.getElementById('admin-detail-suspend-container');
      if (suspendBox) {
        if (Number(data.id) === Number({{ $user->id ?? 0 }})) {
          suspendBox.innerHTML = '<span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Akun Anda Sendiri (Admin)</span>';
        } else {
          const actionUrl = '/dashboard/users/' + (data.slug || data.id) + '/suspend';
          const confirmMsg = isSuspended ? 'Aktifkan kembali akun ' + data.name + '?' : 'Yakin ingin melakukan suspend pada akun ' + data.name + '?';
          suspendBox.innerHTML = `
            <form action="${actionUrl}" method="POST" style="margin:0;" onsubmit="return confirm('${confirmMsg}');">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <button type="submit" class="${isSuspended ? 'btn btn-primary' : 'btn-logout'}" style="padding:6px 14px; font-size:0.75rem; ${isSuspended ? 'background:#059669; border-color:#059669;' : ''}">
                ${isSuspended ? '✓ Aktifkan Kembali Akun' : '⛔ Suspend Akun Ini'}
              </button>
            </form>
          `;
        }
      }

      const isMitra = data.role === 'disuruh';
      document.getElementById('admin-detail-balance-label').textContent = isMitra ? 'Estimasi Saldo Mitra' : 'Estimasi Saldo Dompet';
      document.getElementById('admin-detail-balance').textContent = 'Rp ' + Number(data.estimated_balance || 0).toLocaleString('id-ID');

      document.getElementById('admin-detail-turnover-label').textContent = isMitra ? 'Total Pendapatan Selesai' : 'Total Pengeluaran Selesai';
      const turnoverVal = isMitra ? data.total_earned : data.total_spent;
      document.getElementById('admin-detail-turnover').textContent = 'Rp ' + Number(turnoverVal || 0).toLocaleString('id-ID');

      document.getElementById('admin-detail-active-count').textContent = data.active_count;
      document.getElementById('admin-detail-completed-count').textContent = data.completed_count;
      document.getElementById('admin-detail-cancelled-count').textContent = data.cancelled_count;

      const tabs = document.querySelectorAll('#admin-modal-filter-tabs .filter-tab');
      tabs.forEach(function(t, i) { t.classList.toggle('active', i === 0); });
      renderAdminModalHistory('all');

      document.getElementById('admin-user-detail-modal')?.classList.add('open');
    }

    function closeAdminUserDetail() {
      document.getElementById('admin-user-detail-modal')?.classList.remove('open');
    }

    document.getElementById('admin-user-detail-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeAdminUserDetail();
    });

    function filterAdminModalHistory(btn, status) {
      document.querySelectorAll('#admin-modal-filter-tabs .filter-tab').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      renderAdminModalHistory(status);
    }

    function renderAdminModalHistory(filterStatus) {
      const listEl = document.getElementById('admin-detail-history-list');
      if (!listEl || !currentAdminModalUser) return;

      const items = (currentAdminModalUser.history || []).filter(function(item) {
        if (filterStatus === 'all') return true;
        if (filterStatus === 'aktif') return item.status === 'menunggu' || item.status === 'diambil';
        return item.status === filterStatus;
      });

      if (items.length === 0) {
        listEl.innerHTML = '<li style="padding:24px; text-align:center; color:var(--text-muted); font-size:0.8125rem;">Tidak ada riwayat order pada filter ini.</li>';
        return;
      }

      listEl.innerHTML = items.map(function(item) {
        const badgeStyle = item.status === 'dibatalkan'
          ? 'background:#fef2f2; color:#dc2626;'
          : '';
        const badgeCls = item.status === 'selesai'
          ? 'done'
          : (item.status === 'diambil' ? 'aktif' : (item.status === 'dibatalkan' ? 'cancelled' : 'pending'));
        const badgeText = item.status === 'selesai'
          ? 'Selesai'
          : (item.status === 'diambil' ? 'Diambil' : (item.status === 'dibatalkan' ? 'Dibatalkan' : 'Menunggu'));

        return '<li class="task-item-compact">' +
          '<span class="task-status-badge ' + badgeCls + '" style="' + badgeStyle + '">' + badgeText + '</span>' +
          '<div class="task-compact-info">' +
            '<div class="task-compact-title">#PS-' + item.id + ' · ' + escapeHtml(item.title) + '</div>' +
            '<div class="task-compact-meta">' + escapeHtml(item.context) + ' · ' + escapeHtml(item.date) + ' · Partner: ' + escapeHtml(item.counterpart) + '</div>' +
          '</div>' +
          '<span class="task-compact-price">Rp ' + Number(item.price).toLocaleString('id-ID') + '</span>' +
        '</li>';
      }).join('');
    }

    function escapeHtml(str) {
      return String(str || '').replace(/[&<>"']/g, function(ch) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
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

    function setPickup(loc, lat, lng) {
      const el = document.getElementById('order-pickup');
      if (el) el.value = loc;
      if (typeof lat === 'number' && typeof lng === 'number') {
        setPickupCoordinates(lat, lng);
      }
    }

    function setDropoff(loc, lat, lng) {
      const el = document.getElementById('order-dropoff');
      if (el) el.value = loc;
      if (typeof lat === 'number' && typeof lng === 'number') {
        setTargetCoordinates('dropoff', lat, lng);
      }
    }

    function setPickupCoordinates(lat, lng) {
      setTargetCoordinates('pickup', lat, lng);
    }

    function setTargetCoordinates(target, lat, lng) {
      const isDropoff = target === 'dropoff';
      const latEl = document.getElementById(isDropoff ? 'order-dropoff-lat' : 'order-pickup-lat');
      const lngEl = document.getElementById(isDropoff ? 'order-dropoff-lng' : 'order-pickup-lng');
      const badge = document.getElementById(isDropoff ? 'dropoff-coord-badge' : 'pickup-coord-badge');
      const badgeText = document.getElementById(isDropoff ? 'dropoff-coord-text' : 'pickup-coord-text');

      if (latEl) latEl.value = Number(lat).toFixed(7);
      if (lngEl) lngEl.value = Number(lng).toFixed(7);
      if (badge && badgeText) {
        badge.style.display = 'flex';
        badgeText.textContent = (isDropoff ? 'Pin tujuan aktif (' : 'Pin jemput aktif (')
          + Number(lat).toFixed(5) + ', ' + Number(lng).toFixed(5) + ')';
      }
    }

    // ── Leaflet Map Picker (Penyuruh: Jemput & Tujuan) ──
    let pickupMap = null;
    let pickupMarker = null;
    let mapPickerTarget = 'pickup';
    let pendingPickupCoords = { lat: -7.275847, lng: 112.793755 };
    let pendingPickupAddress = 'Kampus PENS Sukolilo, Surabaya';

    function openPickupMapModal() {
      openMapPickerModal('pickup');
    }

    function openMapPickerModal(target) {
      mapPickerTarget = target === 'dropoff' ? 'dropoff' : 'pickup';
      const isDropoff = mapPickerTarget === 'dropoff';
      const modal = document.getElementById('pickup-map-modal');
      if (!modal) return;

      const titleEl = document.getElementById('pickup-map-title');
      if (titleEl) {
        titleEl.textContent = isDropoff ? 'Tentukan Titik Tujuan di Peta' : 'Tentukan Titik Jemput di Peta';
      }

      modal.classList.add('open');

      const latId = isDropoff ? 'order-dropoff-lat' : 'order-pickup-lat';
      const lngId = isDropoff ? 'order-dropoff-lng' : 'order-pickup-lng';
      const existingLat = parseFloat(document.getElementById(latId)?.value || '');
      const existingLng = parseFloat(document.getElementById(lngId)?.value || '');
      if (!isNaN(existingLat) && !isNaN(existingLng)) {
        pendingPickupCoords = { lat: existingLat, lng: existingLng };
      }

      setTimeout(function() {
        if (!pickupMap && typeof L !== 'undefined') {
          pickupMap = L.map('pickup-leaflet-map').setView([pendingPickupCoords.lat, pendingPickupCoords.lng], 15);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
          }).addTo(pickupMap);

          pickupMarker = L.marker([pendingPickupCoords.lat, pendingPickupCoords.lng], { draggable: true }).addTo(pickupMap);
          pickupMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updatePendingPickupLocation(pos.lat, pos.lng);
          });

          pickupMap.on('click', function(e) {
            updatePendingPickupLocation(e.latlng.lat, e.latlng.lng);
          });
        } else if (pickupMap) {
          pickupMap.invalidateSize();
          pickupMap.setView([pendingPickupCoords.lat, pendingPickupCoords.lng], 15);
          if (pickupMarker) pickupMarker.setLatLng([pendingPickupCoords.lat, pendingPickupCoords.lng]);
        }
        updatePendingPickupLocation(pendingPickupCoords.lat, pendingPickupCoords.lng);
      }, 120);
    }

    function closePickupMapModal() {
      document.getElementById('pickup-map-modal')?.classList.remove('open');
    }

    document.getElementById('pickup-map-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closePickupMapModal();
    });

    function updatePendingPickupLocation(lat, lng) {
      pendingPickupCoords = { lat: lat, lng: lng };
      if (pickupMarker) pickupMarker.setLatLng([lat, lng]);

      const coordsEl = document.getElementById('pickup-map-selected-coords');
      const addrEl = document.getElementById('pickup-map-selected-address');
      if (coordsEl) coordsEl.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
      if (addrEl) addrEl.textContent = 'Mengambil nama lokasi...';

      fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng))
        .then(function(r) { return r.json(); })
        .then(function(data) {
          pendingPickupAddress = data.display_name
            ? data.display_name.split(',').slice(0, 3).join(', ').trim()
            : ('Titik Peta (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ')');
          if (addrEl) addrEl.textContent = pendingPickupAddress;
        })
        .catch(function() {
          pendingPickupAddress = 'Titik Peta (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ')';
          if (addrEl) addrEl.textContent = pendingPickupAddress;
        });
    }

    function searchPickupAddress() {
      const q = document.getElementById('pickup-map-search')?.value.trim();
      if (!q) return;
      fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(results) {
          if (results && results.length > 0) {
            const lat = parseFloat(results[0].lat);
            const lng = parseFloat(results[0].lon);
            if (pickupMap) pickupMap.setView([lat, lng], 16);
            updatePendingPickupLocation(lat, lng);
          } else {
            alert('Lokasi tidak ditemukan. Coba kata kunci yang lebih spesifik.');
          }
        })
        .catch(function() {
          alert('Gagal mencari alamat. Pastikan koneksi internet aktif.');
        });
    }

    function centerPickupMapToGPS() {
      if (!navigator.geolocation) {
        alert('Browser tidak mendukung GPS.');
        return;
      }
      navigator.geolocation.getCurrentPosition(function(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        if (pickupMap) pickupMap.setView([lat, lng], 16);
        updatePendingPickupLocation(lat, lng);
      }, function() {
        alert('Izin lokasi/GPS ditolak atau tidak tersedia.');
      }, { enableHighAccuracy: true, timeout: 8000 });
    }

    function useMyCurrentLocationForPickup() {
      useMyCurrentLocationFor('pickup');
    }

    function useMyCurrentLocationFor(target) {
      const isDropoff = target === 'dropoff';
      if (!navigator.geolocation) {
        alert('Browser tidak mendukung GPS.');
        return;
      }
      navigator.geolocation.getCurrentPosition(function(pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        setTargetCoordinates(isDropoff ? 'dropoff' : 'pickup', lat, lng);
        const inputEl = document.getElementById(isDropoff ? 'order-dropoff' : 'order-pickup');
        if (inputEl) inputEl.value = 'Lokasi GPS (' + lat.toFixed(5) + ', ' + lng.toFixed(5) + ')';
        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng))
          .then(function(r) { return r.json(); })
          .then(function(data) {
            if (data.display_name && inputEl) {
              inputEl.value = data.display_name.split(',').slice(0, 3).join(', ').trim();
            }
          }).catch(function() {});
      }, function() {
        alert('Gagal mengakses GPS. Gunakan tombol "Pilih di Peta" untuk menandai lokasi manual.');
      }, { enableHighAccuracy: true, timeout: 8000 });
    }

    function confirmPickupMapSelection() {
      const isDropoff = mapPickerTarget === 'dropoff';
      const inputEl = document.getElementById(isDropoff ? 'order-dropoff' : 'order-pickup');
      if (inputEl && pendingPickupAddress) {
        inputEl.value = pendingPickupAddress;
      }
      setTargetCoordinates(mapPickerTarget, pendingPickupCoords.lat, pendingPickupCoords.lng);
      closePickupMapModal();
    }

    // ── Leaflet Route Map Viewer (Mitra & Penyuruh: Mitra -> Jemput -> Tujuan) ──
    let routeMap = null;
    let routeOrderMarker = null;
    let routeDestMarker = null;
    let routeMitraMarker = null;
    let routePolyline = null;
    let currentOrderCoords = null;
    let currentDestCoords = null;
    let currentMitraCoords = null;

    function openRouteMapModal(btn) {
      const modal = document.getElementById('route-map-modal');
      if (!modal) return;
      modal.classList.add('open');

      const title = btn.dataset.title || 'Order PenSuruh';
      const pickupText = btn.dataset.pickup || 'Lokasi Jemput';
      const dropoffText = btn.dataset.dropoff || '';
      const rawLat = parseFloat(btn.dataset.lat || '');
      const rawLng = parseFloat(btn.dataset.lng || '');
      const rawDestLat = parseFloat(btn.dataset.destLat || '');
      const rawDestLng = parseFloat(btn.dataset.destLng || '');

      currentOrderCoords = null;
      currentDestCoords = null;
      currentMitraCoords = null;

      document.getElementById('route-map-title').textContent = title;
      document.getElementById('route-map-subtitle').textContent = pickupText + (dropoffText ? (' → ' + dropoffText) : '');
      document.getElementById('route-status-text').textContent = 'Memuat lokasi jemput, tujuan, dan rute...';

      setTimeout(function() {
        if (!routeMap && typeof L !== 'undefined') {
          routeMap = L.map('route-leaflet-map').setView([-7.275847, 112.793755], 14);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
          }).addTo(routeMap);

          routeMap.on('click', function(e) {
            currentMitraCoords = { lat: e.latlng.lat, lng: e.latlng.lng };
            drawMitraToOrderRoute();
          });
        } else if (routeMap) {
          routeMap.invalidateSize();
          if (routeOrderMarker) { routeMap.removeLayer(routeOrderMarker); routeOrderMarker = null; }
          if (routeDestMarker) { routeMap.removeLayer(routeDestMarker); routeDestMarker = null; }
          if (routeMitraMarker) { routeMap.removeLayer(routeMitraMarker); routeMitraMarker = null; }
          if (routePolyline) { routeMap.removeLayer(routePolyline); routePolyline = null; }
        }

        const resolvePickup = (!isNaN(rawLat) && !isNaN(rawLng))
          ? Promise.resolve({ lat: rawLat, lng: rawLng, label: pickupText })
          : geocodeAddressFallback(pickupText, -7.275847, 112.793755);

        const resolveDest = (!isNaN(rawDestLat) && !isNaN(rawDestLng))
          ? Promise.resolve({ lat: rawDestLat, lng: rawDestLng, label: dropoffText || 'Lokasi Tujuan' })
          : (dropoffText ? geocodeAddressFallback(dropoffText, null, null) : Promise.resolve(null));

        Promise.all([resolvePickup, resolveDest]).then(function(results) {
          const pickupCoord = results[0];
          const destCoord = results[1];
          setupOrderDestinationAndRoute(pickupCoord, destCoord);
        });
      }, 120);
    }

    function geocodeAddressFallback(text, fallbackLat, fallbackLng) {
      return fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(text + ', Surabaya'))
        .then(function(r) { return r.json(); })
        .then(function(res) {
          if (res && res.length > 0) {
            return { lat: parseFloat(res[0].lat), lng: parseFloat(res[0].lon), label: text };
          }
          if (fallbackLat !== null && fallbackLng !== null) {
            return { lat: fallbackLat, lng: fallbackLng, label: text + ' (Perkiraan area)' };
          }
          return null;
        })
        .catch(function() {
          return (fallbackLat !== null && fallbackLng !== null)
            ? { lat: fallbackLat, lng: fallbackLng, label: text }
            : null;
        });
    }

    function closeRouteMapModal() {
      document.getElementById('route-map-modal')?.classList.remove('open');
    }

    document.getElementById('route-map-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeRouteMapModal();
    });

    function setupOrderDestinationAndRoute(pickupCoord, destCoord) {
      currentOrderCoords = { lat: pickupCoord.lat, lng: pickupCoord.lng };
      currentDestCoords = destCoord ? { lat: destCoord.lat, lng: destCoord.lng } : null;

      if (routeMap) {
        if (routeOrderMarker) routeMap.removeLayer(routeOrderMarker);
        routeOrderMarker = L.marker([pickupCoord.lat, pickupCoord.lng])
          .addTo(routeMap)
          .bindPopup('<b>Titik Jemput</b><br>' + escapeHtml(pickupCoord.label))
          .openPopup();

        if (currentDestCoords) {
          if (routeDestMarker) routeMap.removeLayer(routeDestMarker);
          routeDestMarker = L.circleMarker([destCoord.lat, destCoord.lng], {
            radius: 10,
            color: '#059669',
            fillColor: '#10b981',
            fillOpacity: 0.95,
            weight: 3
          }).addTo(routeMap).bindPopup('<b>Titik Tujuan</b><br>' + escapeHtml(destCoord.label));
        }

        routeMap.setView([pickupCoord.lat, pickupCoord.lng], 14);
      }

      updateGoogleMapsRouteLink();
      drawMitraToOrderRoute();
      refreshMitraGPSRoute();
    }

    function updateGoogleMapsRouteLink() {
      const gmapsLink = document.getElementById('route-gmaps-link');
      if (!gmapsLink || !currentOrderCoords) return;

      let url = 'https://www.google.com/maps/dir/?api=1';
      if (currentMitraCoords) {
        url += '&origin=' + encodeURIComponent(currentMitraCoords.lat + ',' + currentMitraCoords.lng);
      } else if (currentDestCoords) {
        url += '&origin=' + encodeURIComponent(currentOrderCoords.lat + ',' + currentOrderCoords.lng);
      }

      if (currentDestCoords) {
        if (currentMitraCoords) {
          url += '&waypoints=' + encodeURIComponent(currentOrderCoords.lat + ',' + currentOrderCoords.lng);
        }
        url += '&destination=' + encodeURIComponent(currentDestCoords.lat + ',' + currentDestCoords.lng);
      } else {
        url += '&destination=' + encodeURIComponent(currentOrderCoords.lat + ',' + currentOrderCoords.lng);
      }

      gmapsLink.href = url;
    }

    function refreshMitraGPSRoute() {
      const statusEl = document.getElementById('route-status-text');
      if (!navigator.geolocation) {
        if (statusEl && !currentDestCoords) {
          statusEl.textContent = 'GPS tidak didukung. Klik pada peta untuk set titik awalmu.';
        }
        return;
      }

      navigator.geolocation.getCurrentPosition(function(pos) {
        currentMitraCoords = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        drawMitraToOrderRoute();
      }, function() {
        if (statusEl && !currentDestCoords) {
          statusEl.textContent = 'GPS tidak aktif. Klik di peta untuk set titik awal rute ke order.';
        }
      }, { enableHighAccuracy: true, timeout: 7000 });
    }

    function drawMitraToOrderRoute() {
      if (!routeMap || !currentOrderCoords) return;
      const statusEl = document.getElementById('route-status-text');

      if (currentMitraCoords) {
        if (routeMitraMarker) routeMap.removeLayer(routeMitraMarker);
        routeMitraMarker = L.circleMarker([currentMitraCoords.lat, currentMitraCoords.lng], {
          radius: 9,
          color: '#0471fc',
          fillColor: '#38b6ff',
          fillOpacity: 0.95,
          weight: 3
        }).addTo(routeMap).bindPopup('<b>Posisi Awal / Kamu</b>');
      }

      updateGoogleMapsRouteLink();

      const waypoints = [];
      if (currentMitraCoords) waypoints.push(currentMitraCoords);
      waypoints.push(currentOrderCoords);
      if (currentDestCoords) waypoints.push(currentDestCoords);

      if (waypoints.length < 2) {
        if (statusEl) statusEl.textContent = 'Klik di peta atau aktifkan GPS untuk melihat rute menuju titik jemput.';
        return;
      }

      const osrmCoords = waypoints.map(function(p) { return p.lng + ',' + p.lat; }).join(';');
      const osrmUrl = 'https://router.project-osrm.org/route/v1/driving/' + osrmCoords + '?overview=full&geometries=geojson';

      fetch(osrmUrl)
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (routePolyline) routeMap.removeLayer(routePolyline);
          if (data.routes && data.routes.length > 0) {
            const route = data.routes[0];
            const coords = route.geometry.coordinates.map(function(c) { return [c[1], c[0]]; });
            routePolyline = L.polyline(coords, { color: '#0471fc', weight: 5, opacity: 0.85 }).addTo(routeMap);
            routeMap.fitBounds(routePolyline.getBounds(), { padding: [36, 36] });

            const distKm = (route.distance / 1000).toFixed(1);
            const durMin = Math.max(1, Math.round(route.duration / 60));
            const routeLabel = currentMitraCoords && currentDestCoords
              ? 'Rute (Kamu → Jemput → Tujuan)'
              : (currentDestCoords ? 'Rute (Jemput → Tujuan)' : 'Rute (Kamu → Jemput)');
            if (statusEl) statusEl.textContent = routeLabel + ': ' + distKm + ' km (~' + durMin + ' menit)';
          } else {
            drawFallbackDirectLine(waypoints);
          }
        })
        .catch(function() {
          drawFallbackDirectLine(waypoints);
        });
    }

    function drawFallbackDirectLine(waypoints) {
      if (!routeMap || !waypoints || waypoints.length < 2) return;
      if (routePolyline) routeMap.removeLayer(routePolyline);
      routePolyline = L.polyline(
        waypoints.map(function(p) { return [p.lat, p.lng]; }),
        { color: '#0471fc', weight: 4, dashArray: '8, 8' }
      ).addTo(routeMap);
      routeMap.fitBounds(routePolyline.getBounds(), { padding: [36, 36] });
      const statusEl = document.getElementById('route-status-text');
      if (statusEl) statusEl.textContent = 'Garis rute langsung ditampilkan (gunakan Google Maps untuk navigasi belokan).';
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

      const submitBtn = document.getElementById('btn-submit-order');
      if (submitBtn) {
        if (submitBtn.disabled) {
          e.preventDefault();
          return;
        }
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses Pesanan...';
      }
    });

    // Cegah double-click submit pada aksi Ambil / Selesai order
    document.querySelectorAll('form[action*="/orders/"]').forEach(function(form) {
      if (form.id === 'orderForm') return;
      form.addEventListener('submit', function(e) {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
          if (btn.disabled) {
            e.preventDefault();
            return;
          }
          btn.disabled = true;
          btn.textContent = 'Memproses...';
        }
      });
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

    // ==========================================
    // AUTO-SORT ORDERAN TERDEKAT (MITRA & ADMIN)
    // ==========================================
    const DEFAULT_REF_LAT = -7.275847; // Kampus PENS Sukolilo Surabaya
    const DEFAULT_REF_LNG = 112.793755;
    let userCurrentGpsLat = null;
    let userCurrentGpsLng = null;
    let adminSortNearestActive = true;

    function calcHaversineDistanceKm(lat1, lon1, lat2, lon2) {
      const R = 6371; // Radius bumi dalam km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }

    function formatDistanceText(km) {
      if (km < 1) {
        return Math.round(km * 1000) + ' m';
      }
      return km.toFixed(1) + ' km';
    }

    function detectMitraLocationAndSort(forceAlert) {
      const statusBadge = document.getElementById('mitra-nearest-status-badge');
      if (statusBadge) {
        statusBadge.innerHTML = '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#f59e0b;"></span><span>📍 Mengukur jarak GPS...</span>';
      }

      // Coba urutkan dulu dengan referensi default langsung agar instan tanpa nunggu GPS
      applyMitraNearestSorting(DEFAULT_REF_LAT, DEFAULT_REF_LNG, false);
      applyAdminNearestSorting(DEFAULT_REF_LAT, DEFAULT_REF_LNG, false);

      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(pos) {
            userCurrentGpsLat = pos.coords.latitude;
            userCurrentGpsLng = pos.coords.longitude;
            applyMitraNearestSorting(userCurrentGpsLat, userCurrentGpsLng, true);
            applyAdminNearestSorting(userCurrentGpsLat, userCurrentGpsLng, true);
            if (forceAlert) showToast('Lokasi GPS aktif! Orderan telah diurutkan dari yang terdekat.', 'success');
          },
          function(err) {
            applyMitraNearestSorting(DEFAULT_REF_LAT, DEFAULT_REF_LNG, false);
            applyAdminNearestSorting(DEFAULT_REF_LAT, DEFAULT_REF_LNG, false);
            if (forceAlert) showToast('GPS belum diizinkan. Menggunakan referensi area Kampus PENS.', 'info');
          },
          { enableHighAccuracy: true, timeout: 6000 }
        );
      }
    }

    function applyMitraNearestSorting(centerLat, centerLng, isRealGps) {
      const container = document.getElementById('mitra-incoming-orders-list');
      if (!container) return;

      const cards = Array.from(container.querySelectorAll('.incoming-order-card'));
      if (cards.length === 0) return;

      cards.forEach(card => {
        const rawLat = parseFloat(card.dataset.lat || '');
        const rawLng = parseFloat(card.dataset.lng || '');
        const badge = card.querySelector('.order-dist-badge');

        if (!isNaN(rawLat) && !isNaN(rawLng)) {
          const distKm = calcHaversineDistanceKm(centerLat, centerLng, rawLat, rawLng);
          card.dataset.distKm = distKm;
          if (badge) {
            badge.style.display = 'inline-block';
            badge.textContent = '📍 ' + formatDistanceText(distKm);
          }
        } else {
          card.dataset.distKm = 999999;
          if (badge) badge.style.display = 'none';
        }
      });

      // Urutkan kartu: Jarak terdekat berada di paling atas
      cards.sort((a, b) => {
        const distA = parseFloat(a.dataset.distKm || 999999);
        const distB = parseFloat(b.dataset.distKm || 999999);
        if (distA !== distB) return distA - distB;
        return (parseInt(b.dataset.created, 10) || 0) - (parseInt(a.dataset.created, 10) || 0);
      });

      cards.forEach(card => container.appendChild(card));

      const statusBadge = document.getElementById('mitra-nearest-status-badge');
      if (statusBadge) {
        statusBadge.innerHTML = isRealGps
          ? '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span><span>📍 Otomatis Terdekat dari Lokasimu (GPS Aktif)</span>'
          : '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#3b82f6;"></span><span>📍 Terurut Terdekat dari Area PENS</span>';
      }
    }

    function applyAdminNearestSorting(centerLat, centerLng, isRealGps) {
      const list = document.getElementById('admin-orders-list');
      if (!list) return;

      const items = Array.from(list.querySelectorAll('li.task-item-compact'));
      if (items.length === 0) return;

      items.forEach(item => {
        const rawLat = parseFloat(item.dataset.lat || '');
        const rawLng = parseFloat(item.dataset.lng || '');
        const badge = item.querySelector('.admin-dist-badge');

        if (!isNaN(rawLat) && !isNaN(rawLng)) {
          const distKm = calcHaversineDistanceKm(centerLat, centerLng, rawLat, rawLng);
          item.dataset.distKm = distKm;
          if (badge) {
            badge.style.display = 'inline-block';
            badge.textContent = '📍 ' + formatDistanceText(distKm);
          }
        } else {
          item.dataset.distKm = 999999;
          if (badge) badge.style.display = 'none';
        }
      });

      if (adminSortNearestActive) {
        // Urutkan pesanan admin: yang terdekat berada paling atas
        items.sort((a, b) => {
          const distA = parseFloat(a.dataset.distKm || 999999);
          const distB = parseFloat(b.dataset.distKm || 999999);
          if (distA !== distB) return distA - distB;
          return (parseInt(b.dataset.created, 10) || 0) - (parseInt(a.dataset.created, 10) || 0);
        });
        items.forEach(item => list.appendChild(item));
      }
    }

    function toggleAdminOrderSort(btn) {
      adminSortNearestActive = !adminSortNearestActive;
      const list = document.getElementById('admin-orders-list');
      if (!list) return;

      const items = Array.from(list.querySelectorAll('li.task-item-compact'));
      if (adminSortNearestActive) {
        if (btn) {
          btn.classList.add('active');
          btn.textContent = '📍 Terurut Terdekat';
        }
        const lat = userCurrentGpsLat || DEFAULT_REF_LAT;
        const lng = userCurrentGpsLng || DEFAULT_REF_LNG;
        applyAdminNearestSorting(lat, lng, !!userCurrentGpsLat);
        showToast('Pesanan diurutkan berdasarkan jarak terdekat.', 'info');
      } else {
        if (btn) {
          btn.classList.remove('active');
          btn.textContent = '⏱️ Urutkan Terbaru';
        }
        items.sort((a, b) => (parseInt(b.dataset.created, 10) || 0) - (parseInt(a.dataset.created, 10) || 0));
        items.forEach(item => list.appendChild(item));
        showToast('Pesanan diurutkan berdasarkan waktu terbaru.', 'info');
      }
    }

    // Jalankan auto-detect terdekat otomatis saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
      detectMitraLocationAndSort(false);
    });

    // ==========================================
    // ISI SALDO VIA QRIS DOKU PAYMENT GATEWAY
    // ==========================================
    let topUpPollingInterval = null;
    let activeTopUpInvoice = null;

    function openTopUpModal() {
      const modal = document.getElementById('qris-topup-modal');
      if (!modal) return;
      document.getElementById('topup-form-view').style.display = 'block';
      document.getElementById('topup-payment-view').style.display = 'none';
      document.getElementById('topup-payment-loading').style.display = 'block';
      document.getElementById('topup-payment-ready').style.display = 'none';
      document.getElementById('topup-payment-success').style.display = 'none';
      if (topUpPollingInterval) clearInterval(topUpPollingInterval);
      modal.classList.add('open');
    }

    function closeTopUpModal() {
      const modal = document.getElementById('qris-topup-modal');
      if (modal) modal.classList.remove('open');
      if (topUpPollingInterval) clearInterval(topUpPollingInterval);
    }

    function openWithdrawModal() {
      document.getElementById('withdraw-modal')?.classList.add('open');
    }

    function closeWithdrawModal() {
      document.getElementById('withdraw-modal')?.classList.remove('open');
    }

    document.getElementById('withdraw-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeWithdrawModal();
    });

    document.getElementById('qris-topup-modal')?.addEventListener('click', function(e) {
      if (e.target === this) closeTopUpModal();
    });

    function setTopUpAmount(amt, btn) {
      const input = document.getElementById('topup-amount-input');
      if (input) input.value = amt;
      if (btn) {
        document.querySelectorAll('#topup-quick-chips .location-chip').forEach(b => {
          b.classList.remove('active');
          b.style.background = '';
          b.style.color = '';
          b.style.borderColor = '';
        });
        btn.classList.add('active');
        btn.style.background = '#0471fc';
        btn.style.color = '#ffffff';
        btn.style.borderColor = '#0471fc';
      }
    }

    function processDokuQrisTopUp() {
      const input = document.getElementById('topup-amount-input');
      const amount = parseFloat(input ? input.value : 0);
      if (!amount || amount < 10000) {
        showToast('Minimal pengisian saldo adalah Rp 10.000', 'error');
        return;
      }

      const btn = document.getElementById('btn-proceed-qris');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menghubungkan ke DOKU...';
      }

      document.getElementById('topup-form-view').style.display = 'none';
      document.getElementById('topup-payment-view').style.display = 'block';
      document.getElementById('topup-payment-loading').style.display = 'block';
      document.getElementById('topup-payment-ready').style.display = 'none';
      document.getElementById('topup-payment-success').style.display = 'none';

      fetch('/wallet/topup/qris', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ amount: amount })
      })
      .then(res => res.json())
      .then(data => {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Bayar via QRIS Sekarang';
        }

        if (data.invoice) {
          activeTopUpInvoice = data.invoice;
          document.getElementById('topup-payment-loading').style.display = 'none';
          document.getElementById('topup-payment-ready').style.display = 'block';
          document.getElementById('topup-inv-display').textContent = data.invoice;
          document.getElementById('topup-amount-display').textContent = data.amount_fmt;

          // Render QR Code QRIS langsung di dalam modal
          const qrBox = document.getElementById('qris-qr-container');
          if (qrBox) {
            qrBox.innerHTML = '';
            const payloadText = data.qris_payload || ('QRIS-PENSURUH-' + data.invoice);
            if (typeof QRCode !== 'undefined') {
              new QRCode(qrBox, {
                text: payloadText,
                width: 178,
                height: 178,
                colorDark: '#0f172a',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
              });
            } else {
              const img = document.createElement('img');
              img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(payloadText);
              img.alt = 'QRIS Code';
              img.style.width = '178px';
              img.style.height = '178px';
              qrBox.appendChild(img);
            }
          }

          const link = document.getElementById('topup-doku-link');
          if (link) {
            if (data.doku_active && data.payment_url) {
              link.href = data.payment_url;
              link.style.display = 'flex';
            } else {
              link.style.display = 'none';
            }
          }

          const simBtn = document.getElementById('topup-simulate-btn');
          if (simBtn && data.simulate_url) simBtn.href = data.simulate_url;

          const statusEl = document.getElementById('topup-polling-status');
          if (statusEl) {
            statusEl.innerHTML = data.doku_active
              ? '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span><span>Menunggu pembayaran QRIS dari DOKU...</span>'
              : '<span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#f59e0b;"></span><span>QRIS Siap (Channel DOKU Back Office: ' + (data.message || 'Menunggu Aktivasi') + ')</span>';
          }

          startTopUpStatusPolling(data.invoice);
        } else {
          showToast(data.message || 'Gagal membuat QRIS.', 'error');
          document.getElementById('topup-form-view').style.display = 'block';
          document.getElementById('topup-payment-view').style.display = 'none';
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Bayar via QRIS Sekarang';
        }
        showToast('Gagal memproses permintaan QRIS: ' + err.message, 'error');
        document.getElementById('topup-form-view').style.display = 'block';
        document.getElementById('topup-payment-view').style.display = 'none';
      });
    }

    function startTopUpStatusPolling(invoice) {
      if (topUpPollingInterval) clearInterval(topUpPollingInterval);
      if (!invoice) return;

      topUpPollingInterval = setInterval(() => {
        fetch('/wallet/topup/status/' + invoice, {
          headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
          if (res.success && res.is_paid) {
            clearInterval(topUpPollingInterval);
            document.getElementById('topup-payment-ready').style.display = 'none';
            document.getElementById('topup-payment-success').style.display = 'block';
            document.getElementById('topup-success-msg').textContent = 'Saldo sebesar ' + res.amount_fmt + ' berhasil ditambahkan ke dompetmu.';

            const saldoEl = document.getElementById('penyuruh-saldo');
            if (saldoEl) {
              saldoEl.textContent = res.balance_fmt;
              saldoEl.dataset.value = res.balance_fmt;
            }

            showToast('Top up saldo via QRIS berhasil (' + res.amount_fmt + ')!', 'success');
          }
        })
        .catch(() => {});
      }, 3000);
    }
  </script>
</body>
</html>
