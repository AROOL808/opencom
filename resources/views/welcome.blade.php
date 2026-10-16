<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>OpenCom - E-Commerce Starter</title>
    <meta name="description" content="OpenCom adalah platform stater e-commerce terbaik untuk toko anda.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js (Sangat disarankan di ekosistem Laravel untuk interaktivitas ringan seperti Slider) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Konfigurasi Custom Font untuk Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4F46E5', // Indigo-600
                        secondary: '#111827', // Gray-900
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- HEADER / NAVBAR -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center cursor-pointer">
                    <span class="text-2xl font-bold text-primary tracking-tight">OpenCom.</span>
                </div>
                <!-- Nav Menu (Desktop) -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#" class="text-gray-900 font-medium hover:text-primary transition">Beranda</a>
                    <a href="#kategori" class="text-gray-500 hover:text-gray-900 transition">Kategori</a>
                    <a href="#populer" class="text-gray-500 hover:text-gray-900 transition">Populer</a>
                    <a href="#produk" class="text-gray-500 hover:text-gray-900 transition">Produk</a>
                </nav>
                <!-- Cart & Login Button -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-gray-900 transition relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <!-- Render angka dari Controller, sembunyikan badge jika 0 -->
                    @if($cartCount > 0)
                    <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
                    @endif
                    </a>
                    @if(session('user_logged_in'))
                    <!-- Area Dropdown Akun -->
                    <div class="relative">
                        <!-- Tombol Avatar (Lingkaran dengan huruf pertama) -->
                        <button onclick="toggleUserDropdown()" type="button" class="flex items-center focus:outline-none">
                            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg uppercase shadow-sm border border-indigo-200 hover:opacity-90 transition">
                                {{ strtoupper(substr(session('username'), 0, 1)) }}
                            </div>
                        </button>

                        <!-- Menu Dropdown (Hidden secara default) -->
                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-400">Masuk sebagai</p>
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ session('username') }}</p>
                            </div>
                            
                            <!-- Tombol Keluar / Logout -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                        <!-- Jika belum login, tampilkan tombol Masuk dan Daftar -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class=" text-black px-4 py-2 rounded-lg font-medium hover:bg-black hover:text-white border-2 border-black transition">
                            Daftar
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        
        <!--  HERO SECTION DENGAN SLIDER (Menggunakan Alpine.js) -->
        <section 
            x-data="{ 
                activeSlide: 0, 
                slides: {{ json_encode($heroSlides) }},
                next() { this.activeSlide = this.activeSlide === this.slides.length - 1 ? 0 : this.activeSlide + 1 },
                prev() { this.activeSlide = this.activeSlide === 0 ? this.slides.length - 1 : this.activeSlide - 1 }
            }" 
            x-init="setInterval(() => next(), 5000)" 
            class="relative bg-secondary text-white overflow-hidden"
        >
            <div class="relative h-[500px] md:h-[600px] w-full">
                <!-- Slides Loop -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div 
                        x-show="activeSlide === index"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 translate-x-8"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-500 absolute top-0 left-0 w-full"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0 -translate-x-8"
                        class="absolute inset-0 flex flex-col md:flex-row items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 z-10"
                    >
                        <!-- Teks Info -->
                        <div class="md:w-1/2 space-y-6 text-center md:text-left z-20">
                            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                                <span x-text="slide.title.split(' ')[0]" class="text-primary"></span> 
                                <span x-text="slide.title.substring(slide.title.indexOf(' ') + 1)"></span>
                            </h1>
                            <p class="text-lg text-gray-300 max-w-md mx-auto md:mx-0" x-text="slide.desc"></p>
                            <div class="pt-4">
                                <a href="#produk" class="inline-block bg-primary hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300" x-text="slide.cta"></a>
                            </div>
                        </div>
                        <!-- Gambar -->
                        <div class="md:w-1/2 absolute md:relative inset-0 md:inset-auto mt-12 md:mt-0 opacity-20 md:opacity-90 z-10 flex justify-end">
                            <img :src="slide.image" :alt="slide.title" class="rounded-2xl shadow-2xl object-cover h-full md:h-96 w-full md:w-5/6">
                        </div>
                    </div>
                </template>
                
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary rounded-full blur-3xl opacity-20"></div>

                <!-- Slider Navigation Dots -->
                <div class="absolute bottom-8 left-0 right-0 flex justify-center space-x-2 z-30">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button 
                            @click="activeSlide = index" 
                            :class="{'bg-primary w-8': activeSlide === index, 'bg-gray-400 w-3': activeSlide !== index}"
                            class="h-3 rounded-full transition-all duration-300"
                            aria-label="Go to slide"
                        ></button>
                    </template>
                </div>
            </div>
        </section>

        <!--  SECTION LIST KATEGORI -->
        <section id="kategori" class="py-16 bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-900">Kategori</h2>
                    <p class="mt-3 text-gray-500">Temukan berbagai macam produk berdasarkan kategori favorit.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Menggunakan $productsByCategory agar data relasi produk sudah tersedia -->
                    @foreach($productsByCategory as $category)
                        @php
                            // Ambil produk pertama dari kategori ini (jika ada)
                            $firstProduct = $category->produk->first();
                            
                            // Logika fallback: Jika kategori memiliki produk, gunakan foto produk pertama. 
                            // Jika kategori masih kosong, gunakan placeholder warna.
                            $bgImage = $firstProduct 
                                ? asset('storage/products/' . $firstProduct->alamat_foto) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($category->nama_kategori) . '&background=random';
                        @endphp

                        <a href="#kategori-{{ Str::slug($category->nama_kategori) }}" class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 bg-white">
                            <div class="aspect-w-3 aspect-h-2 relative bg-gray-200">
                                <!-- Menerapkan variabel $bgImage sebagai source gambar -->
                                <img src="{{ $bgImage }}" alt="{{ $category->nama_kategori }}" class="object-cover w-full h-40 opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                                
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                                
                                <!-- Nama Kategori -->
                                <div class="absolute bottom-0 left-0 p-4 w-full flex items-center justify-between">
                                    <span class="text-white font-semibold text-lg">{{ $category->nama_kategori }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!--  PRODUK TERPOPULER -->
        <section id="populer" class="py-16 bg-gray-50 border-b border-gray-200">
            <!-- Notifikasi Pesan -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm text-center font-medium">{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm text-center font-medium">{{ session('error') }}</div>
                </div>
            @endif

            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Produk Terpopuler</h2>
                <p class="mt-3 text-gray-500">Jelajahi koleksi unggulan kami yang paling banyak diminati.</p>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($popularProducts as $product)
                        <article class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                            <div class="relative overflow-hidden aspect-w-4 aspect-h-5 bg-gray-200">
                                <img src="{{ asset('storage/products/' . $product->alamat_foto) }}" alt="{{ $product->nama_produk }}" class="object-cover w-full h-64 group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-semibold text-gray-900 truncate" title="{{ $product->nama_produk }}">
                                    {{ $product->nama_produk }}
                                </h4>
                                <p class="text-primary font-bold mt-2">
                                    Rp {{ number_format($product->harga, 0, ',', '.') }}
                                </p>
                                 <a href="{{ route('product.show', $product->product_id) }}"
                                        class="block text-center mt-2 w-full bg-primary hover:bg-indigo-700 border border-gray-200 text-white font-medium py-2 rounded-lg transition duration-200 ">
                                        Detail
                                </a>
                                <!-- Form Tambah ke Keranjang -->
                                <form action="{{ route('cart.add', $product->product_id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="button" 
                                        onclick="openCartModal('{{ $product->product_id }}', '{{ addslashes($product->nama_produk) }}')" 
                                        class="mt-2 w-full bg-gray-50 hover:bg-primary hover:text-white border border-gray-200 text-gray-700 font-medium py-2 rounded-lg transition duration-200">
                                        Tambah
                                    </button>
                                </form>
                              
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!--  DAFTAR PRODUK BERDASARKAN KATEGORI -->
        <div id="produk" class="bg-white py-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Cari Berdasarkan Kategori</h2>
            </div>
            @foreach($productsByCategory as $category)
                <!-- Hanya tampilkan kategori yang memiliki produk -->
                @if($category->produk->count() > 0)
                <section id="kategori-{{ Str::slug($category->nama_kategori) }}" class="py-10">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 pb-4 border-b border-gray-100">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">{{ $category->nama_kategori }}</h3>
                            <a href="#" class="group flex items-center text-primary font-medium hover:text-indigo-800 transition">
                                Lihat Semua 
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($category->produk as $product)
                                <article class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                                    <div class="relative overflow-hidden aspect-w-4 aspect-h-5 bg-gray-200">
                                        <img src="{{ asset('storage/products/' . $product->alamat_foto) }}" alt="{{ $product->nama_produk }}" class="object-cover w-full h-64 group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <div class="p-5">
                                        <h4 class="text-lg font-semibold text-gray-900 truncate" title="{{ $product->nama_produk }}">
                                            {{ $product->nama_produk }}
                                        </h4>
                                        <p class="text-primary font-bold mt-2">
                                            Rp {{ number_format($product->harga, 0, ',', '.') }}
                                        </p>
                                        <a href="{{ route('product.show', $product->product_id) }}"
                                            class="block text-center mt-2 w-full bg-primary hover:bg-indigo-700 border border-gray-200 text-white font-medium py-2 rounded-lg transition duration-200 ">
                                            Detail
                                        </a>
                                        <!-- Form Tambah ke Keranjang -->
                                        <form action="{{ route('cart.add', $product->product_id) }}" method="POST" class="mt-4">
                                            @csrf
                                            <button type="submit" class="w-full bg-gray-50 hover:bg-primary hover:text-white border border-gray-200 text-gray-700 font-medium py-2 rounded-lg transition duration-200">
                                                Tambah
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                    </div>
                </section>
                @endif
            @endforeach
        </div>
        <!-- . SECTION LARGE COPYWRITING (BARU) -->
        <section class="bg-primary text-white py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Elemen Dekorasi Latar -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-indigo-800 rounded-full blur-3xl opacity-40 translate-x-1/3 translate-y-1/3"></div>
            
            <div class="max-w-4xl mx-auto text-center space-y-8 relative z-10">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
                    Kualitas Premium. <br> Gaya Autentik. Harga Terjangkau.
                </h2>
                <p class="text-lg md:text-xl text-indigo-100 font-medium max-w-2xl mx-auto leading-relaxed">
                    Kami percaya bahwa setiap orang berhak tampil percaya diri dan menawan tanpa harus merogoh kocek terlalu dalam. Mulai perjalanan gaya Anda hari ini bersama OpenCom.
                </p>
                <div class="pt-4">
                    <a href="#produk" class="inline-block bg-white text-primary font-bold py-3.5 px-10 rounded-full shadow-xl hover:bg-gray-100 hover:scale-105 transition-all duration-300">
                        Belanja Sekarang
                    </a>
                </div>
            </div>
        </section>

        <!--  SECTION ALAMAT TOKO (BARU) -->
        <section class="py-16 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row bg-gray-50 rounded-3xl overflow-hidden shadow-sm border border-gray-100">
                    
                    <!-- Informasi Alamat & Kontak -->
                    <div class="md:w-1/2 p-10 lg:p-14 flex flex-col justify-center">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Kunjungi Toko Offline Kami</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            Ingin melihat, menyentuh, dan mencoba langsung produk-produk pilihan kami? Datang dan rasakan pengalaman belanja tatap muka di lokasi kami yang nyaman.
                        </p>
                        
                        <ul class="space-y-6 text-gray-700">
                            <!-- Lokasi -->
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-primary mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <div>
                                    <strong class="block text-lg text-gray-900 mb-1">OpenCom Flagship Store</strong>
                                    Jl. Braga No. 123, Pusat Kota<br>Bandung, Jawa Barat 40111
                                </div>
                            </li>
                            <!-- Jam Buka -->
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-primary mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <strong class="block text-lg text-gray-900 mb-1">Jam Operasional</strong>
                                    Senin - Minggu <br> Pukul 09:00 - 21:00 WIB
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Peta / Gambar Lokasi -->
                    <div class="md:w-1/2 relative min-h-[350px] bg-gray-200">
                        <!-- Menggunakan placeholder foto toko fisik dari Unsplash -->
                        <img src="https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Toko Offline OpenCom" class="absolute inset-0 w-full h-full object-cover">
                        <!-- Overlay ringan agar gambar tidak terlalu terang -->
                        <div class="absolute inset-0 bg-black/10"></div>
                        
                        <!-- Tombol Petunjuk Arah (Opsional, mengarah ke Google Maps) -->
                        <div class="absolute bottom-6 right-6">
                            <a href="#" class="bg-white/90 backdrop-blur-sm hover:bg-white text-gray-900 font-semibold px-5 py-2.5 rounded-lg shadow-lg flex items-center transition duration-300">
                                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                Buka di Maps
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-200 pt-12 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 text-center md:text-left">
                <!-- Branding -->
                <div>
                    <span class="text-2xl font-bold text-primary">OpenCom.</span>
                    <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                       OpenCom adalah fondasi untuk membangun toko online sesuai kebutuhan Anda. Mulai dengan lebih mudah, kembangkan dengan bebas, dan wujudkan ide bisnis Anda.
                    </p>
                </div>
                <!-- Quick Links -->
                <div>
                    <h4 class="text-gray-900 font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-primary transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-primary transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-primary transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <!-- Contact -->
                <div>
                    <h4 class="text-gray-900 font-semibold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>Bantuan & Dukungan</li>
                        <li>support@opencom.org</li>
                        <li>+62 811 2222 3333</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} OpenCom. All rights reserved.</p>
                <p class="mt-2 md:mt-0">Dibuat dengan ❤️ oleh Senior Laravel Dev</p>
            </div>
        </div>
    </footer>
    <!-- ================= MODAL TAMBAH KE KERANJANG ================= -->
    <div id="modalKeranjang" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white p-6 rounded-2xl w-full max-w-sm shadow-xl transform transition-all relative">
            
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Masukan Keranjang</h3>
                <button type="button" onclick="closeCartModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Nama Produk Terpilih -->
            <p id="modalProductName" class="text-gray-600 text-sm mb-5 font-medium truncate"></p>
            
            <!-- Form Pengisian Jumlah -->
            <form id="formCartModal" method="POST" action="">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Atur Jumlah</label>
                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden w-32 shadow-sm">
                        <button type="button" onclick="decrementQty()" class="px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-black transition font-bold text-lg">-</button>
                        <input type="number" name="jumlah" id="inputQty" value="1" min="1" class="w-full text-center border-0 focus:ring-0 p-2 outline-none font-semibold text-gray-800" required readonly>
                        <button type="button" onclick="incrementQty()" class="px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-black transition font-bold text-lg">+</button>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg shadow-md transition duration-300">
                    Konfirmasi
                </button>
            </form>
        </div>
    </div>
    <script>
        // Fungsi untuk membuka/menutup dropdown menu profil
        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('hidden');
        }

        // Menutup dropdown jika pengguna mengklik di luar area avatar/menu
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdownMenu');
            const button = dropdown?.previousElementSibling;
            if (dropdown && !dropdown.contains(e.target) && !button?.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        function openCartModal(productId, productName) {
            // 1. Tampilkan nama produk ke dalam modal
            document.getElementById('modalProductName').innerText = productName;
            
            // 2. Buat URL dinamis sesuai ID produk dan setel ke action form
            let baseUrl = "{{ url('/cart/add') }}";
            document.getElementById('formCartModal').action = baseUrl + '/' + productId;
            
            // 3. Reset kuantitas ke 1 setiap kali modal dibuka
            document.getElementById('inputQty').value = 1;
            
            // 4. Buka modal
            document.getElementById('modalKeranjang').classList.remove('hidden');
        }

        function closeCartModal() {
            document.getElementById('modalKeranjang').classList.add('hidden');
        }

        function incrementQty() {
            let input = document.getElementById('inputQty');
            input.value = parseInt(input.value) + 1;
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