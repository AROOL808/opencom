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
                <tr class="border-b border-gray-200 text-sm text-gray-500">
                    <th class="p-3">ID Pesanan</th>
                    <th class="p-3">Pembeli</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Nomor Resi / Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($orders as $order)
                <tr>
                    <td class="p-3 font-semibold">#{{ substr($order->order_id, 0, 8) }}</td>
                    <td class="p-3">{{ $order->pengguna->nama_user ?? 'User' }}</td>
                    <td class="p-3 font-bold text-primary">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                    <td class="p-3">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                            {{ $order->status_pesanan == 'menunggu_pembayaran' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $order->status_pesanan == 'diproses' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $order->status_pesanan == 'dikirim' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $order->status_pesanan == 'selesai' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($order->status_pesanan == 'diproses' || $order->status_pesanan == 'dikirim')
                            <form action="{{ route('admin.dashboard.orders.update_resi', $order->order_id) }}" method="POST" class="flex items-center gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="nomor_resi" value="{{ $order->nomor_resi }}" placeholder="Input No. Resi" required class="px-3 py-1 border border-gray-300 rounded-lg text-xs outline-none focus:border-primary">
                                <button type="submit" class="bg-primary text-white px-3 py-1 rounded-lg text-xs font-medium hover:bg-indigo-700 transition">
                                    Simpan
                                </button>
                            </form>
                        @else
                            <span class="text-gray-400 text-xs">{{ $order->nomor_resi ?: '-' }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection