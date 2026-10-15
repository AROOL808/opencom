<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Mengambil title dinamis dari halaman anak -->
    <title>@yield('title', 'Dashboard Admin') - OpenCom</title>
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
<body class="bg-gray-50 text-gray-800 font-sans flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-secondary text-white flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-gray-800">
            <span class="text-2xl font-bold text-primary">OpenCom<span class="text-white text-sm ml-2 font-normal">Admin</span></span>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4 space-y-1">
            <!-- Nav: Profil Toko (Dinamis Aktif) -->
            <a href="{{ route('admin.dashboard.profile') }}" class="{{ request()->routeIs('admin.dashboard.profile') ? 'bg-gray-800 text-white border-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white border-transparent' }} px-6 py-3 flex items-center border-l-4 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profil Toko
            </a>
            
            <!-- Nav: Item Produk (Dinamis Aktif) -->
            <a href="{{ route('admin.dashboard.items') }}" class="{{ request()->routeIs('admin.dashboard.items') ? 'bg-gray-800 text-white border-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white border-transparent' }} px-6 py-3 flex items-center border-l-4 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Item Produk
            </a>
        </nav>
        
        <!-- Logout Button (Best Practice Laravel menggunakan form POST untuk keamanan CSRF) -->
        <div class="p-4 border-t border-gray-800">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center text-gray-400 hover:text-red-400 transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Header Atas -->
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 z-10">
            <!-- Judul Halaman Dinamis -->
            <h1 class="text-xl font-semibold text-gray-800">@yield('title')</h1>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">A</div>
                <span class="font-medium text-sm text-gray-700">{{ session('admin_username') ?? 'Administrator' }}</span>
            </div>
        </header>

        <!-- Area Konten Dinamis -->
        <div class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>