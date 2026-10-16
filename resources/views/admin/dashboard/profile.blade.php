@extends('layouts.admin')

@section('title', 'Pengaturan Profil Toko')

@section('content')
<div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <h2 class="text-lg font-bold text-gray-900 mb-6">Informasi Dasar Toko</h2>
    
    <!-- Pesan Sukses -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm mb-6 font-medium">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Form Action & Method -->
    <form action="{{ route('admin.dashboard.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
            <!-- Tambahkan tag name="" dan value="" -->
            <input type="text" name="nama_toko" value="{{ old('nama_toko', $konfigurasi->nama_toko) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Masukkan nama toko Anda">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap Toko / Gudang</label>
            <!-- Tambahkan tag name="" dan isi textarea -->
            <textarea name="alamat_toko" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Jl. Contoh Alamat No. 123...">{{ old('alamat_toko', $konfigurasi->alamat_toko) }}</textarea>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <!-- Ubah type="button" menjadi type="submit" -->
            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection