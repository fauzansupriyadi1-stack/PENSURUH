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
            'type'           => 'TOPUP',
            'invoice_number' => $invoiceNumber,
            'amount'         => $amount,
            'payment_method' => 'QRIS',
            'description'    => 'Isi Saldo Dompet via DOKU QRIS',
            'status'         => 'PENDING',
        ]);

        // Panggil DOKU Checkout QRIS API
        $dokuResponse = $dokuService->createQrisPayment($invoiceNumber, $amount, $user);

        $amountStr = (string) (int) round($amount);
        $fallbackQrisPayload = "00020101021226610014ID.CO.DOKU.WWW01189360089802370924330215{$invoiceNumber}0303UMI51440014ID.CO.QRIS.WWW0215ID10200349182730303UMI52045999530336054"
            . str_pad((string) strlen($amountStr), 2, '0', STR_PAD_LEFT) . $amountStr
            . "5802ID5918PENSURUH INDONESIA6008SURABAYA6105601116304";

        if ($dokuResponse['success']) {
            $transaction->update([
                'doku_payment_url' => $dokuResponse['payment_url'],
                'raw_response'     => $dokuResponse['raw'] ?? null,
            ]);

            return response()->json([
                'success'      => true,
                'doku_active'  => true,
                'invoice'      => $invoiceNumber,
                'amount'       => $amount,
                'amount_fmt'   => 'Rp ' . number_format($amount, 0, ',', '.'),
                'payment_url'  => $dokuResponse['payment_url'],
                'qris_payload' => $dokuResponse['payment_url'],
                'simulate_url' => url('/wallet/topup/simulate/' . $invoiceNumber),
            ]);
        }

        // Jika channel QRIS DOKU belum aktif di Back Office, tetap tampilkan QRIS payload & opsi simulasi
        Log::warning('DOKU API Notice: ' . ($dokuResponse['message'] ?? 'Channel inactive'), [
            'invoice' => $invoiceNumber,
        ]);

        return response()->json([
            'success'      => true,
            'doku_active'  => false,
            'message'      => $dokuResponse['message'] ?? 'Channel QRIS DOKU sedang menunggu aktivasi.',
            'invoice'      => $invoiceNumber,
            'amount'       => $amount,
            'amount_fmt'   => 'Rp ' . number_format($amount, 0, ',', '.'),
            'qris_payload' => $fallbackQrisPayload,
            'simulate_url' => url('/wallet/topup/simulate/' . $invoiceNumber),
        ]);
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

    /**
     * Penarikan Dana Mitra ke Rekening / E-Wallet.
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:10000',
            'bank_name'      => 'required|string|max:50',
            'account_number' => 'required|string|max:40',
        ], [
            'amount.required'         => 'Nominal penarikan wajib diisi.',
            'amount.min'              => 'Minimal penarikan adalah Rp 10.000.',
            'bank_name.required'      => 'Bank / E-Wallet tujuan wajib dipilih.',
            'account_number.required' => 'Nomor rekening / HP tujuan wajib diisi.',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $amount = (float) $request->input('amount');

        $result = DB::transaction(function () use ($user, $amount, $request) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser || (float) $lockedUser->balance < $amount) {
                return ['type' => 'error', 'msg' => 'Saldo tidak mencukupi untuk penarikan sebesar Rp ' . number_format($amount, 0, ',', '.') . '.'];
            }

            $lockedUser->decrement('balance', $amount);

            WalletTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => 'WITHDRAW',
                'invoice_number' => 'WD-' . date('YmdHis') . '-' . strtoupper(Str::random(4)),
                'amount'         => $amount,
                'payment_method' => $request->input('bank_name') . ' - ' . $request->input('account_number'),
                'description'    => 'Penarikan dana ke ' . $request->input('bank_name') . ' (' . $request->input('account_number') . ')',
                'status'         => 'SUCCESS',
                'paid_at'        => now(),
            ]);

            return ['type' => 'success', 'msg' => 'Penarikan dana Rp ' . number_format($amount, 0, ',', '.') . ' ke ' . $request->input('bank_name') . ' berhasil diproses!'];
        });

        return redirect()->back()->with($result['type'], $result['msg']);
    }
}
