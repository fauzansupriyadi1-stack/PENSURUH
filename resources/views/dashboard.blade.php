<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PenSuruh - Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body id="dashboard-page">

  <!-- ═══════════════════════════════════════════ -->
  <!-- HEADER                                      -->
  <!-- ═══════════════════════════════════════════ -->
  <header class="dashboard-header">
    <div class="header-left">
      @if(isset($user) && $user->avatar)
        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="user-avatar" style="object-fit:cover;">
      @else
        <div class="user-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}</div>
      @endif

      <div class="user-greeting">
        <small>Selamat Datang</small>
        <strong>
          Halo, {{ $user->name ?? 'Pengguna' }}
          @if($activeRole === 'penyuruh')
            <span class="role-badge customer" id="current-role-badge">PENYURUH (CUSTOMER)</span>
          @elseif($activeRole === 'disuruh')
            <span class="role-badge mitra" id="current-role-badge">MITRA PESURUH</span>
          @else
            <span class="role-badge admin" id="current-role-badge">ADMINISTRATOR</span>
          @endif
        </strong>
      </div>

      <!-- Main navigation tabs -->
      <nav class="tab-nav" aria-label="Navigasi dashboard">
        <a href="#" class="tab-btn dashboard-tab-btn active" data-tab="ringkasan">Beranda</a>
        <a href="#" class="tab-btn dashboard-tab-btn" data-tab="tugas">
          Pesanan
          <span class="badge-count">{{ $activeRole === 'penyuruh' ? $myOrders->count() : $openOrders->count() }}</span>
        </a>
        <a href="#" class="tab-btn dashboard-tab-btn" data-tab="riwayat">
          Riwayat
          <span class="badge-count">{{ $completedOrders->count() }}</span>
        </a>
      </nav>

      <!-- Role Switcher -->
      <div class="mode-switcher">
        <span>Mode:</span>
        <a href="{{ route('dashboard.switch-role', 'penyuruh') }}" class="mode-link {{ $activeRole === 'penyuruh' ? 'active' : '' }}">Penyuruh</a>
        <a href="{{ route('dashboard.switch-role', 'disuruh') }}" class="mode-link {{ $activeRole === 'disuruh' ? 'active' : '' }}">Yang Disuruh</a>
        <a href="{{ route('dashboard.switch-role', 'admin') }}" class="mode-link {{ $activeRole === 'admin' ? 'active' : '' }}">Admin</a>
      </div>
    </div>

    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="/" style="font-size: 0.8125rem; color: var(--accent-blue); font-weight: 500;">Landing Page</a>
      <form action="/logout" method="POST" style="margin:0;">
        @csrf
        <button type="submit" style="font-size: 0.8125rem; color: #ef4444; font-weight: 600; padding: 6px 14px; border: 1px solid #fca5a5; border-radius: 8px; background: #fff5f5;">Keluar</button>
      </form>
    </div>
  </header>

  <!-- Flash Messages -->
  @if(session('success'))
    <div style="margin: 16px 32px; padding: 12px 16px; background: #d1fae5; border: 1px solid #6ee7b7; border-radius: 10px; color: #065f46; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 10px;">
      <span>✓</span> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="margin: 16px 32px; padding: 12px 16px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 10px; color: #991b1b; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 10px;">
      <span>!</span> {{ session('error') }}
    </div>
  @endif

  <main class="main-content">

    <!-- ═══════════════════════════════════════════ -->
    <!-- VIEW: PENYURUH (Customer - Gojek Style)    -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="role-view" id="view-penyuruh" style="{{ $activeRole !== 'penyuruh' ? 'display:none;' : '' }}">

      <!-- Tab: Beranda -->
      <div class="tab-content active" data-tab-panel="ringkasan">

        <!-- GoPay-style Wallet Bar -->
        <section class="gopay-bar" aria-label="Dompet PenSuruh">
          <div class="gopay-wallet-info">
            <div class="gopay-balance-box">
              <div class="gopay-balance-label">
                <span>Saldo Dompet PenSuruh</span>
                <span>PS-00340227AB</span>
              </div>
              <div class="gopay-balance-value">
                <span class="wallet-balance-amount" id="penyuruh-saldo" data-value="Rp 1.450.000">Rp 1.450.000</span>
                <button type="button" class="eye-toggle" onclick="toggleBalance()" aria-label="Sembunyikan saldo">👁</button>
              </div>
            </div>

            <div class="gopay-mini-stats">
              <div class="gopay-mini-stat">
                <small>Poin Suruh</small>
                <strong>★ 4.780</strong>
              </div>
              <div class="gopay-mini-stat">
                <small>Voucher Aktif</small>
                <strong id="penyuruh-voucher-count">🎫 8 Kupon</strong>
              </div>
            </div>
          </div>

          <div class="gopay-actions">
            <button type="button" class="btn-gopay" onclick="openTopUpModal()">+ Isi Saldo</button>
            <button type="button" class="btn-gopay-ghost" onclick="switchDashboardTab('tugas')">📋 Pesanan Saya</button>
            <button type="button" class="btn-gopay-ghost" onclick="switchDashboardTab('riwayat')">🧾 Mutasi</button>
          </div>
        </section>

        <!-- Gojek-style Quick Service Grid -->
        <section aria-label="Pilih layanan jasa suruh">
          <div class="section-heading">
            <span>Mau suruh apa hari ini?</span>
            <small>Pilih layanan untuk langsung memesan pesuruh terdekat</small>
          </div>

          <div class="service-grid">
            <button type="button" class="service-tile selected" data-service="Antar Paket & Dokumen" data-price="15000" onclick="selectService(this)">
              <div class="service-icon-circle blue" aria-hidden="true">📦</div>
              <span class="service-tile-name">Antar Paket</span>
              <span class="service-tile-price">Mulai Rp 15rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Beli Makan & Minum" data-price="12000" onclick="selectService(this)">
              <div class="service-icon-circle red" aria-hidden="true">🍜</div>
              <span class="service-tile-name">Beli Makan</span>
              <span class="service-tile-price">Mulai Rp 12rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Titip Belanja & Antre" data-price="18000" onclick="selectService(this)">
              <div class="service-icon-circle yellow" aria-hidden="true">🛒</div>
              <span class="service-tile-name">Titip Belanja</span>
              <span class="service-tile-price">Mulai Rp 18rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Bersih Rumah & Kost" data-price="45000" onclick="selectService(this)">
              <div class="service-icon-circle teal" aria-hidden="true">🧹</div>
              <span class="service-tile-name">Bersih Kost</span>
              <span class="service-tile-price">Mulai Rp 45rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Jemput / Antar Orang" data-price="14000" onclick="selectService(this)">
              <div class="service-icon-circle green" aria-hidden="true">🛵</div>
              <span class="service-tile-name">Antar Jemput</span>
              <span class="service-tile-price">Mulai Rp 14rb</span>
            </button>
            <button type="button" class="service-tile" data-service="Suruh Custom / Lainnya" data-price="25000" onclick="selectService(this)">
              <div class="service-icon-circle purple" aria-hidden="true">📝</div>
              <span class="service-tile-name">Suruh Custom</span>
              <span class="service-tile-price">Mulai Rp 25rb</span>
            </button>
          </div>
        </section>

        <!-- Order Form + Active Order -->
        <div class="penyuruh-workspace">

          <!-- Form Pesan Pesuruh (terhubung ke Laravel) -->
          <section class="card" id="order-form-card" aria-label="Form pemesanan pesuruh">
            <div class="card-header">
              <h2 class="card-title">🛵 Pesan Pesuruh Sekarang</h2>
              <span style="font-size: 0.75rem; color: var(--accent-green); font-weight: 600;">● 14 Mitra Online di Sekitarmu</span>
            </div>

            <form id="orderForm" action="{{ route('orders.create') }}" method="POST">
              @csrf
              <div class="form-group">
                <label class="form-label" for="order-service">Jenis Layanan</label>
                <select id="order-service" name="service_type" class="form-input" onchange="onServiceSelectChange(this)">
                  <option value="Antar Paket & Dokumen" data-price="15000" selected>Antar Paket & Dokumen (Rp 15.000)</option>
                  <option value="Beli Makan & Minum" data-price="12000">Beli Makan & Minum (Rp 12.000)</option>
                  <option value="Titip Belanja & Antre" data-price="18000">Titip Belanja & Antre (Rp 18.000)</option>
                  <option value="Bersih Rumah & Kost" data-price="45000">Bersih Rumah & Kost (Rp 45.000)</option>
                  <option value="Jemput / Antar Orang" data-price="14000">Jemput / Antar Orang (Rp 14.000)</option>
                  <option value="Suruh Custom / Lainnya" data-price="25000">Suruh Custom / Lainnya (Rp 25.000)</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="order-pickup">Lokasi Jemput / Lokasi Tugas</label>
                <input type="text" id="order-pickup" name="pickup_location" class="form-input" placeholder="Contoh: Kost Kejawan Putih Tambak No. 12" required>
                <div class="location-chips">
                  <button type="button" class="location-chip" onclick="setPickup('Kampus PENS Sukolilo')">📍 Kampus PENS</button>
                  <button type="button" class="location-chip" onclick="setPickup('Kost Kejawan Putih')">🏠 Kost Kejawan</button>
                  <button type="button" class="location-chip" onclick="setPickup('Galaxy Mall Surabaya')">🏬 Galaxy Mall</button>
                  <button type="button" class="location-chip" onclick="setPickup('Stasiun Gubeng')">🚉 Stasiun Gubeng</button>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="order-dropoff">Lokasi Tujuan <span style="font-weight:400; color:var(--text-muted);">(opsional)</span></label>
                <input type="text" id="order-dropoff" name="dropoff_location" class="form-input" placeholder="Contoh: Gedung D4 PENS / Jl. Arif Rahman Hakim">
              </div>

              <div class="form-group">
                <label class="form-label" for="order-notes">Detail Tugas untuk Pesuruh</label>
                <textarea id="order-notes" name="description" class="form-input" rows="2" placeholder="Contoh: Tolong antarkan map biru ke ruang TU lantai 2." required></textarea>
              </div>

              <div class="form-row" style="margin-bottom: 16px;">
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label" for="order-voucher">Pakai Voucher</label>
                  <select id="order-voucher" class="form-input" onchange="updateOrderTotal()">
                    <option value="0">Tanpa Voucher</option>
                    <option value="5000">HEMAT5RB (-Rp 5.000)</option>
                    <option value="10000">SURUHKILAT (-Rp 10.000)</option>
                  </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label">Estimasi Waktu</label>
                  <div class="form-input" style="background: var(--bg-gray); color: var(--text-secondary);">⚡ 15 - 25 menit</div>
                </div>
              </div>

              <!-- Hidden price field -->
              <input type="hidden" name="price" id="order-price-hidden" value="15000">

              <div class="order-summary-box">
                <div>
                  <div class="total-label">Total Estimasi Ongkos</div>
                  <small id="order-summary-detail" style="color: var(--text-muted);">Antar Paket & Dokumen</small>
                </div>
                <div class="total-price" id="order-total-price">Rp 15.000</div>
              </div>

              <button type="submit" class="btn-auth" id="btn-submit-order">Cari Pesuruh Sekarang</button>
            </form>
          </section>

          <!-- Right: Active Order Tracker + Pesan Lagi -->
          <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Pesanan Aktif dari DB -->
            <section class="card active-order-card" aria-label="Pesanan aktif">
              <div class="card-header">
                <h3 class="card-title">📍 Pesanan Aktif</h3>
                @php $aktif = $myOrders->where('status', 'open')->first(); @endphp
                @if($aktif)
                  <span class="order-status-pill">Menunggu Mitra</span>
                @else
                  <span class="order-status-pill" style="background:#f3f4f6; color:#6b7280;">Tidak Ada</span>
                @endif
              </div>

              @if($aktif)
                <div>
                  <div style="font-weight: 700; font-size: 0.9375rem;">{{ $aktif->service_type ?? $aktif->title }}</div>
                  <div style="font-size: 0.8125rem; color: var(--text-secondary);">{{ $aktif->pickup_location ?? '-' }} → {{ $aktif->dropoff_location ?? 'Lokasi Tugas' }}</div>
                  <div class="order-progress" style="margin: 16px 0;">
                    <div class="order-progress-step done"><div class="bar"></div>Dipesan</div>
                    <div class="order-progress-step {{ $aktif->disuruh_id ? 'done' : '' }}"><div class="bar"></div>Dijemput</div>
                    <div class="order-progress-step"><div class="bar"></div>Dikerjakan</div>
                    <div class="order-progress-step"><div class="bar"></div>Selesai</div>
                  </div>
                  @if($aktif->disuruh_id)
                    <div class="mitra-info-box">
                      <div class="mitra-avatar">{{ strtoupper(substr($aktif->mitra->name ?? 'M', 0, 2)) }}</div>
                      <div style="flex:1;">
                        <div style="font-weight:700; font-size:0.875rem;">{{ $aktif->mitra->name ?? 'Mitra' }}</div>
                        <div style="font-size:0.75rem; color:var(--text-secondary);">★ 4.9 · Sedang menuju lokasi</div>
                      </div>
                    </div>
                  @endif
                </div>
              @else
                <div style="text-align:center; padding: 24px 0; color: var(--text-muted);">
                  <div style="font-size:2rem; margin-bottom:8px;">📋</div>
                  <div style="font-size:0.875rem;">Belum ada pesanan aktif.<br>Pesan pesuruh sekarang!</div>
                </div>
              @endif
            </section>

            <!-- Pesan Lagi -->
            <section class="card" aria-label="Pesan lagi tugas favorit">
              <div class="card-header">
                <h3 class="card-title">🔁 Pesan Lagi</h3>
                <a href="#" class="activity-link" onclick="switchDashboardTab('tugas'); return false;">Lihat Semua</a>
              </div>

              @forelse($myOrders->take(3) as $ord)
                <div class="reorder-card">
                  <div>
                    <div style="font-weight:600; font-size:0.875rem;">{{ $ord->service_type ?? $ord->title }}</div>
                    <div style="font-size:0.75rem; color:var(--text-muted);">{{ $ord->pickup_location ?? '-' }} · Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</div>
                  </div>
                  <button type="button" class="btn btn-outline" style="padding:6px 12px; font-size:0.75rem;" onclick="alert('Fitur pesan ulang segera hadir!')">Pesan</button>
                </div>
              @empty
                <div style="font-size:0.875rem; color:var(--text-muted); text-align:center; padding:16px 0;">Belum ada riwayat pesanan.</div>
              @endforelse
            </section>

          </div>
        </div>
      </div>

      <!-- Tab: Pesanan / Tugas Suruh (Penyuruh) -->
      <div class="tab-content" data-tab-panel="tugas">
        <div class="card">
          <h3 class="card-title" style="margin-bottom: 20px;">📋 Daftar Pesanan Suruh Kamu</h3>

          <div class="filter-tabs" style="margin-bottom: 20px;">
            <button type="button" class="filter-tab active" onclick="filterCustomerTasks(this, 'all')">Semua</button>
            <button type="button" class="filter-tab" onclick="filterCustomerTasks(this, 'aktif')">Aktif</button>
            <button type="button" class="filter-tab" onclick="filterCustomerTasks(this, 'done')">Selesai</button>
          </div>

          <ul class="task-list-compact" id="penyuruh-task-list">
            @forelse($myOrders as $ord)
              <li class="task-item-compact" data-status="{{ $ord->status === 'completed' ? 'done' : 'aktif' }}">
                <span class="task-status-badge {{ $ord->status === 'completed' ? 'done' : 'aktif' }}">{{ $ord->status === 'completed' ? 'Selesai' : 'Aktif' }}</span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->service_type ?? $ord->title }}</div>
                  <div class="task-compact-meta">{{ $ord->created_at->format('d M Y') }} · Mitra: {{ $ord->mitra->name ?? 'Belum ada' }}</div>
                </div>
                <span class="task-compact-price">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:24px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada pesanan.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Tab: Riwayat (Penyuruh) -->
      <div class="tab-content" data-tab-panel="riwayat">
        <div class="card">
          <h3 class="card-title" style="margin-bottom: 20px;">🧾 Riwayat Mutasi Dompet</h3>
          <ul class="transaction-list" id="penyuruh-history-list">
            @forelse($completedOrders as $ord)
              <li class="transaction-item">
                <div class="transaction-icon send" aria-hidden="true">📄</div>
                <div class="transaction-info">
                  <div class="transaction-name">{{ $ord->service_type ?? $ord->title }}</div>
                  <div class="transaction-date">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <span class="transaction-amount debit">-Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:24px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada riwayat transaksi.</li>
            @endforelse
          </ul>
        </div>
      </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- VIEW: YANG DISURUH (Mitra Pelaksana)       -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="role-view" id="view-disuruh" style="{{ $activeRole !== 'disuruh' ? 'display:none;' : '' }}">

      <div class="tab-content active" data-tab-panel="ringkasan">

        <!-- Mitra Online Toggle -->
        <section class="mitra-status-bar" aria-label="Status mitra online">
          <div>
            <div style="font-size:0.75rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase;">Status Akun Mitra</div>
            <div style="font-size:1.125rem; font-weight:700;" id="mitra-status-text">🟢 Online — Siap Menerima Tugas Suruh</div>
          </div>
          <button type="button" class="btn-toggle-online" id="btn-mitra-online" onclick="toggleMitraOnline(this)">Nonaktifkan Mode Terima Order</button>
        </section>

        <!-- Mitra Earnings + Rating -->
        <section class="wallet-section" aria-label="Pendapatan mitra">
          <div class="wallet-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <span class="wallet-label">Pendapatan Tersedia (Bisa Ditarik)</span>
              <span style="font-size:0.75rem; color:var(--text-muted);">MITRA ID: MTR-{{ str_pad($user->id ?? 1, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="wallet-balance">
              <span class="wallet-balance-amount" data-value="Rp 875.000">Rp 875.000</span>
              <button type="button" class="eye-toggle" onclick="toggleBalance()" aria-label="Sembunyikan pendapatan">👁</button>
            </div>
            <div class="wallet-actions">
              <button type="button" class="btn btn-primary" onclick="alert('Penarikan dana mitra ke rekening diproses (Demo).')">↓ Tarik Pendapatan</button>
              <button type="button" class="btn btn-outline" onclick="switchDashboardTab('riwayat')">Lihat Slip Komisi</button>
            </div>
          </div>

          <div class="priority-card">
            <div class="priority-label">🏆 PERFORMA MITRA</div>
            <div style="margin-bottom:4px; font-size:0.8125rem; color:rgba(255,255,255,0.7);">Pendapatan Minggu Ini</div>
            <div class="priority-balance">
              Rp 340.000 <span class="priority-change">{{ $completedOrders->count() }} Tugas Selesai</span>
            </div>
            <div class="priority-stats">
              <div class="priority-stat">
                <div>
                  <div class="priority-stat-label">Rating Pelanggan</div>
                  <div class="priority-stat-value"><span class="star">★</span> 4.92</div>
                </div>
              </div>
              <div class="priority-stat">
                <div>
                  <div class="priority-stat-label">Tingkat Terima</div>
                  <div class="priority-stat-value">96%</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Order Masuk + Tugas Berjalan -->
        <div class="grid-2">
          <!-- Order Masuk -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">🔔 Orderan Masuk di Sekitarmu</h3>
              <span style="font-size:0.75rem; color:var(--accent-blue); font-weight:600;">Radius 3 km</span>
            </div>

            @forelse($openOrders->take(3) as $ord)
              <div class="incoming-order-card">
                <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                  <strong>{{ $ord->service_type ?? $ord->title }}</strong>
                  <span style="font-weight:800; color:var(--accent-green);">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                </div>
                <div style="font-size:0.8125rem; color:var(--text-secondary); margin-bottom:10px;">
                  {{ $ord->pickup_location ?? 'Lokasi Tugas' }} → {{ $ord->dropoff_location ?? 'Tujuan' }}
                </div>
                <form action="{{ route('orders.take', $ord->slug) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Ambil Order Ini</button>
                </form>
              </div>
            @empty
              <div style="text-align:center; padding:24px; color:var(--text-muted); font-size:0.875rem;">
                <div style="font-size:2rem; margin-bottom:8px;">🔍</div>
                Tidak ada orderan masuk saat ini.<br>Tetap online untuk menerima tugas!
              </div>
            @endforelse
          </div>

          <!-- Tugas Yang Harus Dikerjakan -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">⚡ Tugas Yang Harus Dikerjakan</h3>
            </div>
            <ul class="task-list-compact" id="mitra-active-tasks">
              @forelse($myOrders->where('status', 'taken') as $ord)
                <li class="task-item-compact">
                  <span class="task-status-badge aktif">Berjalan</span>
                  <div class="task-compact-info">
                    <div class="task-compact-title">{{ $ord->service_type ?? $ord->title }}</div>
                    <div class="task-compact-meta">Pemesan: {{ $ord->penyuruh->name ?? '-' }}</div>
                  </div>
                  <div style="display:flex; flex-direction:column; align-items:flex-end; gap:4px;">
                    <span class="task-compact-price">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
                    <form action="{{ route('orders.complete', $ord->slug) }}" method="POST" style="margin:0;">
                      @csrf
                      <button type="submit" class="btn btn-outline" style="padding:4px 10px; font-size:0.75rem;">Selesai</button>
                    </form>
                  </div>
                </li>
              @empty
                <li style="padding:16px; text-align:center; color:var(--text-muted); font-size:0.875rem;">Belum ada tugas aktif.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      <!-- Tab: Tugas (Mitra) -->
      <div class="tab-content" data-tab-panel="tugas">
        <div class="card">
          <h3 class="card-title" style="margin-bottom:16px;">📋 Semua Tugas Mitra</h3>
          <ul class="task-list-compact">
            @forelse($myOrders as $ord)
              <li class="task-item-compact">
                <span class="task-status-badge {{ $ord->status === 'completed' ? 'done' : 'aktif' }}">{{ $ord->status === 'completed' ? 'Selesai' : 'Berjalan' }}</span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->service_type ?? $ord->title }}</div>
                  <div class="task-compact-meta">Pemesan: {{ $ord->penyuruh->name ?? '-' }}</div>
                </div>
                <span class="task-compact-price">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:24px; text-align:center; color:var(--text-muted);">Belum ada tugas.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Tab: Riwayat (Mitra) -->
      <div class="tab-content" data-tab-panel="riwayat">
        <div class="card">
          <h3 class="card-title" style="margin-bottom:16px;">🧾 Riwayat Komisi & Pencairan Mitra</h3>
          <ul class="transaction-list">
            @forelse($completedOrders as $ord)
              <li class="transaction-item">
                <div class="transaction-icon receive" aria-hidden="true">+</div>
                <div class="transaction-info">
                  <div class="transaction-name">Komisi Tugas: {{ $ord->service_type ?? $ord->title }}</div>
                  <div class="transaction-date">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <span class="transaction-amount credit">+Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:24px; text-align:center; color:var(--text-muted);">Belum ada riwayat.</li>
            @endforelse
          </ul>
        </div>
      </div>

    </div>

    <!-- ═══════════════════════════════════════════ -->
    <!-- VIEW: ADMIN (Kontrol Penuh)                -->
    <!-- ═══════════════════════════════════════════ -->
    <div class="role-view" id="view-admin" style="{{ $activeRole !== 'admin' ? 'display:none;' : '' }}">

      <div class="tab-content active" data-tab-panel="ringkasan">

        <!-- Admin Stat Cards -->
        <div class="stat-cards">
          <div class="stat-card">
            <div class="stat-label">Total Akun Pengguna</div>
            <div class="stat-value">{{ $adminStats['total_users'] }}</div>
            <div class="stat-link"><a href="#">Semua Role</a></div>
          </div>
          <div class="stat-card">
            <div class="stat-label" style="color:var(--accent-blue);">Akun Penyuruh (Customer)</div>
            <div class="stat-value">{{ $adminStats['total_penyuruh'] }}</div>
            <div class="stat-sub">Pemberi Tugas</div>
          </div>
          <div class="stat-card">
            <div class="stat-label" style="color:var(--accent-green);">Akun Yang Disuruh (Mitra)</div>
            <div class="stat-value">{{ $adminStats['total_disuruh'] }}</div>
            <div class="stat-sub">Pelaksana</div>
          </div>
          <div class="stat-card highlight">
            <div class="stat-label">Total Transaksi Selesai</div>
            <div class="stat-value">Rp {{ number_format($adminStats['total_turnover'], 0, ',', '.') }} <span class="order-badge">{{ $adminStats['completed_count'] }} Order</span></div>
          </div>
        </div>

        <!-- User Management -->
        <div class="management-section">
          <div class="management-header">
            <div>
              <div class="management-label">KONTROL PENUH PLATFORM</div>
              <h2 class="management-title">Manajemen Seluruh Akun</h2>
              <p class="management-desc">Kelola, monitor, dan ubah hak peran akun penyuruh maupun mitra yang disuruh.</p>
            </div>
            <div class="search-box">
              <span class="search-icon">🔍</span>
              <input type="text" id="admin-search" placeholder="Cari nama atau email.." onkeyup="filterUserList()">
            </div>
          </div>

          <!-- Filter Tabs -->
          <div class="filter-tabs">
            <button type="button" class="filter-tab admin-filter-tab active" data-filter="all">Semua Akun <span class="count">{{ $allUsers->count() }}</span></button>
            <button type="button" class="filter-tab admin-filter-tab" data-filter="penyuruh">Akun Penyuruh <span class="count">{{ $adminStats['total_penyuruh'] }}</span></button>
            <button type="button" class="filter-tab admin-filter-tab" data-filter="disuruh">Akun Mitra <span class="count">{{ $adminStats['total_disuruh'] }}</span></button>
          </div>

          <!-- User Table -->
          <table class="user-table" id="userAccountsTable">
            <thead>
              <tr>
                <th>Nama Pengguna</th>
                <th>Email & No. HP</th>
                <th>Peran (Role)</th>
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
                      <span style="font-size:0.875rem; color:var(--text-secondary);"><b>{{ $u->orders_created_count }}</b> order dibuat</span>
                    @elseif($u->role === 'disuruh')
                      <span style="font-size:0.875rem; color:var(--text-secondary);"><b>{{ $u->orders_taken_count }}</b> tugas diambil</span>
                    @else
                      <span style="color:var(--text-secondary);">Akses Penuh</span>
                    @endif
                  </td>
                  <td><span class="status-dot active">Aktif</span></td>
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

      <!-- Tab: Tugas (Admin) -->
      <div class="tab-content" data-tab-panel="tugas">
        <div class="card">
          <h3 class="card-title" style="margin-bottom:16px;">📋 Monitoring Seluruh Pesanan Platform</h3>
          <ul class="task-list-compact">
            @forelse($openOrders->merge($completedOrders) as $ord)
              <li class="task-item-compact">
                <span class="task-status-badge {{ $ord->status === 'completed' ? 'done' : 'aktif' }}">{{ $ord->status === 'completed' ? 'Selesai' : 'Aktif' }}</span>
                <div class="task-compact-info">
                  <div class="task-compact-title">{{ $ord->service_type ?? $ord->title }}</div>
                  <div class="task-compact-meta">Penyuruh: {{ $ord->penyuruh->name ?? '-' }} · Mitra: {{ $ord->mitra->name ?? 'Belum ada' }}</div>
                </div>
                <span class="task-compact-price">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:24px; text-align:center; color:var(--text-muted);">Belum ada pesanan di platform.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Tab: Riwayat (Admin) -->
      <div class="tab-content" data-tab-panel="riwayat">
        <div class="card">
          <h3 class="card-title" style="margin-bottom:16px;">🧾 Log Transaksi Platform</h3>
          <ul class="transaction-list">
            @forelse($completedOrders as $ord)
              <li class="transaction-item">
                <div class="transaction-icon receive" aria-hidden="true">✓</div>
                <div class="transaction-info">
                  <div class="transaction-name">Order #PS-{{ $ord->id }} Selesai · {{ $ord->service_type ?? $ord->title }}</div>
                  <div class="transaction-date">{{ $ord->updated_at->format('d M Y, H:i') }} WIB</div>
                </div>
                <span class="transaction-amount credit">Rp {{ number_format($ord->price ?? 0, 0, ',', '.') }}</span>
              </li>
            @empty
              <li style="padding:24px; text-align:center; color:var(--text-muted);">Belum ada transaksi selesai.</li>
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

  <script src="/pensuruh-web/js/app.js"></script>
  <script>
    // Override switchDashboardTab agar cocok dengan data-tab-panel di blade
    function switchDashboardTab(tab) {
      const currentView = document.querySelector('.role-view:not([style*="display:none"])') ||
                          document.querySelector('.role-view');
      if (!currentView) return;
      currentView.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      const target = currentView.querySelector(`[data-tab-panel="${tab}"]`);
      if (target) target.classList.add('active');
      currentView.querySelectorAll('.dashboard-tab-btn').forEach(b => b.classList.remove('active'));
      const btn = currentView.closest('body')?.querySelector(`.dashboard-tab-btn[data-tab="${tab}"]`);
      if (btn) btn.classList.add('active');
    }

    // Admin user filter search
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
        document.querySelectorAll('.admin-filter-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('#userAccountsTable tbody tr').forEach(row => {
          row.style.display = (filter === 'all' || row.dataset.role === filter) ? '' : 'none';
        });
      });
    });

    // Penyuruh filter tasks
    function filterCustomerTasks(btn, status) {
      document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('#penyuruh-task-list .task-item-compact').forEach(li => {
        li.style.display = (status === 'all' || li.dataset.status === status) ? '' : 'none';
      });
    }

    // Lokasi cepat
    function setPickup(loc) {
      const el = document.getElementById('order-pickup');
      if (el) el.value = loc;
    }

    // Update price hidden field when service changes
    function onServiceSelectChange(sel) {
      const opt = sel.options[sel.selectedIndex];
      const price = opt.dataset.price || 0;
      document.getElementById('order-price-hidden').value = price;
      updateOrderTotal();
    }
  </script>
</body>
</html>
