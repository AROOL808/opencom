@extends('layouts.admin')

@section('title', 'Pengaturan Profil & Tampilan Toko')

@section('content')
<div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm mb-6 font-medium">
            {{ session('success') }}
        </div>
    @endif
    
    <form action="{{ route('admin.dashboard.profile.update') }}" method="POST" class="space-y-10">
        @csrf
        @method('PUT')
        
        <!-- BAGIAN 1: Informasi Dasar -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Informasi Dasar Toko</h2>
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $konfigurasi->nama_toko) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Masukkan nama toko Anda">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap Toko / Gudang</label>
                    <textarea name="alamat_toko" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Jl. Contoh Alamat No. 123...">{{ old('alamat_toko', $konfigurasi->alamat_toko) }}</textarea>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: Tampilan Visual -->
        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Preferensi Tampilan (UI)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- URL Logo -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">URL Logo Toko (Opsional)</label>
                    <input type="url" name="alamat_logo" value="{{ old('alamat_logo', $konfigurasi->alamat_logo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="https://contoh.com/logo.png">
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika ingin menggunakan teks nama toko.</p>
                </div>

                <!-- Font Style -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Font (Google Fonts)</label>
                    <input type="text" name="alamat_font" value="{{ old('alamat_font', $konfigurasi->alamat_font) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition" placeholder="Contoh: Poppins, Roboto, atau Montserrat">
                </div>

                <!-- Warna Tema / Item (Tombol, Badge) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Utama (Tombol & Aksen)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="warna_item" value="{{ old('warna_item', $konfigurasi->warna_item ?? '#4F46E5') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        <span class="text-sm text-gray-500">Pilih warna dominan</span>
                    </div>
                </div>

                <!-- Warna Navigator (Header) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Navbar (Header)</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="warna_navigator" value="{{ old('warna_navigator', $konfigurasi->warna_navigator ?? '#ffffff') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        <span class="text-sm text-gray-500">Pilih warna header atas</span>
                    </div>
                </div>

                <!-- Warna Background Body -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Latar Belakang Web</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="warna_background" value="{{ old('warna_background', $konfigurasi->warna_background ?? '#f9fafb') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        <span class="text-sm text-gray-500">Pilih warna dasar website</span>
                    </div>
                </div>

                <!-- Warna Teks Utama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Teks Utama</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="warna_font" value="{{ old('warna_font', $konfigurasi->warna_font ?? '#1f2937') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        <span class="text-sm text-gray-500">Pilih warna tulisan paragraf</span>
                    </div>
                </div>

            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition shadow-md">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection