<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_suspended) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda telah ditangguhkan (suspend) oleh admin.');
        }

        // Jika bukan admin, pastikan role yang aktif sesuai role akun yang login
        if ($user->role === 'admin') {
            $activeRole = $request->query('role', 'admin');
        } else {
            $activeRole = $user->role ?? 'penyuruh';
        }

        // 1. Data untuk Role PENYURUH (Hanya melihat order miliknya sendiri: apakah belum/sudah ada yang ambil)
        $myOrders = Order::with('disuruh')
            ->where('penyuruh_id', $user->id)
            ->latest()
            ->get();

        // 2. Data untuk Role DISURUH (Mitra) & ADMIN:
        // Order terbuka yang BELUM diambil siapapun (status = 'menunggu')
        $openOrders = Order::with('penyuruh')
            ->where('status', 'menunggu')
            ->latest()
            ->get();

        // Order yang sedang dikerjakan oleh mitra saat ini
        $myTakenOrders = Order::with('penyuruh')
            ->where('disuruh_id', $user->id)
            ->where('status', 'diambil')
            ->latest()
            ->get();

        // 3. Data untuk Role ADMIN:
        // Semua order
        $allOrders = Order::with(['penyuruh', 'disuruh'])
            ->latest()
            ->get();

        // Semua order yang SUDAH SELESAI (untuk tab Riwayat di role Admin)
        $completedOrders = Order::with(['penyuruh', 'disuruh'])
            ->where('status', 'selesai')
            ->latest('completed_at')
            ->get();

        // Seluruh akun pengguna untuk kontrol penuh Admin di tab Ringkasan
        $allUsers = User::withCount(['ordersCreated', 'ordersTaken'])
            ->with(['ordersCreated.disuruh', 'ordersTaken.penyuruh'])
            ->latest()
            ->get();

        // Data detail user (saldo/pendapatan/pengeluaran & history order) untuk modal detail Admin
        $userDetailsMap = [];
        foreach ($allUsers as $u) {
            $created = $u->ordersCreated ?? collect();
            $taken = $u->ordersTaken ?? collect();

            $spentCompleted = $created->where('status', 'selesai')->sum('price');
            $earnedCompleted = $taken->where('status', 'selesai')->sum('price');

            $historyItems = collect();
            foreach ($created as $ord) {
                $historyItems->push([
                    'id'                   => $ord->id,
                    'context'              => 'Penyuruh',
                    'title'                => $ord->title ?? $ord->service_type,
                    'service_type'         => $ord->service_type,
                    'status'               => $ord->status,
                    'price'                => (int) ($ord->price ?? 0),
                    'pickup_location'      => $ord->pickup_location ?? '-',
                    'destination_location' => $ord->destination_location ?? '-',
                    'counterpart'          => $ord->disuruh->name ?? 'Belum ada mitra',
                    'date'                 => $ord->created_at ? $ord->created_at->format('d M Y, H:i') : '-',
                    'timestamp'            => $ord->created_at ? $ord->created_at->timestamp : 0,
                ]);
            }
            foreach ($taken as $ord) {
                $historyItems->push([
                    'id'                   => $ord->id,
                    'context'              => 'Mitra',
                    'title'                => $ord->title ?? $ord->service_type,
                    'service_type'         => $ord->service_type,
                    'status'               => $ord->status,
                    'price'                => (int) ($ord->price ?? 0),
                    'pickup_location'      => $ord->pickup_location ?? '-',
                    'destination_location' => $ord->destination_location ?? '-',
                    'counterpart'          => $ord->penyuruh->name ?? '-',
                    'date'                 => $ord->created_at ? $ord->created_at->format('d M Y, H:i') : '-',
                    'timestamp'            => $ord->created_at ? $ord->created_at->timestamp : 0,
                ]);
            }

            $allUserOrders = $created->merge($taken);
            $estimatedBalance = (float) ($u->balance ?? 0);

            $userDetailsMap[$u->id] = [
                'id'                => $u->id,
                'name'              => $u->name,
                'email'             => $u->email ?? '-',
                'phone'             => $u->phone ?? 'Belum ada No. HP',
                'role'              => $u->role,
                'is_suspended'      => (bool) $u->is_suspended,
                'joined_at'         => $u->created_at ? $u->created_at->format('d M Y') : '-',
                'estimated_balance' => $estimatedBalance,
                'total_spent'       => (int) $spentCompleted,
                'total_earned'      => (int) $earnedCompleted,
                'total_created'     => $created->count(),
                'total_taken'       => $taken->count(),
                'active_count'      => $allUserOrders->whereIn('status', ['menunggu', 'diambil'])->count(),
                'completed_count'   => $allUserOrders->where('status', 'selesai')->count(),
                'cancelled_count'   => $allUserOrders->where('status', 'dibatalkan')->count(),
                'history'           => $historyItems->sortByDesc('timestamp')->values()->all(),
            ];
        }

        // Statistik Admin
        $adminStats = [
            'total_users'     => $allUsers->count(),
            'total_penyuruh'  => $allUsers->where('role', 'penyuruh')->count(),
            'total_disuruh'   => $allUsers->where('role', 'disuruh')->count(),
            'total_orders'    => $allOrders->count(),
            'completed_count' => $completedOrders->count(),
            'total_turnover'  => $completedOrders->sum('price'),
        ];

        $myWalletTransactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard', compact(
            'user', 
            'activeRole', 
            'myOrders', 
            'openOrders', 
            'myTakenOrders', 
            'allOrders',
            'completedOrders',
            'allUsers',
            'userDetailsMap',
            'adminStats',
            'myWalletTransactions'
        ));
    }

    /**
     * Penyuruh membuat order baru.
     */
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_suspended) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda telah ditangguhkan (suspend) oleh admin.');
        }

        $minPrices = [
            'Antar Paket & Dokumen' => 15000,
            'Beli Makan & Minum'    => 12000,
            'Titip Belanja & Antre' => 18000,
            'Bersih Rumah & Kost'   => 45000,
            'Jemput / Antar Orang'  => 14000,
        ];

        $serviceMin = $minPrices[$request->service_type] ?? 5000;

        $request->validate([
            'idempotency_key'  => 'nullable|string|max:64',
            'title'            => 'required|string|max:255',
            'service_type'     => 'required|string|max:255',
            'pickup_lat'       => 'nullable|numeric|between:-90,90',
            'pickup_lng'       => 'nullable|numeric|between:-180,180',
            'destination_lat'  => 'nullable|numeric|between:-90,90',
            'destination_lng'  => 'nullable|numeric|between:-180,180',
            'offer_price'      => 'nullable|numeric|min:' . $serviceMin,
            'voucher_discount' => 'nullable|numeric|in:0,5000,10000',
            'price'            => 'required|numeric|min:5000',
        ], [
            'offer_price.min' => 'Ongkos minimal untuk layanan ' . $request->service_type . ' adalah Rp ' . number_format($serviceMin, 0, ',', '.') . '.',
        ]);

        $idempotencyKey = trim((string) $request->input('idempotency_key', ''));
        $hasIdempotencyCol = Schema::hasColumn('orders', 'idempotency_key');

        if ($hasIdempotencyCol && $idempotencyKey !== '') {
            $existing = Order::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return redirect()->back()->with('success', 'Order suruh sudah tercatat sebelumnya (permintaan ganda dicegah).');
            }
        }

        $offerPrice = $request->filled('offer_price')
            ? (int) $request->offer_price
            : max($serviceMin, (int) $request->price);
        $voucherDiscount = (int) ($request->input('voucher_discount', 0));
        $finalPrice = max(5000, $offerPrice - $voucherDiscount);

        $orderData = [
            'penyuruh_id'          => $user->id,
            'title'                => $request->title,
            'service_type'         => $request->service_type,
            'pickup_location'      => $request->pickup_location,
            'destination_location' => $request->destination_location,
            'description'          => $request->description,
            'price'                => $finalPrice,
            'status'               => 'menunggu', // Belum ada yang ambil
        ];

        if ($hasIdempotencyCol && $idempotencyKey !== '') {
            $orderData['idempotency_key'] = $idempotencyKey;
        }

        if (Schema::hasColumn('orders', 'pickup_lat') && Schema::hasColumn('orders', 'pickup_lng')) {
            $orderData['pickup_lat'] = $request->input('pickup_lat');
            $orderData['pickup_lng'] = $request->input('pickup_lng');
        }

        if (Schema::hasColumn('orders', 'destination_lat') && Schema::hasColumn('orders', 'destination_lng')) {
            $orderData['destination_lat'] = $request->input('destination_lat');
            $orderData['destination_lng'] = $request->input('destination_lng');
        }

        try {
            $txResult = DB::transaction(function () use ($orderData, $hasIdempotencyCol, $idempotencyKey, $user) {
                if ($hasIdempotencyCol && $idempotencyKey !== '' && Order::where('idempotency_key', $idempotencyKey)->lockForUpdate()->exists()) {
                    return ['status' => 'duplicate'];
                }

                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                $price = (float) $orderData['price'];
                if (!$lockedUser || (float) $lockedUser->balance < $price) {
                    return [
                        'status'  => 'insufficient',
                        'balance' => (float) ($lockedUser->balance ?? 0),
                        'price'   => $price,
                    ];
                }

                $lockedUser->decrement('balance', $price);
                $order = Order::create($orderData);

                WalletTransaction::create([
                    'user_id'        => $lockedUser->id,
                    'type'           => 'ORDER_PAYMENT',
                    'invoice_number' => 'ORD-' . $order->id . '-' . strtoupper(Str::random(4)),
                    'amount'         => $price,
                    'payment_method' => 'Dompet PenSuruh',
                    'description'    => 'Pembayaran tugas: ' . ($order->title ?? $order->service_type),
                    'status'         => 'SUCCESS',
                    'paid_at'        => now(),
                ]);

                return ['status' => 'created'];
            });

            if (($txResult['status'] ?? '') === 'insufficient') {
                return redirect()->back()->with('error', 'Saldo dompet tidak mencukupi (Saldo: Rp ' . number_format($txResult['balance'], 0, ',', '.') . ', Biaya: Rp ' . number_format($txResult['price'], 0, ',', '.') . '). Silakan isi saldo via QRIS terlebih dahulu.');
            }
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->back()->with('success', 'Order suruh sudah tercatat sebelumnya (permintaan ganda dicegah).');
        }

        return redirect()->back()->with('success', 'Order suruh berhasil dibuat! Menunggu mitra pesuruh mengambil.');
    }

    /**
     * Cari record berdasarkan kolom slug (jika ada), ID numerik, atau akhiran -{id}.
     */
    private function findBySlugOrId(string $modelClass, string $slug)
    {
        $table = (new $modelClass)->getTable();
        if (Schema::hasColumn($table, 'slug')) {
            $record = $modelClass::where('slug', $slug)->first();
            if ($record) {
                return $record;
            }
        }

        if (ctype_digit($slug)) {
            return $modelClass::find((int) $slug);
        }

        if (preg_match('/-(\d+)$/', $slug, $m)) {
            return $modelClass::find((int) $m[1]);
        }

        return null;
    }

    /**
     * Mitra (Disuruh) mengambil order terbuka secara atomik & idempotent.
     */
    public function takeOrder($slug)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_suspended) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda telah ditangguhkan (suspend) oleh admin.');
        }

        $target = $this->findBySlugOrId(Order::class, (string) $slug);
        if (!$target) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        $result = DB::transaction(function () use ($target, $user) {
            $order = Order::where('id', $target->id)->lockForUpdate()->first();
            if (!$order) {
                return ['type' => 'error', 'msg' => 'Order tidak ditemukan.'];
            }
            if ($order->status === 'diambil' && (int) $order->disuruh_id === (int) $user->id) {
                return ['type' => 'success', 'msg' => 'Order ini sudah kamu ambil sebelumnya.'];
            }
            if ($order->status !== 'menunggu') {
                return ['type' => 'error', 'msg' => 'Order ini sudah diambil oleh mitra lain.'];
            }

            $order->update([
                'disuruh_id' => $user->id,
                'status'     => 'diambil',
                'taken_at'   => now(),
            ]);

            return ['type' => 'success', 'msg' => 'Berhasil mengambil order! Silakan kerjakan tugas.'];
        });

        return redirect()->back()->with($result['type'], $result['msg']);
    }

    /**
     * Selesaikan order secara idempotent.
     */
    public function completeOrder($slug)
    {
        $target = $this->findBySlugOrId(Order::class, (string) $slug);
        if (!$target) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        $result = DB::transaction(function () use ($target) {
            $order = Order::where('id', $target->id)->lockForUpdate()->first();
            if (!$order) {
                return ['type' => 'error', 'msg' => 'Order tidak ditemukan.'];
            }
            if ($order->status === 'dibatalkan') {
                return ['type' => 'error', 'msg' => 'Order yang sudah dibatalkan tidak dapat diselesaikan.'];
            }
            if ($order->status === 'selesai') {
                return ['type' => 'success', 'msg' => 'Tugas suruh ini sudah diselesaikan sebelumnya.'];
            }

            $user = Auth::user();
            $isAdmin = $user && $user->role === 'admin';
            $isPenyuruh = $user && (int) $order->penyuruh_id === (int) $user->id;

            if (!$isAdmin && !$isPenyuruh) {
                return ['type' => 'error', 'msg' => 'Hanya Pemesan (Penyuruh) atau Admin yang berhak menyelesaikan tugas.'];
            }

            $order->update([
                'status'       => 'selesai',
                'completed_at' => now(),
            ]);

            if ($order->disuruh_id && (float) $order->price > 0) {
                $mitra = User::where('id', $order->disuruh_id)->lockForUpdate()->first();
                if ($mitra) {
                    $mitra->increment('balance', (float) $order->price);
                    WalletTransaction::create([
                        'user_id'        => $mitra->id,
                        'type'           => 'ORDER_EARNING',
                        'invoice_number' => 'EARN-' . $order->id . '-' . strtoupper(Str::random(4)),
                        'amount'         => (float) $order->price,
                        'payment_method' => 'Dompet PenSuruh',
                        'description'    => 'Upah tugas selesai: ' . ($order->title ?? $order->service_type),
                        'status'         => 'SUCCESS',
                        'paid_at'        => now(),
                    ]);
                }
            }

            return ['type' => 'success', 'msg' => 'Tugas suruh telah diselesaikan dan saldo telah diteruskan ke Mitra.'];
        });

        return redirect()->back()->with($result['type'], $result['msg']);
    }

    /**
     * Batalkan order:
     * - Penyuruh hanya boleh membatalkan saat status masih 'menunggu' (belum diambil mitra).
     * - Jika sudah 'diambil' oleh mitra, hanya Admin yang dapat membatalkan.
     */
    public function cancelOrder($slug)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->is_suspended) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda telah ditangguhkan (suspend) oleh admin.');
        }

        $target = $this->findBySlugOrId(Order::class, (string) $slug);
        if (!$target) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        $result = DB::transaction(function () use ($target, $user) {
            $order = Order::where('id', $target->id)->lockForUpdate()->first();
            if (!$order) {
                return ['type' => 'error', 'msg' => 'Order tidak ditemukan.'];
            }
            if ($order->status === 'dibatalkan') {
                return ['type' => 'success', 'msg' => 'Pesanan ini sudah dibatalkan sebelumnya.'];
            }
            if ($order->status === 'selesai') {
                return ['type' => 'error', 'msg' => 'Pesanan yang sudah selesai tidak dapat dibatalkan.'];
            }

            $isAdmin = $user->role === 'admin';
            $isOwner = (int) $order->penyuruh_id === (int) $user->id;

            if ($order->status === 'diambil' && !$isAdmin) {
                return ['type' => 'error', 'msg' => 'Pesanan sudah diambil oleh mitra. Hanya Admin yang dapat membatalkan pesanan ini.'];
            }

            if ($order->status === 'menunggu' && !$isOwner && !$isAdmin) {
                return ['type' => 'error', 'msg' => 'Anda tidak memiliki izin untuk membatalkan pesanan ini.'];
            }

            $order->update([
                'status' => 'dibatalkan',
            ]);

            if ($order->penyuruh_id && (float) $order->price > 0) {
                $penyuruh = User::where('id', $order->penyuruh_id)->lockForUpdate()->first();
                if ($penyuruh) {
                    $penyuruh->increment('balance', (float) $order->price);
                    WalletTransaction::create([
                        'user_id'        => $penyuruh->id,
                        'type'           => 'REFUND',
                        'invoice_number' => 'RFD-' . $order->id . '-' . strtoupper(Str::random(4)),
                        'amount'         => (float) $order->price,
                        'payment_method' => 'Dompet PenSuruh',
                        'description'    => 'Pengembalian dana pesanan dibatalkan: ' . ($order->title ?? $order->service_type),
                        'status'         => 'SUCCESS',
                        'paid_at'        => now(),
                    ]);
                }
            }

            return ['type' => 'success', 'msg' => 'Pesanan berhasil dibatalkan dan dana telah dikembalikan ke saldo Penyuruh.'];
        });

        return redirect()->back()->with($result['type'], $result['msg']);
    }

    /**
     * Ganti role user aktif (Helper testing).
     */
    public function switchRole($role)
    {
        if (in_array($role, ['penyuruh', 'disuruh', 'admin'])) {
            $user = Auth::user();
            if ($user) {
                $user->update(['role' => $role]);
            }
        }
        return redirect()->route('dashboard', ['role' => $role]);
    }

    /**
     * Admin mengubah role akun pengguna menggunakan slug.
     */
    public function updateUserRole(Request $request, $slug)
    {
        $targetUser = $this->findBySlugOrId(User::class, (string) $slug);
        if (!$targetUser) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $request->validate(['role' => 'required|in:penyuruh,disuruh,admin']);
        $targetUser->update(['role' => $request->role]);

        return redirect()->back()->with('success', "Peran untuk {$targetUser->name} berhasil diperbarui menjadi " . ucfirst($request->role) . ".");
    }

    /**
     * Admin melakukan suspend atau mengaktifkan kembali akun pengguna.
     */
    public function toggleSuspendUser(Request $request, $slug)
    {
        $currentUser = Auth::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin yang dapat melakukan suspend akun.');
        }

        $targetUser = $this->findBySlugOrId(User::class, (string) $slug);
        if (!$targetUser) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        if ((int) $targetUser->id === (int) $currentUser->id) {
            return redirect()->back()->with('error', 'Admin tidak dapat melakukan suspend pada akun sendiri.');
        }

        $newStatus = !$targetUser->is_suspended;
        $targetUser->update([
            'is_suspended' => $newStatus,
            'suspended_at' => $newStatus ? now() : null,
        ]);

        $statusText = $newStatus ? 'ditangguhkan (suspend)' : 'diaktifkan kembali';
        return redirect()->back()->with('success', "Akun {$targetUser->name} berhasil {$statusText}.");
    }
}
