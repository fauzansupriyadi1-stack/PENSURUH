<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DokuService
{
    protected string $clientId;
    protected string $secretKey;
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId  = (string) config('services.doku.client_id', env('DOKU_CLIENT_ID', ''));
        $this->secretKey = (string) config('services.doku.secret_key', env('DOKU_SECRET_KEY', ''));
        $this->apiKey    = (string) config('services.doku.api_key', env('DOKU_API_KEY', ''));
        $this->baseUrl   = rtrim((string) config('services.doku.api_url', env('DOKU_API_URL', 'https://api.doku.com')), '/');
    }

    /**
     * Buat transaksi pembayaran QRIS via DOKU Checkout V1 API.
     */
    public function createQrisPayment(string $invoiceNumber, float $amount, User $user): array
    {
        $targetPath = '/checkout/v1/payment';
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($user->phone ?? ''));
        if (empty($cleanPhone) || strlen($cleanPhone) < 9) {
            $cleanPhone = '081234567890';
        }

        $payload = [
            'order' => [
                'amount'         => (int) round($amount),
                'invoice_number' => $invoiceNumber,
                'currency'       => 'IDR',
                'callback_url'   => url('/dashboard?topup=finish&invoice=' . $invoiceNumber),
                'auto_redirect'  => true,
            ],
            'payment' => [
                'payment_due_date'      => 60, // 60 menit kedaluwarsa
                'payment_method_types' => ['QRIS'],
            ],
            'customer' => [
                'id'    => (string) $user->id,
                'name'  => $user->name,
                'email' => $user->email ?: ('user' . $user->id . '@pensuruh.id'),
                'phone' => $cleanPhone,
            ],
            'additional_info' => [
                'integration_partner' => 'PenSuruh-Platform',
            ]
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // 1. Hitung Digest: Base64 dari SHA-256 binary hash payload JSON
        $digest = base64_encode(hash('sha256', $payloadJson, true));

        // 2. Susun Component String sesuai standard DOKU Checkout V1
        $component = "Client-Id:" . $this->clientId . "\n" .
                     "Request-Id:" . $requestId . "\n" .
                     "Request-Timestamp:" . $timestamp . "\n" .
                     "Request-Target:" . $targetPath . "\n" .
                     "Digest:" . $digest;

        // 3. Generate Signature HMAC-SHA256
        $signature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $component, $this->secretKey, true));

        try {
            $response = Http::withHeaders([
                'Client-Id'         => $this->clientId,
                'Request-Id'        => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature'         => $signature,
                'Content-Type'      => 'application/json',
            ])->withBody($payloadJson, 'application/json')
              ->timeout(15)
              ->post($this->baseUrl . $targetPath);

            $body = $response->json();
            Log::info('DOKU QRIS Checkout Response', [
                'status'  => $response->status(),
                'invoice' => $invoiceNumber,
                'body'    => $body,
            ]);

            if ($response->successful() && !empty($body['response']['payment']['url'])) {
                return [
                    'success'     => true,
                    'payment_url' => $body['response']['payment']['url'],
                    'invoice'     => $invoiceNumber,
                    'raw'         => $body,
                ];
            }

            // Tangani pesan error dari DOKU
            $rawMsg = $body['error']['message'] ?? ($body['message'] ?? 'Gagal membuat pembayaran DOKU QRIS.');
            $errorMessage = is_array($rawMsg) ? implode(', ', $rawMsg) : (string) $rawMsg;
            return [
                'success' => false,
                'message' => $errorMessage,
                'raw'     => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('DOKU QRIS Request Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Koneksi ke gateway DOKU mengalami kendala: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verifikasi Signature pada notifikasi Webhook DOKU.
     */
    public function verifyWebhookSignature(array $headers, string $rawBody, string $requestPath): bool
    {
        $clientId  = $headers['client-id'][0] ?? ($headers['Client-Id'] ?? '');
        $requestId = $headers['request-id'][0] ?? ($headers['Request-Id'] ?? '');
        $timestamp = $headers['request-timestamp'][0] ?? ($headers['Request-Timestamp'] ?? '');
        $signature = $headers['signature'][0] ?? ($headers['Signature'] ?? '');

        if (!$clientId || !$requestId || !$timestamp || !$signature) {
            return false;
        }

        $digest = base64_encode(hash('sha256', $rawBody, true));

        $component = "Client-Id:" . $clientId . "\n" .
                     "Request-Id:" . $requestId . "\n" .
                     "Request-Timestamp:" . $timestamp . "\n" .
                     "Request-Target:" . $requestPath . "\n" .
                     "Digest:" . $digest;

        $expectedSignature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $component, $this->secretKey, true));

        return hash_equals($expectedSignature, $signature);
    }
}
