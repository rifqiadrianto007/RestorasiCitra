<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function smooth(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'level' => 'required|integer|min:1|max:20',
        ]);

        $path = $request->file('image')->store('uploads', 'public');
        $localPath = storage_path('app/public/' . $path);

        // Kirim ke worker Python
        $response = \Http::attach('image', fopen($localPath, 'r'), basename($localPath))->post(env('PYTHON_WORKER_URL') . '/smooth', [
            'level' => $request->level,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Worker error: ' . $response->body()]);
        }

        $outputName = 'processed/smooth_' . time() . '.png';
        \Storage::disk('public')->put($outputName, $response->body());

        return back()->with('result', [
            'url' => \Storage::url($outputName),
            'download' => \Storage::url($outputName),
        ]);
    }
}
