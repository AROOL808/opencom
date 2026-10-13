<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - OpenCom</title>
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

    <!-- SIDEBAR (Sidetab) -->
    <aside class="w-64 bg-secondary text-white flex flex-col hidden md:flex">
        <div class="h-16 flex items-center px-6 border-b border-gray-800">
            <span class="text-2xl font-bold text-primary">OpenCom<span class="text-white text-sm ml-2 font-normal">Admin</span></span>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4 space-y-1">
            <!-- Nav: Profil Toko (Active State) -->
            <a href="#" class="bg-gray-800 text-white px-6 py-3 flex items-center border-l-4 border-primary">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profil Toko
            </a>
            
            <!-- Nav: Kustomasi Toko -->
            <a href="#" class="text-gray-400 hover:bg-gray-800 hover:text-white px-6 py-3 flex items-center transition border-l-4 border-transparent">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                Kustomasi Toko
            </a>
            
            <!-- Nav: Item -->
            <a href="#" class="text-gray-400 hover:bg-gray-800 hover:text-white px-6 py-3 flex items-center transition border-l-4 border-transparent">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Item Produk
            </a>
            
            <!-- Nav: Log Aktivitas Toko -->
            <a href="#" class="text-gray-400 hover:bg-gray-800 hover:text-white px-6 py-3 flex items-center transition border-l-4 border-transparent">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Log Aktivitas
            </a>
        </nav>
        
        <!-- Logout Button -->
        <div class="p-4 border-t border-gray-800">
            <a href="{{ route('home') }}" class="flex items-center text-gray-400 hover:text-red-400 transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Header Atas -->
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 z-10">
            <h1 class="text-xl font-semibold text-gray-800">Pengaturan Profil Toko</h1>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">A</div>
                <span class="font-medium text-sm text-gray-700">Administrator</span>
            </div>
        </header>

        <!-- Area Konten Form -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Informasi Dasar Toko</h2>
                
                <form action="#" class="space-y-6">
                    <!-- Input Nama Toko -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Masukkan nama toko Anda" value="OpenCom Store">
                    </div>

                    <!-- Input Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Publik (Untuk Pelanggan)</label>
                        <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="cs@tokoanda.com">
                    </div>

                    <!-- Input Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap Toko / Gudang</label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Jl. Contoh Alamat No. 123..."></textarea>
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="button" class="bg-primary hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>