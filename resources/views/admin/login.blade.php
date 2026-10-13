<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - OpenCom</title>
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
<body class="bg-secondary h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border-t-4 border-primary">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Administrator</h1>
            <p class="text-gray-500">Panel Manajemen Toko</p>
        </div>

        <form action="#" method="GET" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Admin</label>
                <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="admin@toko.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="••••••••">
            </div>
            
            <!-- Mengarahkan admin langsung ke dashboard saat diklik -->
            <a href="{{ route('admin.dashboard') }}" class="w-full block text-center bg-gray-900 text-white font-semibold py-2.5 rounded-lg hover:bg-black transition">
                Masuk ke Dashboard
            </a>
        </form>
    </div>

</body>
</html>