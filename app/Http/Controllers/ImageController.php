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

        $uploaded = $request->file('image');
        $path = $uploaded->store('uploads', 'public');
        $localPath = storage_path('app/public/' . $path);

        // URL worker Python dari .env atau default ke localhost
        $workerUrl = env('PYTHON_WORKER_URL', 'http://127.0.0.1:8001') . '/smooth';

        // Kirim gambar ke worker Python
        try {
            $response = Http::attach('image', fopen($localPath, 'r'), basename($localPath))
                ->post($workerUrl, ['level' => $request->level]);
        } catch (Exception $e) {
            return back()->withErrors(['msg' => 'Gagal terhubung ke worker: ' . $e->getMessage()]);
        }

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Worker error: ' . $response->body()]);
        }

        // Untuk menyimpan hasil
        $outputName = 'processed/smooth_' . time() . '_' . uniqid() . '.png';
        Storage::disk('public')->put($outputName, $response->body());
        @unlink($localPath);

        return back()->with('result', [
            'url' => Storage::url($outputName),
            'download' => Storage::url($outputName),
        ]);
    }
}
