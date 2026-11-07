@extends('layouts.admin')

@section('title', 'Daftar Pesanan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Kelola Pesanan Pelanggan</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 text-sm text-gray-500 bg-gray-50">
                    <th class="p-4 font-semibold">ID / Blockchain</th>
                    <th class="p-4 font-semibold">Pembeli & Alamat</th>
                    <th class="p-4 font-semibold">Rincian Barang & Biaya</th>
                    <th class="p-4 font-semibold">Status & Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    
                    <!-- KOLOM 1: ID Pesanan & Hash Blockchain -->
                    <td class="p-4 align-top">
                        <div class="font-bold text-gray-900 mb-1">#{{ substr($order->order_id, 0, 8) }}</div>
                        <div class="text-xs text-gray-500 mt-2">ID Komerce:</div>
                        <div class="font-mono text-xs text-indigo-600 truncate max-w-[120px]">{{ $order->order_another_id ?? '-' }}</div>
                        
                        <div class="text-xs text-gray-500 mt-2">Hash (Block):</div>
                        <div class="font-mono text-xs text-green-600 truncate max-w-[120px]" title="{{ $order->order_hash }}">
                            {{ $order->order_hash ? substr($order->order_hash, 0, 10).'...' : 'Belum di-hash' }}
                        </div>
                    </td>

                    <!-- KOLOM 2: Pembeli & Alamat -->
                    <td class="p-4 align-top">
                        <div class="font-semibold text-gray-900">{{ $order->pengguna->nama_user ?? 'User Tidak Diketahui' }}</div>
                        <div class="text-xs text-gray-500 mb-2">{{ $order->pengguna->email ?? '-' }} | {{ $order->pengguna->no_telepon ?? '-' }}</div>
                        
                        <!-- Asumsi Anda menyimpan alamat di tabel pengguna atau pesanan -->
                        <div class="text-xs text-gray-600 bg-gray-100 p-2 rounded leading-relaxed max-w-[200px]">
                            <span class="font-semibold block mb-1">Alamat Pengiriman:</span>
                            {{ $order->pengguna->alamat ?? 'Alamat belum diatur' }}
                        </div>
                    </td>

                    <!-- KOLOM 3: Rincian Barang & Kalkulasi Biaya -->
                    <td class="p-4 align-top">
                        <!-- Daftar Barang -->
                        <div class="mb-3 max-h-24 overflow-y-auto pr-2 scrollbar-thin">
                            @php 
                                $subtotalBarang = 0; 
                            @endphp
                            
                            @if($order->item_pesanan && $order->item_pesanan->count() > 0)
                                <ul class="space-y-1">
                                @foreach($order->item_pesanan as $item)
                                    @php 
                                        $subtotalItem = $item->jumlah * $item->harga_satuan; 
                                        $subtotalBarang += $subtotalItem;
                                    @endphp
                                    <li class="flex justify-between text-xs border-b border-gray-100 pb-1">
                                        <span class="text-gray-700 truncate w-32" title="{{ $item->produk->nama_produk ?? 'Produk' }}">{{ $item->produk->nama_produk ?? 'Produk' }} (x{{ $item->jumlah }})</span>
                                        <span class="font-medium text-gray-900">Rp {{ number_format($subtotalItem, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                                </ul>
                            @else
                                <span class="text-xs text-gray-400 italic">Detail item tidak ditemukan</span>
                            @endif
                        </div>

                        <!-- Kalkulasi Harga -->
                        <div class="bg-indigo-50/50 p-2 rounded border border-indigo-50 space-y-1">
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Subtotal Barang:</span>
                                <span>Rp {{ number_format($subtotalBarang, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>Biaya Ongkir:</span>
                                @php $ongkir = $order->total_harga - $subtotalBarang; @endphp
                                <span>Rp {{ number_format($ongkir > 0 ? $ongkir : 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-primary border-t border-indigo-100 pt-1 mt-1">
                                <span>Total Bayar:</span>
                                <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- KOLOM 4: Status, Update & Resi -->
                    <td class="p-4 align-top">
                        <!-- Update Status -->
                        <form action="{{ route('admin.dashboard.orders.update_status', $order->order_id) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PUT')
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Ubah Status</label>
                            <div class="flex items-center space-x-2">
                                <select name="status_pesanan" class="text-xs border-gray-300 rounded focus:ring-primary focus:border-primary py-1.5 w-full">
                                    <option value="menunggu_pembayaran" {{ $order->status_pesanan === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Bayar</option>
                                    <option value="diproses" {{ $order->status_pesanan === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="dikirim" {{ $order->status_pesanan === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="selesai" {{ $order->status_pesanan === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="dibatalkan" {{ $order->status_pesanan === 'dibatalkan' ? 'selected' : '' }} class="text-red-600 font-bold">Batal (Revoke)</option>
                                </select>
                                <button type="submit" class="bg-gray-800 hover:bg-black text-white py-1.5 px-3 rounded text-xs transition">
                                    Set
                                </button>
                            </div>
                        </form>

                        <!-- Input Resi (Muncul jika diproses/dikirim) -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Nomor Resi</label>
                            @if($order->status_pesanan == 'diproses' || $order->status_pesanan == 'dikirim')
                                <form action="{{ route('admin.dashboard.orders.update_resi', $order->order_id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="nomor_resi" value="{{ $order->nomor_resi }}" placeholder="Input Resi" required class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs outline-none focus:border-primary">
                                    <button type="submit" class="bg-primary text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-indigo-700 transition">
                                        Simpan
                                    </button>
                                </form>
                            @else
                                <div class="bg-gray-100 px-3 py-1.5 rounded text-xs text-gray-500 truncate font-mono">
                                    {{ $order->nomor_resi ?: 'Belum ada resi' }}
                                </div>
                            @endif
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection