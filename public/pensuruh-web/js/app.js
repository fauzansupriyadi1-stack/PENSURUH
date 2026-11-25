/* PenSuruh - Front-end interactions (no backend) */

// ── Helpers ──
function formatRupiah(num) {
  return 'Rp ' + Number(num).toLocaleString('id-ID');
}

var customerBalance = 1450000;
var selectedTopUpNominal = 100000;
var selectedPaymentMethod = 'Saldo Dompet';
var activeOrderStep = 2; // 1: Dipesan, 2: Dijemput, 3: Dikerjakan, 4: Selesai

// ── Auth Page: Tab Switching ──
function switchTab(tab) {
  document.querySelectorAll('.auth-tab').forEach(function(t) {
    t.classList.toggle('active', t.dataset.tab === tab);
  });
  document.querySelectorAll('.auth-form').forEach(function(f) {
    f.classList.toggle('active', f.id === 'form-' + tab);
  });
}

document.querySelectorAll('.auth-tab').forEach(function(tab) {
  tab.addEventListener('click', function(e) {
    e.preventDefault();
    switchTab(this.dataset.tab);
  });
});

// ── Password Toggle ──
function togglePassword(inputId, btn) {
  var input = document.getElementById(inputId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    btn.textContent = '🙈';
    btn.setAttribute('aria-label', 'Sembunyikan password');
  } else {
    input.type = 'password';
    btn.textContent = '👁';
    btn.setAttribute('aria-label', 'Tampilkan password');
  }
}

// ── Role Selector (Register) ──
function selectRole(element, role) {
  document.querySelectorAll('.role-option').forEach(function(opt) {
    opt.classList.remove('active');
  });
  element.classList.add('active');
}

// ── Username Check (Demo) ──
var usernameInput = document.getElementById('reg-username');
if (usernameInput) {
  usernameInput.addEventListener('input', function() {
    var check = document.getElementById('username-check');
    if (check) {
      check.style.display = this.value.length >= 3 ? 'inline' : 'none';
    }
  });
}

// ── Google Sign-In (Demo) ──
function googleSignIn() {
  window.location.href = 'dashboard.html?role=penyuruh';
}

function googleSignUp() {
  var selectedRole = document.querySelector('.role-option.active');
  var role = selectedRole ? selectedRole.dataset.role : 'customer';
  window.location.href = 'dashboard.html?role=' + (role === 'mitra' ? 'disuruh' : 'penyuruh');
}

// ── Form Submissions (Auth Demo) ──
var loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    var email = document.getElementById('login-email').value.trim();
    var password = document.getElementById('login-password').value;

    if (!email) {
      showFormError('login-email', 'Masukkan nomor WhatsApp atau email.');
      return;
    }
    if (!password) {
      showFormError('login-password', 'Masukkan password.');
      return;
    }

    window.location.href = 'dashboard.html?role=penyuruh';
  });
}

var registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', function(e) {
    e.preventDefault();
    var name = document.getElementById('reg-name').value.trim();
    var username = document.getElementById('reg-username').value.trim();
    var phone = document.getElementById('reg-phone').value.trim();
    var password = document.getElementById('reg-password').value;

    if (!name) { showFormError('reg-name', 'Masukkan nama lengkap.'); return; }
    if (!username) { showFormError('reg-username', 'Masukkan username.'); return; }
    if (!phone) { showFormError('reg-phone', 'Masukkan nomor WhatsApp.'); return; }
    if (!password || password.length < 8) { showFormError('reg-password', 'Password minimal 8 karakter.'); return; }

    var selectedRole = document.querySelector('.role-option.active');
    var role = selectedRole ? selectedRole.dataset.role : 'customer';
    window.location.href = 'dashboard.html?role=' + (role === 'mitra' ? 'disuruh' : 'penyuruh');
  });
}

