<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->nama_produk }} - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#4F46E5', secondary: '#111827' } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex flex-col min-h-screen">

    <!-- Navbar Sederhana -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">OpenCom.</a>
            
            <div class="flex items-center space-x-6">
                <!-- Icon Cart -->
                <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-gray-900 transition relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <!-- Breadcrumb / Navigasi -->
        <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-primary transition">Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2">{{ $product->category->nama_kategori ?? 'Kategori' }}</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-gray-400 truncate w-24 sm:w-auto">{{ $product->nama_produk }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Pesan Sukses / Error -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 text-sm font-medium">{{ session('error') }}</div>
        @endif

        <!-- Layout Produk (Grid 2 Kolom) -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-col md:flex-row">
                
                <!-- Kiri: Foto Produk -->
                <div class="md:w-1/2 bg-gray-50 p-8 flex items-center justify-center">
                    <img src="{{ asset('storage/products/' . $product->alamat_foto) }}" alt="{{ $product->nama_produk }}" class="w-full max-w-md h-auto object-cover rounded-2xl shadow-md border border-gray-200">
                </div>

                <!-- Kanan: Detail Produk -->
                <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                    
                    <div class="mb-6">
                        <span class="bg-indigo-50 text-primary text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            {{ $product->category->nama_kategori ?? 'Umum' }}
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-2">
                            {{ $product->nama_produk }}
                        </h1>
                        <p class="text-2xl font-bold text-primary mt-2">
                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Status Stok -->
                    <div class="flex items-center space-x-2 mb-6 text-sm">
                        @if($product->stok > 0)
                            <span class="flex items-center text-green-600 font-semibold">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Stok Tersedia ({{ $product->stok }} item)
                            </span>
                        @else
                            <span class="flex items-center text-red-500 font-semibold">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Stok Habis
                            </span>
                        @endif
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Deskripsi Produk</h3>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            {{ $product->deskripsi ?: 'Tidak ada deskripsi untuk produk ini.' }}
                        </p>
                    </div>

                    <div class="border-t border-gray-100 my-6"></div>

                    <!-- Form Keranjang -->
                    <form action="{{ route('cart.add', $product->product_id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Input Kuantitas (Hanya aktif jika stok > 0) -->
                            <div class="flex-shrink-0">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                                <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden w-32 h-12 shadow-sm">
                                    <button type="button" onclick="decrementQty()" class="px-4 bg-gray-50 text-gray-600 hover:bg-gray-200 h-full font-bold transition {{ $product->stok == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stok == 0 ? 'disabled' : '' }}>-</button>
                                    
                                    <input type="number" name="jumlah" id="inputQty" value="{{ $product->stok > 0 ? 1 : 0 }}" min="1" max="{{ $product->stok }}" class="w-full text-center border-0 focus:ring-0 p-0 h-full outline-none font-semibold text-gray-800" readonly>
                                    
                                    <button type="button" onclick="incrementQty({{ $product->stok }})" class="px-4 bg-gray-50 text-gray-600 hover:bg-gray-200 h-full font-bold transition {{ $product->stok == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stok == 0 ? 'disabled' : '' }}>+</button>
                                </div>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="flex-grow flex items-end">
                                <button type="submit" class="w-full h-12 bg-primary hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition duration-300 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed" {{ $product->stok == 0 ? 'disabled' : '' }}>
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    {{ $product->stok > 0 ? 'Masukkan Keranjang' : 'Stok Kosong' }}
                                </button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </main>

    <!-- Script Kuantitas -->
    <script>
        function incrementQty(maxStok) {
            let input = document.getElementById('inputQty');
            let currentVal = parseInt(input.value);
            if (currentVal < maxStok) {
                input.value = currentVal + 1;
            } else {
                alert('Maksimal pembelian sesuai stok tersedia!');
            }
        }

        function decrementQty() {
            let input = document.getElementById('inputQty');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    </script>
</body>
</html>