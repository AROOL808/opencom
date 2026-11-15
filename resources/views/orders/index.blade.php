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
    <title>Pesanan Saya - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex flex-col min-h-screen">

    <!-- Header / Navbar -->
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

    <main class="flex-grow max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        <h1 class="text-2xl font-bold mb-8">Daftar Pesanan Saya</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @forelse($orders as $order)
            <!-- Kartu Pesanan -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 hover:shadow-md transition">
                
                <!-- Bagian Atas: Info Pesanan & Status -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-100 pb-4 mb-4 gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"></path></svg>
                            <span class="text-sm text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($order->tanggal_pesanan)->format('d M Y, H:i') }}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg">Order #{{ strtoupper(substr($order->order_id, 0, 8)) }}</h3>
                    </div>
                    
                    <!-- Badge Status Dinamis -->
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $order->status_pesanan == 'menunggu_pembayaran' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $order->status_pesanan == 'diproses' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $order->status_pesanan == 'dikirim' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ $order->status_pesanan == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $order->status_pesanan == 'dibatalkan' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ str_replace('_', ' ', $order->status_pesanan) }}
                    </span>
                </div>

                <!-- Bagian Tengah: Daftar Item Produk -->
                <div class="space-y-4">
                    @foreach($order->item_pesanan as $item)
                        <div class="flex items-center gap-4">
                            <!-- Karena nama model 'produk' di ItemPesanan, panggil $item->produk -->
                            <img src="{{ asset('storage/products/' . ($item->produk->alamat_foto ?? 'default.png')) }}" alt="Foto Produk" class="w-16 h-16 object-cover rounded-lg bg-gray-100">
                            <div class="flex-grow">
                                <h4 class="font-semibold text-gray-900">{{ $item->produk->nama_produk ?? 'Produk Dihapus' }}</h4>
                                <p class="text-sm text-gray-500">{{ $item->jumlah }} barang x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right font-bold text-gray-700">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

               <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-center sm:text-left">
                        <p class="text-sm text-gray-500 mb-1">Total Tagihan</p>
                        <p class="text-xl font-extrabold text-primary">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center sm:justify-end gap-3">
                        
                        <!-- Jika ada resi, tampilkan resinya -->
                        @if($order->nomor_resi)
                            <div class="bg-gray-50 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm flex items-center">
                                <span class="mr-2">No. Resi:</span>
                                <strong class="text-gray-900">{{ $order->nomor_resi }}</strong>
                            </div>
                        @endif

                        <!-- Tombol Lacak Paket muncul jika paket sudah dikirim -->
                        @if($order->status_pesanan == 'dikirim' && $order->nomor_resi)
                           
                        @endif

                        <!-- Jika status masih menunggu pembayaran, tampilkan tombol bayar -->
                        @if($order->status_pesanan == 'menunggu_pembayaran')
                            <a href="{{ route('orders.payment', $order->order_id) }}" class="bg-primary hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-lg text-sm shadow-md transition duration-200">
                                Bayar Sekarang
                            </a>
                        @endif

                    </div>
                </div>

                <!-- Panel Hasil Pelacakan Resi (Tersembunyi secara default) -->
                @if($order->status_pesanan == 'dikirim' && $order->nomor_resi)
                    <div id="tracking-panel-{{ $order->order_id }}" class="hidden mt-4 pt-4 border-t border-dashed border-gray-200 bg-gray-50/70 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                                Status Keberadaan Paket
                            </h4>
                            <button type="button" onclick="document.getElementById('tracking-panel-{{ $order->order_id }}').classList.add('hidden')" class="text-xs text-gray-400 hover:text-gray-600">
                                Tutup
                            </button>
                        </div>
                        <div id="tracking-content-{{ $order->order_id }}" class="text-sm text-gray-600">
                            <!-- Diisi otomatis oleh JavaScript -->
                        </div>
                    </div>
                @endif

            </div>

            </div>
        @empty
            <!-- Tampilan Jika Belum Ada Pesanan Sama Sekali -->
            <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Pesanan</h2>
                <p class="text-gray-500 mb-6">Anda belum pernah melakukan transaksi apa pun. Ayo mulai penuhi keranjangmu!</p>
                <a href="{{ route('home') }}" class="inline-block bg-primary hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-300">
                    Mulai Belanja
                </a>
            </div>
        @endforelse

    </main>
    <script>
        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            if (menu) menu.classList.toggle('hidden');
        }

        async function cekResi(orderId, url) {
            const panel = document.getElementById(`tracking-panel-${orderId}`);
            const content = document.getElementById(`tracking-content-${orderId}`);

            panel.classList.remove('hidden');
            content.innerHTML = `
                <div class="flex items-center gap-2 text-purple-600 py-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memeriksa posisi paket dari kurir...</span>
                </div>
            `;

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const res = await response.json();

                if (!response.ok || res.status === 'error') {
                    content.innerHTML = `<p class="text-red-500 text-xs">${res.message || 'Gagal mengambil data resi.'}</p>`;
                    return;
                }

                // Menyesuaikan format respons API RajaOngkir / Komerce
                const raw = res.tracking || {};
                const result = raw.data || (raw.rajaongkir && raw.rajaongkir.result) || raw;
                const manifest = result.manifest || [];
                const statusInfo = result.delivery_status || result.summary || {};

                if (manifest.length === 0) {
                    const errMsg = (raw.meta && raw.meta.message) || (raw.rajaongkir && raw.rajaongkir.status && raw.rajaongkir.status.description) || 'Data perjalanan paket belum tersedia atau nomor resi tidak ditemukan di sistem kurir.';
                    content.innerHTML = `<p class="text-amber-600 bg-amber-50 border border-amber-200 p-3 rounded-lg text-xs">${errMsg}</p>`;
                    return;
                }

                // Posisi terbaru (elemen pertama dari manifest)
                const posisiTerakhir = manifest[0];

                let html = `
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-3 mb-4">
                        <p class="text-xs text-purple-600 font-semibold uppercase">Posisi Terkini (${statusInfo.status || 'Dalam Pengiriman'})</p>
                        <p class="font-bold text-gray-900 mt-0.5">${posisiTerakhir.manifest_description || '-'}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            ${posisiTerakhir.city_name ? posisiTerakhir.city_name + ' • ' : ''}
                            ${posisiTerakhir.manifest_date || ''} ${posisiTerakhir.manifest_time || ''}
                        </p>
                    </div>
                    <div class="space-y-3 border-l-2 border-purple-200 ml-2 pl-4 max-h-60 overflow-y-auto">
                `;

                manifest.forEach((item, index) => {
                    const isLatest = index === 0;
                    html += `
                        <div class="relative">
                            <span class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full ${isLatest ? 'bg-purple-600 ring-4 ring-purple-100' : 'bg-gray-300'}"></span>
                            <p class="text-xs font-semibold ${isLatest ? 'text-gray-900' : 'text-gray-600'}">${item.manifest_description || '-'}</p>
                            <p class="text-[11px] text-gray-400">
                                ${item.city_name ? item.city_name + ' - ' : ''}${item.manifest_date || ''} ${item.manifest_time || ''}
                            </p>
                        </div>
                    `;
                });

                html += `</div>`;
                content.innerHTML = html;

            } catch (error) {
                content.innerHTML = `<p class="text-red-500 text-xs">Terjadi kesalahan jaringan saat menghubungi layanan pelacakan.</p>`;
            }
        }
    </script>
</body>
</html>