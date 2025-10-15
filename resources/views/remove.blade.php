<!DOCTYPE html>
<html>
<head>
    <title>Remove Background</title>
</head>
<body>
    <h2>Remove Background Demo</h2>

    @if($errors->any())
        <div style="color: red;">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('remove.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="image">Pilih Gambar:</label>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Hapus Latar Belakang</button>
    </form>

    @if(session('result'))
        <h3>Hasil:</h3>
        <img src="{{ session('result')['url'] }}" alt="hasil" width="400"><br>
        <a href="{{ session('result')['download'] }}" download>Download Hasil</a>
    @endif
</body>
</html>
