<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user saat ini atau user pertama sebagai demo jika belum login
        $user = Auth::user() ?? User::where('role', 'penyuruh')->first() ?? User::first();

        // Support override role via query parameter untuk kemudahan pengujian (?role=penyuruh / ?role=disuruh / ?role=admin)
        $activeRole = $request->query('role', $user->role ?? 'penyuruh');

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
        $user = Auth::user() ?? User::first();

        $request->validate([
            'title'        => 'required|string|max:255',
            'service_type' => 'required|string',
            'price'        => 'required|numeric|min:5000',
        ]);

        Order::create([
            'penyuruh_id'          => $user->id,
            'title'                => $request->title,
            'service_type'         => $request->service_type,
            'pickup_location'      => $request->pickup_location,
            'destination_location' => $request->destination_location,
            'description'          => $request->description,
            'price'                => $request->price,
            'status'               => 'menunggu', // Belum ada yang ambil
        ]);

        return redirect()->back()->with('success', 'Order suruh berhasil dibuat! Menunggu mitra pesuruh mengambil.');
    }

    /**
     * Mitra (Disuruh) mengambil order terbuka.
     */
    public function takeOrder($id)
    {
        $user = Auth::user() ?? User::where('role', 'disuruh')->first() ?? User::first();
        $order = Order::findOrFail($id);

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
     * Selesaikan order.
     */
    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);
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
            $user = Auth::user() ?? User::first();
            if ($user) {
                $user->update(['role' => $role]);
            }
        }
        return redirect()->route('dashboard', ['role' => $role]);
    }

    /**
     * Admin mengubah role akun pengguna.
     */
    public function updateUserRole(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $request->validate(['role' => 'required|in:penyuruh,disuruh,admin']);
        $targetUser->update(['role' => $request->role]);

        return redirect()->back()->with('success', "Peran untuk {$targetUser->name} berhasil diperbarui menjadi " . ucfirst($request->role) . ".");
    }
}
