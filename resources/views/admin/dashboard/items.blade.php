@extends('layouts.admin')

@section('title', 'Manajemen Item & Kategori')

@section('content')
<div class="space-y-6">

    <!-- Notifikasi Sukses -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Error Validasi -->
    @if($errors->any())
    <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <ul class="list-disc ml-5 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- BAGIAN 1: KATEGORI -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-900">Kategori Produk</h2>
            <button onclick="toggleModal('modalKategori')" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-black transition">+ Tambah Kategori</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                        <th class="p-3 font-medium">Nama Kategori</th>
                        <th class="p-3 font-medium">Deskripsi</th>
                        <th class="p-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($categories as $cat)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-900">{{ $cat->nama_kategori }}</td>
                        <td class="p-3 text-gray-500">{{ Str::limit($cat->deskripsi, 50) }}</td>
                        <td class="p-3 text-right">
                            <!-- Tombol Edit -->
                            <button type="button" 
                                data-url="{{ route('admin.dashboard.categories.update', $cat->category_id) }}"
                                data-nama="{{ $cat->nama_kategori }}"
                                data-deskripsi="{{ $cat->deskripsi ?? '' }}"
                                onclick="openEditCategoryModal(this)" 
                                class="text-blue-500 hover:text-blue-700 font-medium transition mr-3">
                                Edit
                            </button>
                            <!-- Tombol Hapus -->
                            <button type="button" onclick="openDeleteModal('{{ route('admin.dashboard.categories.destroy', $cat->category_id) }}', 'Yakin ingin menghapus kategori <b>{{ $cat->nama_kategori }}</b>?')" class="text-red-500 hover:text-red-700 font-medium transition">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-6 text-center text-gray-400">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- BAGIAN 2: PRODUK -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-900">Daftar Produk</h2>
            <button onclick="toggleModal('modalProduk')" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">+ Tambah Produk</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-sm border-b">
                        <th class="p-3 font-medium">Foto</th>
                        <th class="p-3 font-medium">Nama Produk</th>
                        <th class="p-3 font-medium">Kategori</th>
                        <th class="p-3 font-medium">Harga</th>
                        <th class="p-3 font-medium">Stok</th>
                        <th class="p-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($products as $prod)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">
                            <img src="{{ asset('storage/products/' . $prod->alamat_foto) }}" class="w-12 h-12 object-cover rounded-md border border-gray-200" alt="foto">
                        </td>
                        <td class="p-3 font-medium text-gray-900">{{ $prod->nama_produk }}</td>
                        <td class="p-3 text-gray-600">{{ $prod->category->nama_kategori ?? '-' }}</td>
                        <td class="p-3 text-gray-600">Rp {{ number_format($prod->harga, 0, ',', '.') }}</td>
                        <td class="p-3 text-gray-600">{{ $prod->stok }}</td>
                        <td class="p-3 text-right">
                            <!-- Tombol Edit -->
                            <button type="button" 
                                data-url="{{ route('admin.dashboard.products.update', $prod->product_id) }}"
                                data-nama="{{ $prod->nama_produk }}"
                                data-category="{{ $prod->category_id }}"
                                data-harga="{{ $prod->harga }}"
                                data-stok="{{ $prod->stok }}"
                                data-deskripsi="{{ $prod->deskripsi ?? '' }}"
                                onclick="openEditProductModal(this)" 
                                class="text-blue-500 hover:text-blue-700 font-medium transition mr-3">
                                Edit
                            </button>
                            <!-- Tombol Hapus -->
                            <button type="button" onclick="openDeleteModal('{{ route('admin.dashboard.products.destroy', $prod->product_id) }}', 'Produk <b>{{ $prod->nama_produk }}</b> dan fotonya akan dihapus permanen. Yakin?')" class="text-red-500 hover:text-red-700 font-medium transition">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-400">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI HAPUS ================= -->
<div id="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-sm text-center shadow-xl">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Hapus</h3>
        <p id="deleteModalMessage" class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data ini?</p>
        
        <form id="deleteForm" method="POST" action="">
            @csrf @method('DELETE')
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="toggleModal('modalHapus')" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL TAMBAH KATEGORI ================= -->
<div id="modalKategori" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold mb-4">Tambah Kategori</h3>
        <form action="{{ route('admin.dashboard.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="nama_kategori" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="toggleModal('modalKategori')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-black transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT KATEGORI ================= -->
<div id="modalEditKategori" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-md shadow-xl">
        <h3 class="text-lg font-bold mb-4">Edit Kategori</h3>
        <form id="editCategoryForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm text-gray-700 mb-1">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="edit_nama_kategori" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi_kategori" class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="toggleModal('modalEditKategori')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL TAMBAH PRODUK ================= -->
<div id="modalProduk" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-xl shadow-xl">
        <h3 class="text-lg font-bold mb-4">Tambah Produk Baru</h3>
        <form action="{{ route('admin.dashboard.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-gray-700 mb-1">Upload Foto Baru (Otomatis WebP 1:1)</label>
                <input type="file" name="foto" accept="image/*" required class="w-full border px-3 py-2 rounded-lg outline-none">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Deskripsi Produk</label>
                <textarea name="deskripsi" rows="3" class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="toggleModal('modalProduk')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT PRODUK ================= -->
<div id="modalEditProduk" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-xl shadow-xl">
        <h3 class="text-lg font-bold mb-4">Edit Produk</h3>
        <form id="editProductForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" id="edit_nama_produk" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" id="edit_category_id" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" id="edit_harga" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok" id="edit_stok" required class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-gray-700 mb-1">Update Foto <span class="text-gray-400 text-xs">(Kosongkan jika tidak ingin mengubah foto)</span></label>
                <!-- Atribut "required" dihilangkan pada Edit form -->
                <input type="file" name="foto" accept="image/*" class="w-full border px-3 py-2 rounded-lg outline-none">
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Deskripsi Produk</label>
                <textarea name="deskripsi" id="edit_deskripsi_produk" rows="3" class="w-full border px-3 py-2 rounded-lg outline-none focus:border-primary"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="toggleModal('modalEditProduk')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Buka tutup modal generik
    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle('hidden');
    }

    // Modal Hapus Dinamis
    function openDeleteModal(actionUrl, customMessage) {
        document.getElementById('deleteForm').action = actionUrl;
        document.getElementById('deleteModalMessage').innerHTML = customMessage;
        toggleModal('modalHapus');
    }

    // Modal Edit Kategori Dinamis
    function openEditCategoryModal(btn) {
        // Ambil data dari atribut HTML tombol yang diklik
        document.getElementById('editCategoryForm').action = btn.getAttribute('data-url');
        document.getElementById('edit_nama_kategori').value = btn.getAttribute('data-nama');
        document.getElementById('edit_deskripsi_kategori').value = btn.getAttribute('data-deskripsi');
        toggleModal('modalEditKategori');
    }

    // Modal Edit Produk Dinamis
    function openEditProductModal(btn) {
        // Ambil data dari atribut HTML tombol yang diklik
        document.getElementById('editProductForm').action = btn.getAttribute('data-url');
        document.getElementById('edit_nama_produk').value = btn.getAttribute('data-nama');
        document.getElementById('edit_category_id').value = btn.getAttribute('data-category');
        document.getElementById('edit_harga').value = btn.getAttribute('data-harga');
        document.getElementById('edit_stok').value = btn.getAttribute('data-stok');
        document.getElementById('edit_deskripsi_produk').value = btn.getAttribute('data-deskripsi');
        toggleModal('modalEditProduk');
    }
</script>
@endsection