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
                    <button class="text-gray-500 hover:text-gray-900 transition relative">
                        <!-- Icon Cart SVG -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">0</span>
                    </button>
                    <a href="{{ route('login') }}" class="hidden md:inline-flex bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        
        <!-- 1. HERO SECTION DENGAN SLIDER (Menggunakan Alpine.js) -->
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

        <!-- 2. SECTION LIST KATEGORI (Menggantikan Produk Terpopuler sebelumnya) -->
        <section id="kategori" class="py-16 bg-gray-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-900">Kategori</h2>
                    <p class="mt-3 text-gray-500">Temukan berbagai macam produk berdasarkan kategori favorit.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($categories as $category)
                        <a href="#kategori-{{ Str::slug($category['name']) }}" class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 bg-white">
                            <div class="aspect-w-3 aspect-h-2 relative bg-gray-200">
                                <img src="{{ $category['image'] }}" alt="{{ $category['name'] }}" class="object-cover w-full h-40 opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                                <!-- Content -->
                                <div class="absolute bottom-0 left-0 p-4 w-full flex items-center justify-between">
                                    <span class="text-white font-semibold text-lg">{{ $category['name'] }}</span>
                                    <span class="text-2xl">{{ $category['icon'] }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        <section id="populer" class="py-16 bg-gray-50 border-b border-gray-200">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Produk Terpopuler</h2>
                <p class="mt-3 text-gray-500">Jelajahi koleksi unggulan kami yang paling banyak diminati. Solusi terbaik yang dirancang khusus untuk memenuhi kebutuhan Anda.</p>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($popularProducts as $product)
                        <article class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                            <!-- Gambar Produk -->
                            <div class="relative overflow-hidden aspect-w-4 aspect-h-5 bg-gray-200">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="object-cover w-full h-64 group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <!-- Detail Produk -->
                            <div class="p-5">
                                <h4 class="text-lg font-semibold text-gray-900 truncate" title="{{ $product['name'] }}">
                                    {{ $product['name'] }}
                                </h4>
                                <p class="text-primary font-bold mt-2">
                                    Rp {{ number_format($product['price'], 0, ',', '.') }}
                                </p>
                                <button class="mt-4 w-full bg-gray-50 hover:bg-primary hover:text-white border border-gray-200 text-gray-700 font-medium py-2 rounded-lg transition duration-200">
                                    Tambah
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- 3. SECTION DAFTAR PRODUK BERDASARKAN KATEGORI -->
        <div id="produk" class="bg-white py-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Cari Berdasarkan Kategori</h2>
            </div>
            @foreach($productsByCategory as $categoryName => $products)
                <section id="kategori-{{ Str::slug($categoryName) }}" class="py-10">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        
                        <!-- Header Kategori & Tombol See More -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 pb-4 border-b border-gray-100">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">{{ $categoryName }}</h3>
                            <a href="#" class="group flex items-center text-primary font-medium hover:text-indigo-800 transition">
                                Lihat Semua 
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                        <!-- Grid Produk Kategori -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            @foreach($products as $product)
                                <article class="bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                                    <!-- Gambar Produk -->
                                    <div class="relative overflow-hidden aspect-w-4 aspect-h-5 bg-gray-200">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="object-cover w-full h-64 group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <!-- Detail Produk -->
                                    <div class="p-5">
                                        <h4 class="text-lg font-semibold text-gray-900 truncate" title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </h4>
                                        <p class="text-primary font-bold mt-2">
                                            Rp {{ number_format($product['price'], 0, ',', '.') }}
                                        </p>
                                        <button class="mt-4 w-full bg-gray-50 hover:bg-primary hover:text-white border border-gray-200 text-gray-700 font-medium py-2 rounded-lg transition duration-200">
                                            Tambah
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                    </div>
                </section>
            @endforeach
        </div>
        <!-- 4. SECTION LARGE COPYWRITING (BARU) -->
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

        <!-- 5. SECTION ALAMAT TOKO (BARU) -->
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

</body>
</html>