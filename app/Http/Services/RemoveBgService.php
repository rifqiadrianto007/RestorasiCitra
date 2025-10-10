<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class RemoveBgService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('REMOVEBG_API_KEY');
    }

    public function removeBackground($imagePath)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
        ])->attach(
            'image_file',
            fopen($imagePath, 'r'),
            basename($imagePath)
        )->post('https://api.remove.bg/v1.0/removebg', [
            'size' => 'auto'
        ]);

        if ($response->failed()) {
            throw new \Exception("Remove.bg API error: " . $response->body());
        }

        return $response->body(); // binary hasil gambar tanpa background
    }
}
