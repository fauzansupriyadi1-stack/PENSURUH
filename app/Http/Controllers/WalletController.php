<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Memulai transaksi Top Up saldo via DOKU QRIS.
     */
    public function initiateTopUp(Request $request, DokuService $dokuService)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000',
        ], [
            'amount.required' => 'Nominal isi saldo wajib ditentukan.',
            'amount.numeric'  => 'Nominal isi saldo harus berupa angka.',
            'amount.min'      => 'Minimal isi saldo adalah Rp 10.000.',
            'amount.max'      => 'Maksimal isi saldo adalah Rp 10.000.000.',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        if ($user->is_suspended) {
            return response()->json(['success' => false, 'message' => 'Akun Anda ditangguhkan. Tidak dapat melakukan top up.'], 403);
        }

        $amount = (float) $request->input('amount');
        $invoiceNumber = 'TOPUP-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

        $transaction = WalletTransaction::create([
            'user_id'        => $user->id,
            'invoice_number' => $invoiceNumber,
            'amount'         => $amount,
            'payment_method' => 'QRIS',
            'status'         => 'PENDING',
        ]);

        // Panggil DOKU Checkout QRIS API
        $dokuResponse = $dokuService->createQrisPayment($invoiceNumber, $amount, $user);

        if ($dokuResponse['success']) {
            $transaction->update([
                'doku_payment_url' => $dokuResponse['payment_url'],
                'raw_response'     => $dokuResponse['raw'] ?? null,
            ]);

            return response()->json([
                'success'      => true,
                'invoice'      => $invoiceNumber,
                'amount'       => $amount,
                'amount_fmt'   => 'Rp ' . number_format($amount, 0, ',', '.'),
                'payment_url'  => $dokuResponse['payment_url'],
                'simulate_url' => url('/wallet/topup/simulate/' . $invoiceNumber),
            ]);
        }

        // Jika DOKU API gagal (misal credentials sandbox / endpoint butuh aktivasi channel)
        Log::warning('DOKU API Error, Menyediakan mode simulasi sandbox lokal', [
            'error'   => $dokuResponse['message'] ?? 'Unknown error',
            'invoice' => $invoiceNumber,
        ]);

        return response()->json([
            'success'      => false,
            'message'      => $dokuResponse['message'] ?? 'Gagal membuat tagihan QRIS DOKU.',
            'invoice'      => $invoiceNumber,
            'amount'       => $amount,
            'amount_fmt'   => 'Rp ' . number_format($amount, 0, ',', '.'),
            'simulate_url' => url('/wallet/topup/simulate/' . $invoiceNumber),
        ], 422);
    }

    /**
     * Cek status transaksi pembayaran QRIS (untuk polling di frontend modal).
     */
    public function checkStatus($invoiceNumber)
    {
        $transaction = WalletTransaction::where('invoice_number', $invoiceNumber)->first();
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $user = User::find($transaction->user_id);

        return response()->json([
            'success'     => true,
            'invoice'     => $transaction->invoice_number,
            'status'      => $transaction->status,
            'is_paid'     => $transaction->status === 'SUCCESS',
            'amount'      => (float) $transaction->amount,
            'amount_fmt'  => 'Rp ' . number_format($transaction->amount, 0, ',', '.'),
            'new_balance' => (float) ($user->balance ?? 0),
            'balance_fmt' => 'Rp ' . number_format($user->balance ?? 0, 0, ',', '.'),
        ]);
    }

    /**
     * Webhook Notification dari DOKU Payment Gateway (Server-to-Server).
     */
    public function handleNotification(Request $request, DokuService $dokuService)
    {
        Log::info('DOKU Webhook Notification Received', [
            'headers' => $request->headers->all(),
            'body'    => $request->all(),
        ]);

        $rawBody = $request->getContent();
        $invoiceNumber = $request->input('order.invoice_number') ?? $request->input('invoice_number');
        $transactionStatus = strtoupper($request->input('transaction.status', ''));

        if (!$invoiceNumber) {
            return response()->json(['message' => 'Invalid payload: missing invoice_number'], 400);
        }

        // Cari transaksi
        $transaction = WalletTransaction::where('invoice_number', $invoiceNumber)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction->status === 'SUCCESS') {
            return response()->json(['message' => 'Transaction already processed']);
        }

        if ($transactionStatus === 'SUCCESS') {
            DB::transaction(function () use ($transaction, $rawBody) {
                $trx = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();
                if ($trx && $trx->status !== 'SUCCESS') {
                    $trx->update([
                        'status'       => 'SUCCESS',
                        'paid_at'      => now(),
                        'raw_response' => json_decode($rawBody, true) ?: $trx->raw_response,
                    ]);

                    // Tambahkan saldo ke pengguna
                    $user = User::where('id', $trx->user_id)->lockForUpdate()->first();
                    if ($user) {
                        $user->increment('balance', $trx->amount);
                    }
                }
            });

            return response()->json(['message' => 'SUCCESS']);
        }

        if (in_array($transactionStatus, ['FAILED', 'EXPIRED'])) {
            $transaction->update(['status' => $transactionStatus]);
        }

        return response()->json(['message' => 'Notification recorded']);
    }

    /**
     * Simulasi Pembayaran QRIS Berhasil (Helper untuk pengetesan di Localhost).
     */
    public function simulatePayment($invoiceNumber)
    {
        $transaction = WalletTransaction::where('invoice_number', $invoiceNumber)->first();
        if (!$transaction) {
            return redirect('/dashboard')->with('error', 'Transaksi tidak ditemukan.');
        }

        if ($transaction->status === 'SUCCESS') {
            return redirect('/dashboard')->with('info', 'Transaksi ini sudah berhasil dibayar sebelumnya.');
        }

        DB::transaction(function () use ($transaction) {
            $trx = WalletTransaction::where('id', $transaction->id)->lockForUpdate()->first();
            $trx->update([
                'status'  => 'SUCCESS',
                'paid_at' => now(),
            ]);

            $user = User::where('id', $trx->user_id)->lockForUpdate()->first();
            if ($user) {
                $user->increment('balance', $trx->amount);
            }
        });

        return redirect('/dashboard')->with('success', 'Simulasi pembayaran QRIS berhasil! Saldo sebesar Rp ' . number_format($transaction->amount, 0, ',', '.') . ' telah ditambahkan ke dompet.');
    }
}
