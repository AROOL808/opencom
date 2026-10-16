<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Belanja - OpenCom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#4F46E5', secondary: '#111827' } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex flex-col min-h-screen">

    <!-- Header / Navbar Sederhana -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">OpenCom.</a>
            <a href="{{ route('home') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Kembali Berbelanja</a>
        </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        <h1 class="text-2xl font-bold mb-8">Keranjang Belanja Anda</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(count($cartItems) > 0)
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- SISI KIRI: Daftar Item -->
                <div class="lg:w-2/3 space-y-4">
                    @foreach($cartItems as $item)
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-6">
                            
                            <!-- Foto Produk -->
                            <img src="{{ asset('storage/products/' . $item->product->alamat_foto) }}" alt="{{ $item->product->nama_produk }}" class="w-24 h-24 object-cover rounded-xl bg-gray-100">
                            
                            <!-- Info Produk -->
                            <div class="flex-grow text-center sm:text-left">
                                <h3 class="text-lg font-bold text-gray-900">{{ $item->product->nama_produk }}</h3>
                                <p class="text-primary font-semibold mt-1">Rp {{ number_format($item->product->harga, 0, ',', '.') }}</p>
                            </div>

                            <!-- Kontrol Kuantitas & Hapus -->
                            <div class="flex flex-col items-center sm:items-end gap-3">
                                
                                <!-- Tombol Plus Minus -->
                                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                    <form action="{{ route('cart.update', $item->cart_item_id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="decrement">
                                        <button type="submit" class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 hover:text-black font-bold transition">-</button>
                                    </form>
                                    
                                    <span class="px-4 py-1.5 font-semibold text-sm bg-white border-x border-gray-200">{{ $item->jumlah }}</span>
                                    
                                    <form action="{{ route('cart.update', $item->cart_item_id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="increment">
                                        <button type="submit" class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 hover:text-black font-bold transition">+</button>
                                    </form>
                                </div>

                                <!-- Total Harga per Item & Tombol Hapus -->
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-sm font-bold text-gray-800">
                                        Rp {{ number_format($item->product->harga * $item->jumlah, 0, ',', '.') }}
                                    </span>
                                    
                                    <form action="{{ route('cart.destroy', $item->cart_item_id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- SISI KANAN: Ringkasan Pembayaran -->
                <div class="lg:w-1/3">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Belanja</h2>
                        
                        <form action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4 text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <p class="font-bold text-gray-800 mb-1">Dikirim ke:</p>
                                <p>{{ $pengguna->alamat }}</p>
                            </div>

                            <!-- TAHAP 1: Pilih Kurir -->
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kurir</label>
                                <select id="courierSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                                    <option value="" disabled selected>-- Pilih Kurir --</option>
                                    <option value="jne">JNE</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="sicepat">SiCepat</option>
                                </select>
                            </div>

                            <!-- TAHAP 2: Pilih Layanan (Muncul otomatis dari API) -->
                            <div class="mb-4 hidden" id="serviceWrapper">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Layanan Tersedia</label>
                                <select id="serviceSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary">
                                    <option value="" disabled selected>-- Pilih Layanan --</option>
                                </select>
                            </div>
                            
                            <!-- Hidden input untuk disubmit -->
                            <input type="hidden" name="layanan_ongkir" id="hiddenLayananOngkir">

                            <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                                <span>Subtotal Barang</span>
                                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                                <span>Ongkos Kirim</span>
                                <span id="displayOngkir" class="font-semibold text-gray-900">Rp 0</span>
                            </div>

                            <div class="border-t border-gray-100 my-4"></div>
                            
                            <div class="flex justify-between items-center mb-8">
                                <span class="text-base font-bold text-gray-900">Total Tagihan</span>
                                <span class="text-xl font-bold text-primary" id="displayTotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            <button type="submit" id="btnCheckout" class="w-full block text-center bg-primary hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition duration-300 shadow-md opacity-50 cursor-not-allowed" disabled>
                                Lengkapi Pengiriman
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Jika Keranjang Kosong -->
            <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Keranjang Anda Kosong</h2>
                <p class="text-gray-500 mb-6">Sepertinya Anda belum menambahkan produk apapun.</p>
                <a href="{{ route('home') }}" class="inline-block bg-primary hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-300">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </main>
    <script>
    const courierSelect = document.getElementById('courierSelect');
    const serviceWrapper = document.getElementById('serviceWrapper');
    const serviceSelect = document.getElementById('serviceSelect');
    const btnCheckout = document.getElementById('btnCheckout');
    
    const hiddenLayananOngkir = document.getElementById('hiddenLayananOngkir');
    const displayOngkir = document.getElementById('displayOngkir');
    const displayTotal = document.getElementById('displayTotal');
    const subtotalBarang = {{ $total }};

    // 1. Saat Kurir Dipilih (Tarik Data dari API)
    courierSelect.addEventListener('change', function() {
        let courier = this.value;
        
        // Reset tampilan saat kurir diganti
        displayOngkir.innerText = "Mencari layanan...";
        displayTotal.innerText = 'Rp ' + subtotalBarang.toLocaleString('id-ID');
        serviceWrapper.classList.add('hidden');
        serviceSelect.innerHTML = '<option value="" disabled selected>-- Pilih Layanan --</option>';
        
        btnCheckout.disabled = true;
        btnCheckout.classList.add('opacity-50', 'cursor-not-allowed');
        btnCheckout.innerText = "Lengkapi Pengiriman";

        fetch(`{{ route('api.calculate_cost') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ courier: courier })
        })
        .then(res => res.json())
        .then(data => {
            if(data.error) {
                alert(data.error);
                displayOngkir.innerText = "Gagal";
                return;
            }

            if(data.data && data.data.length > 0) {
                displayOngkir.innerText = "Pilih Layanan di Atas";
                
                // Masukkan data layanan ke dalam Dropdown ke-2
                data.data.forEach(item => {
                    let option = document.createElement('option');
                    // Format value yang disubmit: "NAMA_LAYANAN|HARGA" (contoh: "REG|45000")
                    option.value = `${item.service}|${item.cost}`;
                    // Tampilan tulisan di dropdown
                    option.text = `${item.service} - Rp ${item.cost.toLocaleString('id-ID')} (Est. ${item.etd})`;
                    serviceSelect.appendChild(option);
                });

                // Tampilkan form layanan
                serviceWrapper.classList.remove('hidden');
            } else {
                displayOngkir.innerText = "Kurir tidak tersedia";
            }
        })
        .catch(err => {
            console.error('Error:', err);
            displayOngkir.innerText = "Terjadi Kesalahan";
        });
    });

    // 2. Saat Layanan Dipilih (Hitung Total & Buka Tombol Bayar)
    serviceSelect.addEventListener('change', function() {
        let selectedValue = this.value; // Contoh isi: "REG|45000"
        if(!selectedValue) return;

        let arrData = selectedValue.split('|');
        let ongkir = parseInt(arrData[1]);
        
        // Simpan ke hidden input untuk backend OrderController
        hiddenLayananOngkir.value = selectedValue;

        // Ubah tampilan angka
        let totalAkhir = subtotalBarang + ongkir;
        displayOngkir.innerText = 'Rp ' + ongkir.toLocaleString('id-ID');
        displayTotal.innerText = 'Rp ' + totalAkhir.toLocaleString('id-ID');

        // Aktifkan Tombol
        btnCheckout.disabled = false;
        btnCheckout.classList.remove('opacity-50', 'cursor-not-allowed');
        btnCheckout.innerText = "Bayar Sekarang";
    });
    </script>
</body>
</html>