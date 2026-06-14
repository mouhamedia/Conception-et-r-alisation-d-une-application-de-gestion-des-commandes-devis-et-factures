<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IAService
{
    private string $baseUrl;
    private string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.fastapi.url', 'http://localhost:8001');
        $this->secretKey = config('services.fastapi.secret_key', '');
    }

    public function getPredictions(int $entrepriseId): ?array
    {
        return $this->post('/api/predictions', ['entreprise_id' => $entrepriseId]);
    }

    public function getRecommendations(int $entrepriseId, int $userId): ?array
    {
        return $this->post('/api/recommendations', [
            'entreprise_id' => $entrepriseId,
            'user_id' => $userId,
        ]);
    }

    public function getAnalyse(int $entrepriseId): ?array
    {
        return $this->post('/api/analyse', ['entreprise_id' => $entrepriseId]);
    }

    public function getSuggestions(int $entrepriseId, string $texte): ?array
    {
        return $this->post('/api/devis/suggestion', [
            'entreprise_id' => $entrepriseId,
            'texte' => $texte,
        ]);
    }

    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }

    private function post(string $endpoint, array $data): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->secretKey])
                ->post("{$this->baseUrl}{$endpoint}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("FastAPI {$endpoint} returned {$response->status()}");
            return null;
        } catch (\Exception $e) {
            Log::error("FastAPI {$endpoint} error: " . $e->getMessage());
            return null;
        }
    }
}
