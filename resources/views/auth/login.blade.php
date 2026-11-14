<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PenSuruh — Masuk</title>
    <link rel="icon" type="image/png" href="/images/sources/hero_courier_clean.jpg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .auth-gradient {
            background: linear-gradient(135deg, #0471fc 0%, #0285fe 30%, #38b6ff 65%, #0471fc 100%);
        }
        .orb-glow {
            background: radial-gradient(circle, rgba(255,255,255,0.35) 0%, rgba(56,182,255,0.1) 60%, transparent 100%);
        }
        .orb-glow-sm {
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        }
        .step-card-active { background: rgba(255,255,255,1); }
        .step-card-inactive { background: rgba(255,255,255,0.15); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.25); }
        input:focus { outline: none; }
        .input-field {
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .input-field:focus {
            border-color: #0471fc;
            box-shadow: 0 0 0 3px rgba(4, 113, 252, 0.12);
        }
        .tab-active {
            color: #0471fc;
            border-bottom: 2px solid #0471fc;
        }
        .tab-inactive {
            color: #9ca3af;
            border-bottom: 2px solid transparent;
        }
        .pw-toggle { cursor: pointer; }
    </style>
</head>
<body class="min-h-screen bg-[#f0f4ff] flex items-center justify-center p-3 sm:p-6 antialiased">

    <!-- Outer Card Container -->
    <div class="w-full max-w-[960px] bg-white rounded-[24px] sm:rounded-[32px] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px] md:min-h-[640px]">

        <!-- ========== LEFT PANEL: Gradient Blue ========== -->
        <div class="auth-gradient relative flex flex-col justify-between p-6 sm:p-8 md:p-10 w-full md:w-[46%] flex-shrink-0 overflow-hidden min-h-[260px] md:min-h-0">

            <!-- Ambient Orb Glows (Dekoratif seperti referensi) -->
            <div class="absolute top-[-80px] left-[-60px] w-[340px] h-[340px] orb-glow rounded-full pointer-events-none"></div>
            <div class="absolute bottom-[60px] right-[-80px] w-[260px] h-[260px] orb-glow-sm rounded-full pointer-events-none"></div>

            <!-- Logo & Brand -->
            <div class="relative z-10">
                <a href="/" class="inline-flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-white/20 border border-white/30 flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <span class="text-white font-black text-xl tracking-tight">
                        Pen<span class="text-[#f3c903]">Suruh</span>
                    </span>
                </a>
            </div>

            <!-- Center Content -->
            <div class="relative z-10 flex flex-col gap-3 mt-auto md:mt-0 pb-2 md:pb-6">
                <h2 class="text-white font-black text-[28px] sm:text-[32px] md:text-[36px] leading-tight">
                    Jasa Suruh,<br>Kapan Saja.
                </h2>
                <p class="text-white/70 text-[13px] leading-relaxed max-w-[260px]">
                    Daftarkan akun dan langsung cari pesuruh terpercaya di sekitarmu.
                </p>
            </div>

            <!-- Bottom: Step Cards -->
            <div class="relative z-10 grid grid-cols-3 gap-2 mt-4 md:mt-6">
                <!-- Step 1: Active -->
                <div class="step-card-active rounded-[14px] p-3 flex flex-col gap-2 shadow-lg">
                    <span class="w-6 h-6 rounded-full bg-[#0471fc] text-white text-xs font-black flex items-center justify-center flex-shrink-0">1</span>
                    <span class="text-gray-900 font-bold text-[11px] leading-tight">Daftar<br>akun</span>
                </div>
                <!-- Step 2: Inactive -->
                <div class="step-card-inactive rounded-[14px] p-3 flex flex-col gap-2">
                    <span class="w-6 h-6 rounded-full bg-white/25 text-white text-xs font-black flex items-center justify-center flex-shrink-0">2</span>
                    <span class="text-white/80 font-semibold text-[11px] leading-tight">Pilih<br>pesuruh</span>
                </div>
                <!-- Step 3: Inactive -->
                <div class="step-card-inactive rounded-[14px] p-3 flex flex-col gap-2">
                    <span class="w-6 h-6 rounded-full bg-white/25 text-white text-xs font-black flex items-center justify-center flex-shrink-0">3</span>
                    <span class="text-white/80 font-semibold text-[11px] leading-tight">Tugas<br>beres</span>
                </div>
            </div>
        </div>

        <!-- ========== RIGHT PANEL: Form ========== -->
        <div class="flex-grow flex flex-col justify-center px-6 sm:px-10 md:px-12 py-8 md:py-10">

            <!-- Tab Switcher: Masuk / Daftar -->
            <div class="flex gap-6 border-b border-gray-100 mb-7">
                <button id="tabLoginBtn" onclick="switchTab('login')" class="pb-2.5 text-sm font-bold transition-all tab-active">
                    Masuk
                </button>
                <button id="tabRegisterBtn" onclick="switchTab('register')" class="pb-2.5 text-sm font-bold transition-all tab-inactive">
                    Daftar Baru
                </button>
            </div>

            @if(session('error'))
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-start gap-2.5">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl flex items-start gap-2.5">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- ---- LOGIN FORM ---- -->
            <div id="loginFormPanel">
                <h1 class="text-[24px] sm:text-[28px] font-black text-gray-900 mb-1">Masuk</h1>
                <p class="text-gray-400 text-sm mb-7">Lanjutkan ke akun PenSuruh kamu.</p>

                <form onsubmit="handleLogin(event)" class="flex flex-col gap-4">
                    <!-- WhatsApp / Email -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Nomor WhatsApp / Email</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input
                                type="text"
                                required
                                placeholder="0812xxxx atau email@domain.com"
                                class="input-field w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-gray-600">Password</label>
                            <a href="#" class="text-xs text-[#0471fc] font-semibold hover:underline">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                id="loginPassword"
                                type="password"
                                required
                                placeholder="••••••••"
                                class="input-field w-full pl-10 pr-11 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400"
                            >
                            <button type="button" class="pw-toggle absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword('loginPassword', this)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 accent-[#0471fc]">
                        <span class="text-xs text-gray-600 font-medium">Ingat saya</span>
                    </label>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-[#0471fc] hover:bg-[#0060db] active:scale-[0.98] text-white font-bold py-3.5 rounded-xl text-sm shadow-md transition-all mt-1 cursor-pointer">
                        Masuk
                    </button>
                </form>

                <!-- Divider -->
                <div class="flex items-center gap-3 my-5">
                    <div class="flex-grow h-px bg-gray-100"></div>
                    <span class="text-xs text-gray-400 font-medium">atau</span>
                    <div class="flex-grow h-px bg-gray-100"></div>
                </div>

                <!-- Google Login -->
                <a href="/auth/google" class="w-full border border-gray-200 hover:border-gray-300 hover:bg-gray-50 active:scale-[0.98] text-gray-700 font-semibold py-3 rounded-xl text-sm flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                    <svg class="w-4.5 h-4.5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Masuk dengan Google
                </a>

                <p class="text-center text-xs text-gray-500 mt-5">
                    Belum punya akun?
                    <button onclick="switchTab('register')" class="text-[#0471fc] font-bold hover:underline cursor-pointer">Daftar di sini</button>
                </p>
            </div>

            <!-- ---- REGISTER FORM ---- -->
            <div id="registerFormPanel" class="hidden">
                <h1 class="text-[24px] sm:text-[28px] font-black text-gray-900 mb-1">Buat Akun</h1>
                <p class="text-gray-400 text-sm mb-6">Daftar gratis dan langsung cari pesuruh.</p>

                <!-- Role Switcher -->
                <div class="flex bg-gray-100 p-1 rounded-full mb-5 text-xs font-bold">
                    <button id="roleCustomerBtn" type="button" onclick="setRole('customer')" class="flex-1 py-2 rounded-full bg-[#0471fc] text-white transition-all cursor-pointer">Pengguna Jasa</button>
                    <button id="roleMitraBtn" type="button" onclick="setRole('mitra')" class="flex-1 py-2 rounded-full text-gray-500 transition-all cursor-pointer">Mitra Pesuruh</button>
                </div>

                <form onsubmit="handleRegister(event)" class="flex flex-col gap-3.5">
                    <!-- Full Name + Username row -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Nama Lengkap</label>
                            <input type="text" required placeholder="Nama sesuai KTP" class="input-field w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1.5">Username</label>
                            <div class="relative">
                                <input type="text" required placeholder="pensuruh_user" class="input-field w-full px-3.5 pr-9 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Nomor WhatsApp Aktif</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 select-none">+62</span>
                            <input type="tel" required placeholder="812 xxxx xxxx" class="input-field w-full pl-12 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Buat Password</label>
                        <div class="relative">
                            <input id="regPassword" type="password" required placeholder="Min. 8 karakter" class="input-field w-full px-3.5 pr-11 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-900 placeholder-gray-400">
                            <button type="button" class="pw-toggle absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword('regPassword', this)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Min. 8 karakter. Kombinasi huruf, angka & simbol lebih aman.</p>
                    </div>

                    <!-- Mitra Skills (hidden by default) -->
                    <div id="mitraSkillsReg" class="hidden bg-blue-50/60 border border-blue-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Layanan yang Ingin Ditawarkan</label>
                        <div class="grid grid-cols-2 gap-1.5 text-xs font-medium text-gray-700">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="accent-[#0471fc]"> Antar Barang</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" checked class="accent-[#0471fc]"> Bersih-bersih</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="accent-[#0471fc]"> Rawat Tanaman</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="accent-[#0471fc]"> Teman Ngobrol</label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full bg-[#0471fc] hover:bg-[#0060db] active:scale-[0.98] text-white font-bold py-3.5 rounded-xl text-sm shadow-md transition-all cursor-pointer">
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Divider -->
                <div class="flex items-center gap-3 my-4">
                    <div class="flex-grow h-px bg-gray-100"></div>
                    <span class="text-xs text-gray-400 font-medium">atau</span>
                    <div class="flex-grow h-px bg-gray-100"></div>
                </div>

                <!-- Google Register -->
                <a href="/auth/google" class="w-full border border-gray-200 hover:border-gray-300 hover:bg-gray-50 active:scale-[0.98] text-gray-700 font-semibold py-3 rounded-xl text-sm flex items-center justify-center gap-2.5 transition-all cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Daftar dengan Google
                </a>

                <p class="text-center text-xs text-gray-400 mt-4 leading-relaxed">
                    Dengan mendaftar, kamu setuju dengan
                    <a href="#" class="text-[#0471fc] font-semibold hover:underline">Kebijakan Privasi</a>
                    &amp;
                    <a href="#" class="text-[#0471fc] font-semibold hover:underline">Syarat Layanan</a> PenSuruh.
                </p>

                <p class="text-center text-xs text-gray-500 mt-3">
                    Sudah punya akun?
                    <button onclick="switchTab('login')" class="text-[#0471fc] font-bold hover:underline cursor-pointer">Masuk di sini</button>
                </p>
            </div>

        </div>
        <!-- End Right Panel -->
    </div>

    <!-- Toast Notification -->
    <div id="toastAuth" class="fixed top-5 right-5 z-[100] transform -translate-y-24 opacity-0 transition-all duration-300 ease-out max-w-xs w-full pointer-events-none">
        <div class="bg-white rounded-2xl p-4 shadow-2xl border border-gray-100 flex items-start gap-3 pointer-events-auto">
            <div id="toastAuthIcon" class="w-8 h-8 rounded-full bg-blue-50 text-[#0471fc] flex items-center justify-center flex-shrink-0 font-bold text-sm">✓</div>
            <div class="flex-grow">
                <h5 id="toastAuthTitle" class="text-sm font-bold text-gray-900 leading-tight">Sukses</h5>
                <p id="toastAuthMsg" class="text-xs text-gray-500 mt-0.5">Operasi berhasil.</p>
            </div>
            <button onclick="hideAuthToast()" class="text-gray-300 hover:text-gray-600 font-bold text-sm cursor-pointer">✕</button>
        </div>
    </div>

    <script>
        // Tab Switcher
        function switchTab(tab) {
            const loginPanel = document.getElementById('loginFormPanel');
            const registerPanel = document.getElementById('registerFormPanel');
            const loginBtn = document.getElementById('tabLoginBtn');
            const registerBtn = document.getElementById('tabRegisterBtn');

            if (tab === 'login') {
                loginPanel.classList.remove('hidden');
                registerPanel.classList.add('hidden');
                loginBtn.className = 'pb-2.5 text-sm font-bold transition-all tab-active';
                registerBtn.className = 'pb-2.5 text-sm font-bold transition-all tab-inactive';
            } else {
                loginPanel.classList.add('hidden');
                registerPanel.classList.remove('hidden');
                loginBtn.className = 'pb-2.5 text-sm font-bold transition-all tab-inactive';
                registerBtn.className = 'pb-2.5 text-sm font-bold transition-all tab-active';
            }
        }

        // Role Switcher (Register)
        function setRole(role) {
            const customerBtn = document.getElementById('roleCustomerBtn');
            const mitraBtn = document.getElementById('roleMitraBtn');
            const mitraSkills = document.getElementById('mitraSkillsReg');

            if (role === 'customer') {
                customerBtn.className = 'flex-1 py-2 rounded-full bg-[#0471fc] text-white transition-all cursor-pointer';
                mitraBtn.className = 'flex-1 py-2 rounded-full text-gray-500 transition-all cursor-pointer';
                mitraSkills.classList.add('hidden');
            } else {
                mitraBtn.className = 'flex-1 py-2 rounded-full bg-[#0471fc] text-white transition-all cursor-pointer';
                customerBtn.className = 'flex-1 py-2 rounded-full text-gray-500 transition-all cursor-pointer';
                mitraSkills.classList.remove('hidden');
            }
        }

        // Password Toggle
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
                ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>`
                : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
        }

        // Toast
        let toastTimer = null;
        function showAuthToast(title, msg, success = true) {
            const toast = document.getElementById('toastAuth');
            document.getElementById('toastAuthTitle').innerText = title;
            document.getElementById('toastAuthMsg').innerText = msg;
            document.getElementById('toastAuthIcon').innerText = success ? '✓' : '!';
            toast.classList.remove('-translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            if (toastTimer) clearTimeout(toastTimer);
            toastTimer = setTimeout(hideAuthToast, 4000);
        }
        function hideAuthToast() {
            const toast = document.getElementById('toastAuth');
            toast.classList.add('-translate-y-24', 'opacity-0');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }

        // Form Handlers
        function handleLogin(e) {
            e.preventDefault();
            showAuthToast('Berhasil masuk', 'Mengalihkan ke dashboard...');
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 600);
        }
        function handleRegister(e) {
            e.preventDefault();
            showAuthToast('Akun dibuat', 'Mengalihkan ke dashboard...');
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 600);
        }
    </script>
</body>
</html>

