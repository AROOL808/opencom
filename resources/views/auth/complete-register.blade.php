<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lengkapi Profil - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#4F46E5', secondary: '#111827' } } } }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-10">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-lg">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Lengkapi Profil Anda</h1>
            <p class="text-gray-500 text-sm">Satu langkah lagi untuk menyelesaikan registrasi</p>
        </div>

        <form action="{{ route('register.complete.post') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Email (Readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ session('register_email') }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-300 text-gray-500 rounded-lg outline-none cursor-not-allowed">
            </div>

            <!-- Username dengan Validasi Error -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username Unik</label>
                <input type="text" name="username" value="{{ old('username') }}" required class="w-full px-4 py-2 border {{ $errors->has('username') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="contoh_user123">
                @error('username')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama User -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama_user" value="{{ old('nama_user', session('register_name')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition">
            </div>

            <!-- Nomor Telepon -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                <input type="tel" name="no_telepon" value="{{ old('no_telepon') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="081234567890">
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                <textarea name="alamat" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Masukkan alamat lengkap...">{{ old('alamat') }}</textarea>
            </div>
            
            <button type="submit" class="w-full block text-center bg-primary text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition mt-2">
                Simpan & Selesaikan Pendaftaran
            </button>
        </form>
    </div>

</body>
</html>