<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Saya - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#4F46E5', secondary: '#111827' } } } }</script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex flex-col min-h-screen">

    <!-- Header / Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">OpenCom.</a>
            <a href="{{ route('home') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition">Kembali Berbelanja</a>
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

                <!-- Bagian Bawah: Total & Resi / Aksi -->
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
                            
                            <!-- (Opsional) Tombol Lacak Resi jika Anda sudah buat rutenya -->
                            <!-- <a href="{{ route('orders.track', $order->order_id) }}" class="bg-white border border-primary text-primary hover:bg-primary hover:text-white font-medium px-4 py-2 rounded-lg text-sm transition">Lacak Pengiriman</a> -->
                        @endif

                        <!-- Jika status masih menunggu pembayaran, tampilkan tombol bayar -->
                        @if($order->status_pesanan == 'menunggu_pembayaran')
                            <!-- Jika Anda sudah buat rute payment dari jawaban sebelumnya -->
                            <a href="{{ route('orders.payment', $order->order_id) }}" class="bg-primary hover:bg-indigo-700 text-white font-medium px-6 py-2 rounded-lg text-sm shadow-md transition duration-200">
                                Bayar Sekarang
                            </a>
                        @endif

                    </div>
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
</body>
</html>