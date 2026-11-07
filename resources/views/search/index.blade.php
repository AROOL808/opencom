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
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pencarian produk — {{ $store->nama_toko ?? 'OpenCom' }}</title>
        <link rel="stylesheet" href="{{ asset('css/search.css') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontName) }}:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body>
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
        <a class="skip" href="#results">Lewati ke hasil pencarian</a>
        <header class="bg-customNav shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    
                    <!-- Logo Dinamis -->
                    <div class="flex-shrink-0 flex items-center cursor-pointer">
                        <a href="{{ route('home') }}">
                            @if($konfig && $konfig->alamat_logo)
                                <!-- Menampilkan Gambar Logo jika URL diisi -->
                                <img src="{{ $konfig->alamat_logo }}" alt="{{ $namaToko }}" class="h-8 w-auto object-contain">
                            @else
                                <!-- Fallback Teks jika Logo kosong -->
                                <span class="text-2xl font-bold text-primary tracking-tight">{{ $namaToko }}</span>
                            @endif
                        </a>
                    </div>
                    <!-- Nav Menu (Desktop) -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-900 font-medium hover:text-primary transition">Beranda</a>
                    </nav>
                    <!-- Cart & Login Button -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-gray-900 transition relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            @if(isset($cartCount) && $cartCount > 0)
                                <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
                            @endif
                        </a>

                        @if(session('user_logged_in'))
                        <!-- Area Dropdown Akun -->
                        <div class="relative">
                            <button onclick="toggleUserDropdown()" type="button" class="flex items-center focus:outline-none">
                                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg uppercase shadow-sm border border-indigo-200 hover:opacity-90 transition">
                                    {{ strtoupper(substr(session('username'), 0, 1)) }}
                                </div>
                            </button>

                            <!-- Menu Dropdown -->
                            <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs text-gray-400">Masuk sebagai</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ session('username') }}</p>
                                </div>
                                
                                <!-- OPSI BARU: Pesanan Saya -->
                                <a href="{{ route('orders.index') }}" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"></path></svg>
                                    Pesanan Saya
                                </a>

                                <!-- Logout -->
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
                        <div class="hidden md:flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">Masuk</a>
                            <a href="{{ route('register') }}" class="text-black px-4 py-2 rounded-lg font-medium hover:bg-black hover:text-white border-2 border-black transition">Daftar</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>
        <main class="container">
            <div class="intro"><p class="text-primary">KATALOG TOKO</p><h1>Temukan produk pilihan Anda</h1><p>Cari nama produk atau kategori, lalu sesuaikan pilihan Anda.</p></div>
            @if ($errors->any())
                <div class="notice" role="alert">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('search') }}" method="GET" role="search" class="search-panel">
                <label for="q">Kata pencarian</label>
                <div class="search-row">
                    <input id="q" name="q" type="search" value="{{ $result['original'] }}" maxlength="200" placeholder="Nama produk atau kategori" aria-describedby="search-hint">
                    <button type="submit">Cari produk</button>
                </div>
                <p id="search-hint" class="hint">Gunakan beberapa kata yang menggambarkan produk Anda.</p>
                <div class="filters">
                    <div><label for="category">Kategori</label><select id="category" name="category"><option value="">Semua kategori</option>@foreach ($categories as $category)<option value="{{ $category->category_id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->category_id)>{{ $category->nama_kategori }}</option>@endforeach</select></div>
                    <div><label for="min_price">Harga minimum (Rp)</label><input id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ $filters['min_price'] ?? '' }}" placeholder="0"></div>
                    <div><label for="max_price">Harga maksimum (Rp)</label><input id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ $filters['max_price'] ?? '' }}" placeholder="Tanpa batas"></div>
                    <label class="checkbox"><input type="checkbox" name="in_stock" value="1" @checked(!empty($filters['in_stock']))> Stok tersedia</label>
                </div>
                @if(request()->boolean('original'))<input type="hidden" name="original" value="1">@endif
            </form>
            @if ($result['corrected'])
                <div class="notice" role="status">
                    Menampilkan hasil untuk <strong>{{ $result['effective'] }}</strong>.
                    <a href="{{ route('search', array_merge($filters, ['q' => $result['original'], 'original' => 1])) }}">Cari tetap dengan “{{ $result['original'] }}”</a>.
                </div>
            @elseif(count($result['suggestions']))
                <div class="notice" role="status">Mungkin yang Anda maksud:
                    @foreach($result['suggestions'] as $suggestion)
                        <a class="suggestion" href="{{ route('search', array_merge($filters, ['q' => $suggestion, 'original' => 1])) }}">{{ $suggestion }}</a>
                    @endforeach
                </div>
            @endif
            <section id="results" aria-labelledby="result-heading">
                <div class="results-heading"><h2 id="result-heading">Hasil pencarian</h2><p>{{ $result['results']->total() }} produk @if($result['effective'] !== '') untuk “{{ $result['effective'] }}” @endif</p></div>
                @if($result['results']->count())
                    <div class="product-grid">
                        @foreach($result['results'] as $hit)
                            @php($product = $products->get($hit->product_key))
                            @if($product)
                            <article class="product-card">
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
                            @endif
                        @endforeach
                    </div>
                    @if($result['results']->hasPages())
                        <nav class="pagination" aria-label="Halaman hasil pencarian">
                            @if($result['results']->previousPageUrl())<a href="{{ $result['results']->previousPageUrl() }}" rel="prev">← Sebelumnya</a>@endif
                            <span>Halaman {{ $result['results']->currentPage() }} dari {{ $result['results']->lastPage() }}</span>
                            @if($result['results']->nextPageUrl())<a href="{{ $result['results']->nextPageUrl() }}" rel="next">Berikutnya →</a>@endif
                        </nav>
                    @endif
                @else
                    <div class="empty"><h3>{{ trim($result['original']) === '' ? 'Mulai pencarian Anda' : 'Produk tidak ditemukan' }}</h3><p>{{ trim($result['original']) === '' ? 'Masukkan nama produk atau kategori pada kolom pencarian.' : 'Coba kata yang lebih sederhana atau ubah filter pencarian.' }}</p><a href="{{ route('home') }}">Lihat katalog toko</a></div>
                @endif
            </section>
        </main>
        <footer class="container footer">{{ $store->nama_toko ?? 'OpenCom' }} · Belanja sesuai kebutuhan Anda.</footer>
    </body>
</html>