// ── Form Error Display ──
function showFormError(inputId, message) {
  var input = document.getElementById(inputId);
  if (!input) return;

  var wrapper = input.closest('.form-input-wrapper') || input.parentElement;
  var existing = wrapper.querySelector('.form-error');
  if (existing) existing.remove();

  input.style.borderColor = '#ef4444';
  var errorEl = document.createElement('p');
  errorEl.className = 'form-error';
  errorEl.style.cssText = 'color: #ef4444; font-size: 0.75rem; margin-top: 4px;';
  errorEl.textContent = message;
  wrapper.appendChild(errorEl);

  input.addEventListener('input', function handler() {
    input.style.borderColor = '';
    var err = wrapper.querySelector('.form-error');
    if (err) err.remove();
    input.removeEventListener('input', handler);
  }, { once: true });

  input.focus();
}

// ── Dashboard: Tab Content Switching (scoped to active role view) ──
function switchDashboardTab(tabName) {
  document.querySelectorAll('.dashboard-tab-btn').forEach(function(btn) {
    btn.classList.toggle('active', btn.dataset.tab === tabName);
  });

  var role = getCurrentRole();
  var activeView = document.getElementById('view-' + role);
  if (!activeView) return;

  activeView.querySelectorAll('.tab-content').forEach(function(content) {
    content.classList.toggle('active', content.dataset.tabPanel === tabName);
  });
}

document.querySelectorAll('.dashboard-tab-btn').forEach(function(btn) {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    switchDashboardTab(this.dataset.tab);
  });
});

// ── Dashboard: Role Mode Switching ──
function switchRole(role) {
  window.location.href = 'dashboard.html?role=' + role;
}

document.querySelectorAll('.mode-link').forEach(function(link) {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    switchRole(this.dataset.role);
  });
});

function getCurrentRole() {
  var params = new URLSearchParams(window.location.search);
  return params.get('role') || 'penyuruh';
}

function initDashboard() {
  var role = getCurrentRole();

  document.querySelectorAll('.role-view').forEach(function(view) {
    var isTarget = view.id === 'view-' + role;
    view.classList.toggle('active', isTarget);
    view.style.display = isTarget ? 'block' : 'none';
  });

  var roleBadge = document.getElementById('current-role-badge');
  if (roleBadge) {
    var roleLabels = {
      penyuruh: { text: 'PENYURUH (CUSTOMER)', cls: 'customer' },
      disuruh: { text: 'YANG DISURUH (MITRA)', cls: 'mitra' },
      admin: { text: 'ADMINISTRATOR', cls: 'admin' }
    };
    var r = roleLabels[role] || roleLabels.penyuruh;
    roleBadge.textContent = r.text;
    roleBadge.className = 'role-badge ' + r.cls;
  }

  document.querySelectorAll('.mode-link').forEach(function(link) {
    link.classList.toggle('active', link.dataset.role === role);
  });

  switchDashboardTab('ringkasan');
}

if (document.getElementById('dashboard-page')) {
  initDashboard();
}

// ── Wallet: Balance Toggle & Update ──
function syncBalanceUI() {
  var formatted = formatRupiah(customerBalance);
  var saldoEl = document.getElementById('penyuruh-saldo');
  if (saldoEl) {
    saldoEl.dataset.value = formatted;
    if (saldoEl.dataset.hidden !== 'true') {
      saldoEl.textContent = formatted;
    }
  }
  var payPreview = document.getElementById('pay-saldo-preview');
  if (payPreview) {
    payPreview.textContent = formatted;
  }
}

