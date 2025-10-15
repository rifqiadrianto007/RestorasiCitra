<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class ImageController extends Controller
{
    public function smooth(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'level' => 'required|integer|min:1|max:25',
        ]);

        // Simpan sementara
        $uploaded = $request->file('image');
        $path = $uploaded->store('uploads', 'public');
        $localPath = storage_path('app/public/' . $path);

        // Kirim ke Python worker
        $workerUrl = env('PYTHON_WORKER_URL', 'http://127.0.0.1:8001') . '/smooth';

        try {
            $response = Http::attach(
                'image',
                fopen($localPath, 'r'),
                basename($localPath)
            )->post($workerUrl, [
                'level' => $request->level,
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['msg' => 'Gagal terhubung ke worker: ' . $e->getMessage()]);
        }

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Worker error: ' . $response->body()]);
        }

        // Simpan hasil
        $outputName = 'processed/smooth_' . time() . '.png';
        Storage::disk('public')->put($outputName, $response->body());

        return back()->with('result', [
            'url' => Storage::url($outputName),
            'download' => Storage::url($outputName),
        ]);
    }

    public function removeBackground(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // max 10MB
        ]);

        $uploaded = $request->file('image');
        $path = $uploaded->store('uploads', 'public');
        $localPath = storage_path('app/public/' . $path);

        $apiKey = env('REMOVEBG_API_KEY');

        if (empty($apiKey)) {
            return back()->withErrors(['msg' => 'Remove.bg API key belum diatur di .env']);
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
            ])->attach(
                'image_file',
                fopen($localPath, 'r'),
                basename($localPath)
            )->post('https://api.remove.bg/v1.0/removebg', [
                'size' => 'auto'
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['msg' => 'Gagal memanggil remove.bg: ' . $e->getMessage()]);
        }

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'remove.bg error: ' . $response->body()]);
        }

        // Hasil remove.bg berupa binary PNG
        $outputName = 'processed/removed_' . time() . '.png';
        Storage::disk('public')->put($outputName, $response->body());

        return back()->with('result', [
            'url' => Storage::url($outputName),
            'download' => Storage::url($outputName),
        ]);
    }
}
