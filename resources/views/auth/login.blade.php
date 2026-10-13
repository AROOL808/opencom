<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#4F46E5', secondary: '#111827' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">OpenCom.</h1>
            <p class="text-gray-500">Masuk dengan akun yang sudah ada</p>
        </div>

        <form action="#" method="GET" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="email@contoh.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="••••••••">
            </div>
            
            <!-- Karena belum ada fungsi, tombol ini kita arahkan kembali ke halaman beranda sebagai simulasi -->
            <a href="{{ route('home') }}" class="w-full block text-center bg-primary text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition">
                Masuk
            </a>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Belum punya akun? <a href="#" class="text-primary font-medium hover:underline">Daftar sekarang</a>
        </p>
    </div>

</body>
</html>