function toggleBalance() {
  var balanceEls = document.querySelectorAll('.wallet-balance-amount');
  balanceEls.forEach(function(el) {
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

// ── Top Up Modal ──
function openTopUpModal() {
  var modal = document.getElementById('topup-modal');
  if (modal) modal.classList.add('open');
}

function closeTopUpModal() {
  var modal = document.getElementById('topup-modal');
  if (modal) modal.classList.remove('open');
}

function selectTopUpNominal(btn, amount) {
  selectedTopUpNominal = amount;
  document.querySelectorAll('.nominal-btn').forEach(function(b) {
    b.classList.remove('selected');
  });
  btn.classList.add('selected');
}

function confirmTopUp() {
  customerBalance += selectedTopUpNominal;
  syncBalanceUI();

  var historyList = document.getElementById('penyuruh-history-list');
  if (historyList) {
    var li = document.createElement('li');
    li.className = 'transaction-item';
    li.innerHTML =
      '<div class="transaction-icon receive" aria-hidden="true">+</div>' +
      '<div class="transaction-info">' +
        '<div class="transaction-name">Isi Saldo Dompet</div>' +
        '<div class="transaction-date">Baru saja</div>' +
      '</div>' +
      '<span class="transaction-amount credit">+' + formatRupiah(selectedTopUpNominal) + '</span>';
    historyList.prepend(li);
  }

  closeTopUpModal();
}

var topUpModalEl = document.getElementById('topup-modal');
if (topUpModalEl) {
  topUpModalEl.addEventListener('click', function(e) {
    if (e.target === topUpModalEl) closeTopUpModal();
  });
}

// ── Penyuruh: Service Selection & Order Form ──
function selectService(tileEl) {
  document.querySelectorAll('.service-tile').forEach(function(t) {
    t.classList.remove('selected');
  });
  tileEl.classList.add('selected');

  var serviceName = tileEl.dataset.service;
  var select = document.getElementById('order-service');
  if (select) {
    select.value = serviceName;
  }
  updateOrderTotal();

  var formCard = document.getElementById('order-form-card');
  if (formCard) {
    formCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
}

function onServiceSelectChange(selectEl) {
  var selectedName = selectEl.value;
  document.querySelectorAll('.service-tile').forEach(function(tile) {
    tile.classList.toggle('selected', tile.dataset.service === selectedName);
  });
  updateOrderTotal();
}

function setPickup(text) {
  var pickupInput = document.getElementById('order-pickup');
  if (pickupInput) {
    pickupInput.value = text;
    pickupInput.style.borderColor = '';
  }
}

function selectPayment(btn) {
  document.querySelectorAll('.payment-option').forEach(function(opt) {
    opt.classList.remove('selected');
  });
  btn.classList.add('selected');
  selectedPaymentMethod = btn.dataset.pay;
}

function getCalculatedOrderTotal() {
  var select = document.getElementById('order-service');
  var voucherSelect = document.getElementById('order-voucher');
  if (!select) return { serviceName: '', total: 0 };

  var opt = select.options[select.selectedIndex];
  var basePrice = parseInt(opt.dataset.price || '15000', 10);
  var discount = voucherSelect ? parseInt(voucherSelect.value || '0', 10) : 0;
  var total = Math.max(0, basePrice - discount);

  return { serviceName: select.value, total: total };
}

function updateOrderTotal() {
  var info = getCalculatedOrderTotal();
  var detailEl = document.getElementById('order-summary-detail');
  var totalEl = document.getElementById('order-total-price');
  if (detailEl) detailEl.textContent = info.serviceName + ' · ' + selectedPaymentMethod;
  if (totalEl) totalEl.textContent = formatRupiah(info.total);
}

function quickReorder(service, price, pickup, dropoff, notes) {
  var select = document.getElementById('order-service');
  if (select) {
    select.value = service;
    onServiceSelectChange(select);
  }
  setPickup(pickup);
  var dropInput = document.getElementById('order-dropoff');
  var notesInput = document.getElementById('order-notes');
  if (dropInput) dropInput.value = dropoff;
  if (notesInput) notesInput.value = notes;

  var formCard = document.getElementById('order-form-card');
  if (formCard) {
    formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

var orderForm = document.getElementById('orderForm');
if (orderForm) {
  orderForm.addEventListener('submit', function(e) {
    e.preventDefault();
    var pickup = document.getElementById('order-pickup').value.trim();
    var dropoff = document.getElementById('order-dropoff').value.trim();
    var notes = document.getElementById('order-notes').value.trim();

    if (!pickup) {
      showFormError('order-pickup', 'Masukkan lokasi jemput atau lokasi tugas.');
      return;
    }
    if (!notes) {
      showFormError('order-notes', 'Tuliskan detail tugas agar pesuruh jelas.');
      return;
    }

    var calc = getCalculatedOrderTotal();
    if (selectedPaymentMethod === 'Saldo Dompet' && customerBalance < calc.total) {
      alert('Saldo Dompet tidak mencukupi. Silakan Isi Saldo terlebih dahulu.');
      openTopUpModal();
      return;
    }

    var submitBtn = document.getElementById('btn-submit-order');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Mencari mitra pesuruh terdekat...';
    }

    setTimeout(function() {
      if (selectedPaymentMethod === 'Saldo Dompet') {
        customerBalance -= calc.total;
        syncBalanceUI();
      }

      renderActiveOrder({
        title: calc.serviceName,
        route: dropoff ? (pickup + ' → ' + dropoff) : pickup,
        mitraName: 'Andi Pratama',
        mitraMeta: '★ 4.95 · Yamaha NMAX L 2310 AB · 3 menit lagi'
      });

      // Add to task list
      var taskList = document.getElementById('penyuruh-task-list');
      if (taskList) {
        var li = document.createElement('li');
        li.className = 'task-item-compact';
        li.dataset.status = 'aktif';
        li.innerHTML =
          '<span class="task-status-badge aktif">Aktif</span>' +
          '<div class="task-compact-info">' +
            '<div class="task-compact-title">' + calc.serviceName + '</div>' +
            '<div class="task-compact-meta">Baru saja · Mitra: Andi Pratama</div>' +
          '</div>' +
          '<span class="task-compact-price">' + formatRupiah(calc.total) + '</span>';
        taskList.prepend(li);
      }

      // Add to wallet history if paid by wallet
      if (selectedPaymentMethod === 'Saldo Dompet') {
        var historyList = document.getElementById('penyuruh-history-list');
        if (historyList) {
          var hLi = document.createElement('li');
          hLi.className = 'transaction-item';
          hLi.innerHTML =
            '<div class="transaction-icon send" aria-hidden="true">🛵</div>' +
            '<div class="transaction-info">' +
              '<div class="transaction-name">' + calc.serviceName + '</div>' +
              '<div class="transaction-date">Baru saja</div>' +
            '</div>' +
            '<span class="transaction-amount debit">-' + formatRupiah(calc.total) + '</span>';
          historyList.prepend(hLi);
        }
      }

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Cari Pesuruh Sekarang';
      }
      orderForm.reset();
      updateOrderTotal();
    }, 650);
  });
}

function renderActiveOrder(order) {
  activeOrderStep = 2;
  var body = document.getElementById('active-order-body');
  if (!body) return;

  body.innerHTML =
    '<div style="margin-bottom: 10px;">' +
      '<div style="font-weight: 700; font-size: 0.9375rem;" id="active-order-title">' + order.title + '</div>' +
      '<div style="font-size: 0.8125rem; color: var(--text-secondary);" id="active-order-route">' + order.route + '</div>' +
    '</div>' +
    '<div class="order-progress" id="active-order-progress">' +
      '<div class="order-progress-step done"><div class="bar"></div>Dipesan</div>' +
      '<div class="order-progress-step done"><div class="bar"></div>Dijemput</div>' +
      '<div class="order-progress-step"><div class="bar"></div>Dikerjakan</div>' +
      '<div class="order-progress-step"><div class="bar"></div>Selesai</div>' +
    '</div>' +
    '<div class="mitra-info-box">' +
      '<div class="mitra-avatar" aria-hidden="true">AP</div>' +
      '<div style="flex: 1;">' +
        '<div style="font-weight: 700; font-size: 0.875rem;">' + order.mitraName + '</div>' +
        '<div style="font-size: 0.75rem; color: var(--text-secondary);">' + order.mitraMeta + '</div>' +
      '</div>' +
      '<button type="button" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.75rem;" onclick="alert(\'Membuka chat dengan mitra...\')">💬 Chat</button>' +
    '</div>' +
    '<div style="display: flex; gap: 8px;">' +
      '<button type="button" class="btn btn-primary" style="flex: 1; justify-content: center; font-size: 0.8125rem;" onclick="advanceOrderStatus()">Simulasi: Lanjut Status →</button>' +
      '<button type="button" class="btn btn-outline" style="font-size: 0.8125rem;" onclick="cancelActiveOrder()">Batalkan</button>' +
    '</div>';

  updateOrderProgressUI();
}

function updateOrderProgressUI() {
  var badge = document.getElementById('active-order-badge');
  var steps = document.querySelectorAll('#active-order-progress .order-progress-step');
  var labels = ['Mencari Mitra', 'Menuju Lokasi', 'Sedang Dikerjakan', 'Tugas Selesai'];

  steps.forEach(function(stepEl, idx) {
    stepEl.classList.toggle('done', idx < activeOrderStep);
  });

  if (badge) {
    badge.textContent = labels[activeOrderStep - 1] || 'Menuju Lokasi';
    badge.className = 'order-status-pill' + (activeOrderStep === 4 ? ' done' : '');
  }
}

function advanceOrderStatus() {
  if (activeOrderStep < 4) {
    activeOrderStep += 1;
    updateOrderProgressUI();
  } else {
    alert('Pesanan sudah selesai! Terima kasih sudah memakai PenSuruh.');
  }
}

function cancelActiveOrder() {
  var body = document.getElementById('active-order-body');
  var badge = document.getElementById('active-order-badge');
  if (badge) {
    badge.textContent = 'Tidak Ada';
    badge.className = 'order-status-pill searching';
  }
  if (body) {
    body.innerHTML =
      '<div style="text-align: center; padding: 20px 8px; color: var(--text-secondary);">' +
        '<p style="font-size: 0.875rem; margin-bottom: 12px;">Belum ada pesanan aktif saat ini.</p>' +
        '<button type="button" class="btn btn-primary" onclick="document.getElementById(\'order-pickup\').focus()">Buat Pesanan Baru</button>' +
      '</div>';
  }
}

function filterCustomerTasks(btn, status) {
  btn.parentElement.querySelectorAll('.filter-tab').forEach(function(t) {
    t.classList.remove('active');
  });
  btn.classList.add('active');

  document.querySelectorAll('#penyuruh-task-list .task-item-compact').forEach(function(item) {
    if (status === 'all') {
      item.style.display = '';
    } else {
      item.style.display = item.dataset.status === status ? '' : 'none';
    }
  });
}

// ── Mitra View: Online Toggle & Accept Order ──
function toggleMitraOnline(btn) {
  var statusText = document.getElementById('mitra-status-text');
  var isOffline = btn.classList.toggle('offline');
  if (isOffline) {
    btn.textContent = 'Aktifkan Mode Terima Order';
    if (statusText) statusText.textContent = '⚪ Offline — Tidak Menerima Order Baru';
  } else {
    btn.textContent = 'Nonaktifkan Mode Terima Order';
    if (statusText) statusText.textContent = '🟢 Online — Siap Menerima Tugas Suruh';
  }
}

function acceptMitraOrder(cardId, title) {
  var card = document.getElementById(cardId);
  if (card) card.remove();

  var activeList = document.getElementById('mitra-active-tasks');
  if (activeList) {
    var li = document.createElement('li');
    li.className = 'task-item-compact';
    li.innerHTML =
      '<span class="task-status-badge aktif">Berjalan</span>' +
      '<div class="task-compact-info">' +
        '<div class="task-compact-title">' + title + '</div>' +
        '<div class="task-compact-meta">Baru saja diambil · Segera menuju lokasi</div>' +
      '</div>' +
      '<span class="task-compact-price">Aktif</span>';
    activeList.prepend(li);
  }
}

// ── Admin: Filter Tabs ──
document.querySelectorAll('.admin-filter-tab').forEach(function(tab) {
  tab.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelectorAll('.admin-filter-tab').forEach(function(t) {
      t.classList.remove('active');
    });
    this.classList.add('active');

    var filter = this.dataset.filter;
    document.querySelectorAll('.user-table tbody tr').forEach(function(row) {
      if (filter === 'all') {
        row.style.display = '';
      } else {
        row.style.display = row.dataset.role === filter ? '' : 'none';
      }
    });
  });
});

// ── Admin: Search ──
var adminSearch = document.getElementById('admin-search');
if (adminSearch) {
  adminSearch.addEventListener('input', function() {
    var query = this.value.toLowerCase();
    document.querySelectorAll('.user-table tbody tr').forEach(function(row) {
      var text = row.textContent.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  });
}

// ── Admin: Role Change ──
function changeUserRole(selectEl, userId) {
  var newRole = selectEl.value;
  alert('Demo: Mengubah role user ' + userId + ' menjadi ' + newRole);
}

// ── Keyboard: Escape closes modal ──
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeTopUpModal();
  }
});
