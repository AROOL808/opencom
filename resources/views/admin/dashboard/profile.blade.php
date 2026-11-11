@extends('layouts.admin')

@section('title', 'Pengaturan Profil & Tampilan Toko')

@section('content')
<div class="space-y-8">
    <!-- KOTAK 1: PROFIL & UI -->
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
                        <input type="text" name="nama_toko" value="{{ old('nama_toko', $konfigurasi->nama_toko) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap Toko / Gudang</label>
                        <textarea name="alamat_toko" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition">{{ old('alamat_toko', $konfigurasi->alamat_toko) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 2: Tampilan Visual -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Preferensi Tampilan (UI)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Logo Toko (Opsional)</label>
                        <input type="url" name="alamat_logo" value="{{ old('alamat_logo', $konfigurasi->alamat_logo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Font (Google Fonts)</label>
                        <input type="text" name="alamat_font" value="{{ old('alamat_font', $konfigurasi->alamat_font) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Utama (Tombol & Aksen)</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="warna_item" value="{{ old('warna_item', $konfigurasi->warna_item ?? '#4F46E5') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Navbar (Header)</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="warna_navigator" value="{{ old('warna_navigator', $konfigurasi->warna_navigator ?? '#ffffff') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Latar Belakang Web</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="warna_background" value="{{ old('warna_background', $konfigurasi->warna_background ?? '#f9fafb') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Teks Utama</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="warna_font" value="{{ old('warna_font', $konfigurasi->warna_font ?? '#1f2937') }}" class="w-12 h-12 rounded cursor-pointer border-0 p-0">
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

    <!-- KOTAK 2: PENGATURAN HERO SECTION -->
    <div class="max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Hero Banner (Slider)</h2>
            <button onclick="document.getElementById('modalAddHero').classList.remove('hidden')" class="bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                + Tambah Banner
            </button>
        </div>

        <!-- Tabel Daftar Hero -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-100">
                        <th class="p-3">Urutan</th>
                        <th class="p-3">Gambar</th>
                        <th class="p-3">Judul & CTA</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($heroes as $hero)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-bold">{{ $hero->urutan }}</td>
                        <td class="p-3">
                           @php
                                // Cek apakah alamat_foto diawali dengan http:// atau https://
                                $isUrl = Str::startsWith($hero->alamat_foto, ['http://', 'https://']);
                                $imgSource = $isUrl ? $hero->alamat_foto : asset('storage/heroes/' . $hero->alamat_foto);
                            @endphp
                            <img src="{{ $imgSource }}" alt="Hero" class="h-16 w-24 object-cover rounded shadow-sm border border-gray-200">
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-gray-900">{{ $hero->title }}</div>
                            <div class="text-xs text-gray-500 mt-1">Tombol: {{ $hero->cta }}</div>
                        </td>
                        <td class="p-3">
                            <form action="{{ route('admin.dashboard.hero.destroy', $hero->hero_item_id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold px-2 py-1 bg-red-50 rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-400 italic">Belum ada banner hero yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH HERO ================= -->
<div id="modalAddHero" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white p-6 rounded-2xl w-full max-w-lg shadow-xl transform transition-all relative max-h-[90vh] overflow-y-auto">
        
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h3 class="text-lg font-bold text-gray-900">Tambah Hero Banner</h3>
            <button type="button" onclick="document.getElementById('modalAddHero').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- INGAT: Gunakan enctype="multipart/form-data" untuk upload file gambar -->
        <form action="{{ route('admin.dashboard.hero.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Banner (Title)</label>
                <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Singkat</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Teks Tombol (CTA)</label>
                    <input type="text" name="cta" placeholder="Cth: Beli Sekarang" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="urutan" value="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Link Redirect</label>
                    <input type="text" name="redirect_link" placeholder="#produk" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-gray-100 pt-4 mt-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Upload File (Prioritas)</label>
                    <input type="file" name="foto_file" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Atau Gunakan URL Gambar</label>
                    <input type="url" name="foto_url" placeholder="https://contoh.com/banner.png" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div class="col-span-full">
                    <p class="text-xs text-gray-500 italic">*Pilih salah satu. Jika Anda mengisi keduanya, sistem akan memprioritaskan File Upload.</p>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg shadow-md transition">
                    Simpan Banner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection