@php
    // Ambil data konfigurasi dari database
    $konfig = \App\Models\Konfigurasi::first();
    
    // Set fallback default jika kosong
    $fontName = $konfig->alamat_font ?? 'Inter';
    $primaryColor = $konfig->warna_item ?? '#4F46E5';
    $navColor = $konfig->warna_navigator ?? '#ffffff';
    $bgColor = $konfig->warna_background ?? '#f9fafb';
    $textColor = $konfig->warna_font ?? '#1f2937';
    $namaToko = $konfig->nama_toko ?? 'OpenCom.';
    $alamat = $konfig->alamat_toko ?? '-';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $namaToko }} - E-Commerce</title>
    <meta name="description" content="{{ $namaToko }} adalah platform e-commerce terbaik untuk toko anda.">

    <!-- Dinamis Google Fonts (Membaca nama font dari database) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontName) }}:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Injeksi Tailwind Config Dinamis -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"{{ $fontName }}"', 'sans-serif'],
                    },
                    colors: {
                        primary: '{{ $primaryColor }}',
                        customBg: '{{ $bgColor }}',
                        customNav: '{{ $navColor }}',
                        customText: '{{ $textColor }}'
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased bg-customBg text-customText font-sans flex flex-col min-h-screen">

    <!-- Terapkan Warna Navbar Dinamis pada Header -->
     <header class="bg-white w-full border-b border-gray-100 shadow-sm mb-4">
        <!-- Bagian Utama Header (Logo, Pencarian, Aksi) -->
        <div class="container mx-auto px-4 py-4 flex items-center justify-between gap-6">
            
            <!-- Logo / Nama Toko -->
            <a class="flex items-center gap-2 text-2xl font-black text-gray-900 shrink-0" href="{{ route('home') }}" title="{{ $namaToko }}">
                @if($konfig && $konfig->alamat_logo)
                            <!-- Menampilkan Gambar Logo jika URL diisi -->
                    <img src="{{ $konfig->alamat_logo }}" alt="{{ $namaToko }}" class="h-8 w-auto object-contain">
                @else
                    <!-- Fallback Teks jika Logo kosong -->
                    <span class="text-2xl font-bold text-primary tracking-tight">{{ $namaToko }}</span>
                @endif
            </a>

            <!-- Form Pencarian -->
            <div class="flex-1 max-w-2xl relative" data-store-search data-endpoint="{{ route('search.suggestions') }}">
                <form action="{{ route('search') }}" method="GET" role="search" class="flex items-stretch w-full border border-[#ed6756] rounded-xl overflow-hidden bg-white" data-search-form>
                    <label for="store-search-input" class="sr-only">Cari produk di toko ini</label>
                    
                    <div class="flex items-center justify-center pl-4 pr-2 text-slate-400">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                    </div>
                    
                    <input id="store-search-input" type="search" name="q" value="{{ $query ?? '' }}" placeholder="Cari produk di toko ini..." maxlength="{{ config('search.max_query_length', 100) }}" autocomplete="off" spellcheck="false" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="store-search-list" data-search-input
                        class="flex-1 py-2.5 px-1 text-sm text-gray-700 placeholder-slate-400 bg-transparent focus:outline-none focus:ring-0 appearance-none">
                    
                    @if(isset($searchParameters))
                        @foreach($searchParameters as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                    @endif
                    
                    <button type="button" class="px-3 text-gray-400 hover:text-gray-600 text-xl font-light focus:outline-none hidden" aria-label="Hapus pencarian" data-search-clear>×</button>
                    
                    <button type="submit" class="bg-[#ed6756] hover:bg-[#d65b4c] px-6 flex items-center justify-center transition-colors focus:outline-none" aria-label="Cari produk">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></svg>
                    </button>
                </form>

                <!-- Panel Saran Pencarian -->
                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden" data-search-panel>
                    <p class="px-4 py-2 text-sm text-gray-500" data-search-message></p>
                    <div id="store-search-list" role="listbox" aria-label="Saran pencarian dan produk" class="max-h-64 overflow-y-auto" data-search-list></div>
                </div>
                <span class="sr-only" role="status" aria-live="polite" aria-atomic="true" data-search-status></span>
            </div>

            <!-- Aksi Header (Keranjang & Akun/Auth) -->
            <div class="flex items-center gap-5 shrink-0">
                <!-- Keranjang -->
                <a class="relative p-1 text-gray-700 hover:text-gray-900 transition-colors" href="{{ route('cart.index') }}" aria-label="Keranjang, {{ $cartItems ?? 0 }} barang">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 7h14l1 14H4L5 7Z"/><path d="M8 9V6a4 4 0 0 1 8 0v3"/></svg>
                    @if(isset($cartItems) && $cartItems > 0)
                        <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white">
                            {{ $cartItems > 99 ? '99+' : $cartItems }}
                        </span>
                    @endif
                </a>

                <!-- Akun atau Autentikasi -->
                @if(session('user_logged_in'))
                    <details class="relative group" data-store-account>
                        <summary class="flex items-center gap-2 cursor-pointer list-none focus:outline-none [&::-webkit-details-marker]:hidden" aria-label="Menu akun {{ session('username') }}">
                            <span class="flex items-center justify-center w-8 h-8 text-sm font-bold text-[#ed6756] bg-red-50 rounded-full border border-red-100">
                                {{ mb_strtoupper(mb_substr(session('username'), 0, 1)) }}
                            </span>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 hidden sm:block">Akun</span>
                        </summary>
                        <div class="absolute right-0 w-48 mt-2 py-2 bg-white border border-gray-100 rounded-lg shadow-xl z-50">
                            <span class="block px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ session('username') }}</span>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pesanan saya</a>
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Keluar</button>
                            </form>
                        </div>
                    </details>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-medium text-primary border border-primary rounded-lg hover:shadow-sm hover:shadow-primary">Masuk</a>
                        <a href="{{ route('register') }}" class="px-5 py-2 text-sm font-medium text-white bg-primary border border-primary rounded-lg hover:shadow-sm hover:shadow-primary ">Daftar</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Navigasi (Tengah, Tanpa Garis Bawah Oranye) -->
        <nav class="container mx-auto px-4 flex justify-center items-center gap-8 py-3" aria-label="Navigasi toko">
            <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-900 hover:text-primary transition-colors" @if(request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
            <a href="{{ route('home') }}#produk" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Semua produk</a>
            <a href="{{ route('home') }}#kategori" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Kategori</a>
            <a href="{{ route('home') }}#populer" class="text-sm font-medium text-gray-500 hover:text-primary transition-colors">Populer</a>
        </nav>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        <!--  HERO SECTION -->
        <section 
            x-data="{ 
                activeSlide: 0, 
                totalSlides: {{ $heroes->count() }},
                next() { this.activeSlide = this.activeSlide === this.totalSlides - 1 ? 0 : this.activeSlide + 1 },
                prev() { this.activeSlide = this.activeSlide === 0 ? this.totalSlides - 1 : this.activeSlide - 1 }
            }" 
            x-init="setInterval(() => next(), 5000)" 
            class="relative bg-secondary text-white overflow-hidden"
        >
            <div class="relative h-[500px] md:h-[600px] w-full">
                
                @if($heroes->count() > 0)
                    <!-- Slides Loop via Blade -->
                    @foreach($heroes as $index => $hero)
                        @php
                            // Cek format gambar (URL eksternal vs File storage)
                            $isUrl = \Illuminate\Support\Str::startsWith($hero->alamat_foto, ['http://', 'https://']);
                            $imgSource = $isUrl ? $hero->alamat_foto : asset('storage/heroes/' . $hero->alamat_foto);
                            
                            // Logika pemisah kata pertama agar berwarna beda (Primary Color)
                            $titleParts = explode(' ', $hero->title, 2);
                            $firstWord = $titleParts[0];
                            $restOfTitle = $titleParts[1] ?? '';
                        @endphp

                        <div 
                            x-show="activeSlide === {{ $index }}"
                            x-transition:enter="transition ease-out duration-700"
                            x-transition:enter-start="opacity-0 translate-x-8"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-500 absolute top-0 left-0 w-full"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0 -translate-x-8"
                            style="display: none;" 
                            class="absolute inset-0 flex flex-col md:flex-row items-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 z-10"
                        >
                            <!-- Teks Info -->
                            <div class="md:w-1/2 space-y-6 text-center md:text-left z-20">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                                    <span class="text-primary">{{ $firstWord }}</span> 
                                    <span>{{ $restOfTitle }}</span>
                                </h1>
                                <p class="text-lg text-gray-300 max-w-md mx-auto md:mx-0">
                                    {{ $hero->description }}
                                </p>
                                <div class="pt-4">
                                    <a href="{{ $hero->redirect_link ?? '#produk' }}" class="inline-block bg-primary hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300">
                                        {{ $hero->cta ?? 'Belanja Sekarang' }}
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Gambar -->
                            <div class="md:w-1/2 absolute md:relative inset-0 md:inset-auto mt-12 md:mt-0 opacity-20 md:opacity-90 z-10 flex justify-end">
                                <img src="{{ $imgSource }}" alt="{{ $hero->title }}" class="rounded-2xl shadow-2xl object-cover h-full md:h-96 w-full md:w-5/6">
                            </div>
                        </div>
                    @endforeach

                    <!-- Slider Navigation Dots -->
                    <div class="absolute bottom-8 left-0 right-0 flex justify-center space-x-2 z-30">
                        @foreach($heroes as $index => $hero)
                            <button 
                                @click="activeSlide = {{ $index }}" 
                                :class="{'bg-primary w-8': activeSlide === {{ $index }}, 'bg-gray-400 w-3': activeSlide !== {{ $index }}}"
                                class="h-3 rounded-full transition-all duration-300"
                                aria-label="Go to slide {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>

                @else
                    <!-- Fallback Jika Database Hero Kosong -->
                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                        <p>Belum ada banner yang dikonfigurasi.</p>
                    </div>
                @endif
                
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary rounded-full blur-3xl opacity-20 pointer-events-none"></div>

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
                                        onclick="openCartModal('{{ $product->product_id }}', '{{ addslashes($product->nama_produk) }}', '{{$product->berat}}', '{{$product->harga}}')" 
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
                            <a href="{{ route('search', ['category' => $category->category_id]) }}" class="group flex items-center text-primary font-medium hover:text-indigo-800 transition">
                                Lihat Semua 
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
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
                                        <form action="{{ route('cart.add', $product->product_id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <button type="button" 
                                                onclick="openCartModal('{{ $product->product_id }}', '{{ addslashes($product->nama_produk) }}', '{{$product->berat}}', '{{$product->harga}}')" 
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
                @endif
            @endforeach
        </div>
        <!-- . SECTION LARGE COPYWRITING (BARU) -->
        <section class="primary text-white py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <!-- Elemen Dekorasi Latar -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-primary rounded-full blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-primary rounded-full blur-3xl opacity-20 translate-x-1/3 translate-y-1/3"></div>
            
            <div class="max-w-4xl mx-auto text-center space-y-8 relative z-10">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
                    Kualitas Premium. <br> Gaya Autentik. Harga Terjangkau.
                </h2>
                <p class="text-lg md:text-xl text-white font-medium max-w-2xl mx-auto leading-relaxed">
                    Kami percaya bahwa setiap orang berhak tampil percaya diri dan menawan tanpa harus merogoh kocek terlalu dalam. Mulai perjalanan gaya Anda hari ini bersama OpenCom.
                </p>
                <div class="pt-4">
                    <a href="#produk" class="inline-block bg-primary text-white font-bold py-3.5 px-10 rounded-full shadow-xl hover:bg-gray-100 hover:scale-105 transition-all duration-300">
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
                                    <strong class="block text-lg text-gray-900 mb-1">{{$namaToko}}</strong>
                                    {{$alamat}}
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
                    <span class="text-2xl font-bold text-primary">{{ $namaToko }}</span>
                    <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                       {{ $namaToko }} adalah fondasi untuk membangun toko online sesuai kebutuhan Anda. Mulai dengan lebih mudah, kembangkan dengan bebas, dan wujudkan ide bisnis Anda.
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
            <p id="modalProductPrice" class="text-gray-600 text-sm mb-5 font-medium truncate"></p>
            <p id="modalProductWeight" class="text-gray-600 text-sm mb-5 font-medium truncate"></p>

            
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
        function openCartModal(productId, productName, productWeight, productPrice) {
            // 1. Tampilkan nama produk dan berat ke dalam modal
            document.getElementById('modalProductName').innerText = productName;
            document.getElementById('modalProductWeight').innerText = 'Berat : ' + productWeight + 'g';
            document.getElementById('modalProductPrice').innerText = 'Rp.' + new Intl.NumberFormat('de-DE').format(productPrice);
            
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