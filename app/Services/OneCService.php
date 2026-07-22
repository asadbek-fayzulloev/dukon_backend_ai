<?php

namespace App\Services;

use App\Models\CompanyIntegration;
use Illuminate\Support\Facades\Http;
use Throwable;

class OneCService
{
    public function testConnection(CompanyIntegration $integration): array
    {
        if (!$integration->url) {
            return ['success' => false, 'message' => "1C ulanish manzili kiritilmagan."];
        }

        try {
            $response = Http::withBasicAuth($integration->username ?? '', $integration->password ?? '')
                ->timeout(15)
                ->acceptJson()
                ->get(rtrim($integration->url, '/') . '/$metadata');

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Ulanish muvaffaqiyatli.'];
            }

            return ['success' => false, 'message' => "1C javob berdi, lekin xatolik bilan (HTTP {$response->status()})."];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => "Ulanib bo'lmadi: {$e->getMessage()}"];
        }
    }

    public function pushDocument(CompanyIntegration $integration, string $entitySet, array $payload): array
    {
        if (!$integration->url) {
            return ['success' => false, 'message' => "1C ulanish manzili kiritilmagan."];
        }

        try {
            $response = Http::withBasicAuth($integration->username ?? '', $integration->password ?? '')
                ->timeout(30)
                ->acceptJson()
                ->post(rtrim($integration->url, '/') . '/' . ltrim($entitySet, '/'), $payload);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Yuborildi.', 'data' => $response->json()];
            }

            return ['success' => false, 'message' => "1C xatolik qaytardi (HTTP {$response->status()}): {$response->body()}"];
        } catch (Throwable $e) {
            return ['success' => false, 'message' => "Yuborib bo'lmadi: {$e->getMessage()}"];
        }
    }
}
