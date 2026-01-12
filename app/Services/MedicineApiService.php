<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use RuntimeException;

class MedicineApiService
{
    protected string $baseUrl;
    protected string $email;
    protected string $password;
    protected string $tokenCacheKey;

    public function __construct()
    {
        $this->baseUrl = config('medicine.base_url');
        $this->email = config('medicine.email');
        $this->password = config('medicine.password');
        $this->tokenCacheKey = config('medicine.token_cache_key');

        if (!$this->baseUrl || !$this->email || !$this->password) {
            throw new \RuntimeException('Medicine API configuration is missing.');
        }
    }

    public function getToken(): string
    {
        if (Cache::has($this->tokenCacheKey)) {
            return Cache::get($this->tokenCacheKey);
        }

        $response = Http::post("{$this->baseUrl}/auth", [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to authenticate with Medicine API.');
        }

        $data = $response->json();

        $token = $data['access_token'] ?? null;
        $expiresIn = $data['expires_in'] ?? 86400;

        if (!$token) {
            throw new \RuntimeException('Medicine API did not return access token.');
        }

        Cache::put(
            $this->tokenCacheKey,
            $token,
            now()->addSeconds($expiresIn - 60)
        );

        return $token;
    }

    public function getMedicines(): array
    {
        return Http::withToken($this->getToken())
        ->get("{$this->baseUrl}/medicines")
        ->json('medicines', []);
    }

    public function getMedicinePrices(string $medicineId): array
    {
        $response = Http::withToken($this->getToken())
            ->get("{$this->baseUrl}/medicines/{$medicineId}/prices");

        if ($response->failed()) {
            throw new \RuntimeException("Failed to fetch prices for medicine ID: {$medicineId}");
        }

        return $response->json('prices', []);
    }
}
