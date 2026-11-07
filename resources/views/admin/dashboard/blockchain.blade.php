@extends('layouts.admin')
@section('title', 'Blockchain & Audit Log')

@section('content')
<div class="space-y-6">

    <!-- Status Verifikasi Integritas (The Verification System) -->
    @if($verification['is_valid'])
        <div class="bg-green-50 border border-green-200 p-4 rounded-xl flex items-center shadow-sm">
            <svg class="w-8 h-8 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <div>
                <h3 class="font-bold text-green-800">Integritas Rantai Aman</h3>
                <p class="text-sm text-green-700">Semua hash sinkron. Tidak ada manipulasi data pada database pesanan terdeteksi.</p>
            </div>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 p-4 rounded-xl flex items-center shadow-sm">
            <svg class="w-8 h-8 text-red-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h3 class="font-bold text-red-800">PERINGATAN MANIPULASI TERDETEKSI!</h3>
                <p class="text-sm text-red-700">Terdeteksi modifikasi ilegal pada Block ID: {{ implode(', ', $verification['corrupted_blocks']) }}. Segera audit database Anda.</p>
            </div>
        </div>
    @endif

    <!-- Kalkulasi Keuangan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Pendapatan Bersih (Valid)</p>
            <h2 class="text-3xl font-extrabold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Nilai Di-Revoke (Dibatalkan)</p>
            <h2 class="text-3xl font-bold text-red-600">Rp {{ number_format($revokedAmount, 0, ',', '.') }}</h2>
        </div>
    </div>

    <!-- Tabel Block -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800">Daftar Non-Repudiation Block</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Block ID</th>
                        <th class="px-6 py-3 font-semibold">Block Hash</th>
                        <th class="px-6 py-3 font-semibold">Previous Hash</th>
                        <th class="px-6 py-3 font-semibold">ID Pesanan (Batch)</th>
                        <th class="px-6 py-3 font-semibold">Waktu Pembuatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($blocks as $block)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold">#{{ $block->block_id }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-indigo-600 truncate max-w-[150px]" title="{{ $block->block_hash }}">
                            {{ substr($block->block_hash, 0, 15) }}...
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 truncate max-w-[150px]" title="{{ $block->previous_hash }}">
                            {{ $block->previous_hash === '00000000000000000000000000000000' ? 'Genesis' : substr($block->previous_hash, 0, 15) . '...' }}
                        </td>
                        <td class="px-6 py-4">
                            @foreach($block->order_ids as $oid)
                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded border border-gray-200">ID:{{ $oid }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $block->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $blocks->links() }}
        </div>
    </div>
</div>
@endsection