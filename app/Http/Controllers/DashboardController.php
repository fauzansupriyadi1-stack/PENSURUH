<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
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
            ->latest()
            ->get();

        // Statistik Admin
        $adminStats = [
            'total_users'     => $allUsers->count(),
            'total_penyuruh'  => $allUsers->where('role', 'penyuruh')->count(),
            'total_disuruh'   => $allUsers->where('role', 'disuruh')->count(),
            'total_orders'    => $allOrders->count(),
            'completed_count' => $completedOrders->count(),
            'total_turnover'  => $completedOrders->sum('price'),
        ];

        return view('dashboard', compact(
            'user', 
            'activeRole', 
            'myOrders', 
            'openOrders', 
            'myTakenOrders', 
            'allOrders',
            'completedOrders',
            'allUsers',
            'adminStats'
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

        $minPrices = [
            'Antar Paket & Dokumen' => 15000,
            'Beli Makan & Minum'    => 12000,
            'Titip Belanja & Antre' => 18000,
            'Bersih Rumah & Kost'   => 45000,
            'Jemput / Antar Orang'  => 14000,
        ];

        $serviceMin = $minPrices[$request->service_type] ?? 5000;

        $request->validate([
            'title'            => 'required|string|max:255',
            'service_type'     => 'required|string|max:255',
            'pickup_lat'       => 'nullable|numeric|between:-90,90',
            'pickup_lng'       => 'nullable|numeric|between:-180,180',
            'offer_price'      => 'nullable|numeric|min:' . $serviceMin,
            'voucher_discount' => 'nullable|numeric|in:0,5000,10000',
            'price'            => 'required|numeric|min:5000',
        ], [
            'offer_price.min' => 'Ongkos minimal untuk layanan ' . $request->service_type . ' adalah Rp ' . number_format($serviceMin, 0, ',', '.') . '.',
        ]);

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

        if (Schema::hasColumn('orders', 'pickup_lat') && Schema::hasColumn('orders', 'pickup_lng')) {
            $orderData['pickup_lat'] = $request->input('pickup_lat');
            $orderData['pickup_lng'] = $request->input('pickup_lng');
        }

        Order::create($orderData);

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
     * Mitra (Disuruh) mengambil order terbuka menggunakan slug.
     */
    public function takeOrder($slug)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $order = $this->findBySlugOrId(Order::class, (string) $slug);
        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        if ($order->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Order ini sudah diambil oleh mitra lain.');
        }

        $order->update([
            'disuruh_id' => $user->id,
            'status'     => 'diambil',
            'taken_at'   => now(),
        ]);

        return redirect()->back()->with('success', 'Berhasil mengambil order! Silakan kerjakan tugas.');
    }

    /**
     * Selesaikan order menggunakan slug.
     */
    public function completeOrder($slug)
    {
        $order = $this->findBySlugOrId(Order::class, (string) $slug);
        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan.');
        }

        $order->update([
            'status'       => 'selesai',
            'completed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Tugas suruh telah diselesaikan.');
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
}
