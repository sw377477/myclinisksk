<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SatuSehatService
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $orgId;

    public function __construct()
    {
        // Pastikan BASE_URL TIDAK berakhir dengan slash
        $this->baseUrl = rtrim(env('SATUSEHAT_BASE_URL'), '/');
        $this->clientId = env('SATUSEHAT_CLIENT_ID');
        $this->clientSecret = env('SATUSEHAT_CLIENT_SECRET');
        $this->orgId = env('SATUSEHAT_ORGANIZATION_ID');
    }

    public function getToken()
    {
        $url = $this->baseUrl . '/oauth2/v1/accesstoken';

        $response = Http::asForm()->post($url, [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        // Debug dulu
         dd($url, $response->status(), $response->body());

        return $response->json()['access_token'] ?? null;
    }

    public function request($method, $endpoint, $body = [])
    {
        $token = $this->getToken();

        return Http::withToken($token)
            ->withHeaders([
                'Content-Type'      => 'application/json',
                'X-Organization-ID' => $this->orgId,
            ])
            ->$method($this->baseUrl . '/' . ltrim($endpoint, '/'), $body)
            ->json();
    }
}
