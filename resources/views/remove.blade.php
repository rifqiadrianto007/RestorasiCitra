<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Background</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Remove Background</h2>
            <p class="text-gray-600">Upload gambar dan hapus latar belakang secara otomatis</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-red-800 font-semibold mb-1">Terjadi Kesalahan</h3>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigation -->
        <div class="mb-6">
            <a href="/" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Main Content - Side by Side Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Upload Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Upload Gambar
                </h3>

                <form action="{{ route('remove.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <div class="mb-6">
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-3">
                            Pilih Gambar
                        </label>

                        <div id="uploadArea" class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-indigo-500 transition-colors duration-200">
                            <input type="file" name="image" id="image" accept="image/*" required
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                            <div id="uploadPlaceholder" class="space-y-3">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div class="text-gray-600">
                                    <span class="font-semibold text-indigo-600">Klik untuk upload</span> atau drag & drop
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 10MB)</p>
                            </div>

                            <!-- Preview Image -->
                            <div id="imagePreview" class="hidden">
                                <img id="previewImg" src="" alt="Preview" class="max-w-full h-auto mx-auto rounded-lg shadow-md max-h-64">
                                <div class="mt-3 text-sm text-gray-600">
                                    <span id="fileName" class="font-semibold"></span>
                                </div>
                                <button type="button" id="changeImage" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                    Ganti Gambar
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Hapus Latar Belakang
                        </span>
                    </button>
                </form>
            </div>

            <!-- Result Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Hasil Proses
                </h3>

                @if(session('result'))
                    <div class="mb-6 bg-gray-50 rounded-xl p-4">
                        <img src="{{ session('result')['url'] }}"
                             alt="Hasil remove background"
                             class="max-w-full h-auto mx-auto rounded-lg shadow-md">
                    </div>

                    <a href="{{ session('result')['download'] }}"
                       download
                       class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 text-center transform hover:-translate-y-0.5">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Hasil
                        </span>
                    </a>
                @else
                    <!-- Placeholder when no result -->
                    <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500">Upload gambar untuk melihat hasil di sini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('image');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const fileName = document.getElementById('fileName');
        const changeImageBtn = document.getElementById('changeImage');
        const uploadArea = document.getElementById('uploadArea');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    fileName.textContent = file.name;
                    uploadPlaceholder.classList.add('hidden');
                    imagePreview.classList.remove('hidden');
                    uploadArea.classList.remove('border-dashed');
                    uploadArea.classList.add('border-solid', 'border-indigo-500');
                }

                reader.readAsDataURL(file);
            }
        });

        changeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            uploadPlaceholder.classList.remove('hidden');
            imagePreview.classList.add('hidden');
            uploadArea.classList.remove('border-solid', 'border-indigo-500');
            uploadArea.classList.add('border-dashed');
        });
    </script>
</body>
</html>
