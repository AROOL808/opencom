@extends('layouts.admin')

@section('title', 'Pengaturan Profil Toko')

@section('content')
<div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <h2 class="text-lg font-bold text-gray-900 mb-6">Informasi Dasar Toko</h2>
    
    <form action="#" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Masukkan nama toko Anda" value="OpenCom Store">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Publik (Untuk Pelanggan)</label>
            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="cs@tokoanda.com">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap Toko / Gudang</label>
            <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Jl. Contoh Alamat No. 123..."></textarea>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end">
            <button type="button" class="bg-primary hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection