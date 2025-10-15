<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRECA - Sistem Restorasi Citra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h2 class="text-5xl font-bold text-gray-800 mb-6 leading-tight">
                Sistem Restorasi Citra untuk
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
                    Penghalusan dan Penghapusan Latar Belakang
                </span>
                Berbasis Web
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                SIRECA menyediakan fitur canggih untuk memproses dan meningkatkan kualitas gambar
                dengan teknologi pemrosesan citra modern.
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Remove Background Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-red-100 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-cut text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Remove Background</h3>
                    <p class="text-gray-600 mb-6">
                        Hapus latar belakang gambar secara otomatis dengan teknologi yang canggih.
                        Hasil yang presisi dan bersih dalam hitungan detik.
                    </p>
                    <a href="/remove-form"
                       class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                        <i class="fas fa-play mr-2"></i>
                        Gunakan Fitur
                    </a>
                </div>
            </div>

            <!-- Gaussian Smoothing Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-10 h-10 text-blue-600">
                            <defs>
                                <linearGradient id="gaussianGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#1d4ed8;stop-opacity:1" />
                                </linearGradient>
                                <filter id="gaussianBlur" x="-50%" y="-50%" width="200%" height="200%">
                                    <feGaussianBlur in="SourceGraphic" stdDeviation="2" />
                                </filter>
                            </defs>
                            <circle cx="12" cy="12" r="8" fill="url(#gaussianGradient)" filter="url(#gaussianBlur)" />
                            <circle cx="9" cy="9" r="3" fill="white" fill-opacity="0.7" />
                            <circle cx="15" cy="15" r="2" fill="white" fill-opacity="0.5" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Gaussian Smoothing</h3>
                    <p class="text-gray-600 mb-6">
                        Terapkan efek penghalusan Gaussian untuk mengurangi noise dan
                        meningkatkan kualitas visual gambar dengan kontrol tingkat kehalusan.
                    </p>
                    <a href="/smooth-form"
                       class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                        <i class="fas fa-play mr-2"></i>
                        Gunakan Fitur
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
