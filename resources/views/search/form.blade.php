<form action="{{ route('search') }}" method="GET" role="search" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <label for="catalog-search" class="block text-sm font-medium mb-2">Cari produk di toko ini</label>
    <div class="flex gap-2">
        <input id="catalog-search" type="search" name="q" maxlength="200" placeholder="Nama produk atau kategori" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-4 py-3 bg-white text-gray-900" required>
        <button class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700" type="submit">Cari</button>
    </div>
</form>
