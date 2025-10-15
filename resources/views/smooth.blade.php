<!DOCTYPE html>
<html>
<head>
    <title>Gaussian Smoothing</title>
</head>
<body>
    <h2>Gaussian Smoothing Demo</h2>

    <form action="{{ route('smooth.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="image">Pilih Gambar:</label>
        <input type="file" name="image" accept="image/*" required><br><br>

        <label for="level">Tingkat Kehalusan (1-20):</label>
        <input type="range" name="level" min="1" max="20" value="5" oninput="this.nextElementSibling.value = this.value">
        <output>5</output><br><br>

        <button type="submit">Proses</button>
    </form>

    @if(session('result'))
        <h3>Hasil:</h3>
        <img src="{{ session('result')['url'] }}" alt="hasil" width="400"><br>
        <a href="{{ session('result')['download'] }}" download>Download Hasil</a>
    @endif
</body>
</html>
