<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdminIsLoggedIn;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;


Route::get('/', function () {
    // Data dummy produk hardcode untuk keperluan starter (tanpa query database)
     $heroSlides = [
        [
            'title' => 'Diskon Musim Panas 50%',
            'desc' => 'Koleksi pakaian pria terbaik untuk musim ini. Jangan sampai kehabisan!',
            'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'cta' => 'Belanja Sekarang'
        ],
        [
            'title' => 'Sneakers Rilis Terbaru',
            'desc' => 'Langkah lebih gaya dengan koleksi sneakers eksklusif minggu ini.',
            'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'cta' => 'Lihat Koleksi'
        ],
        [
            'title' => 'Aksesoris Premium',
            'desc' => 'Lengkapi gayamu dengan jam tangan minimalis dan elegan.',
            'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'cta' => 'Eksplor Aksesoris'
        ]
    ];

    $categories = [
        ['name' => 'Pakaian', 'icon' => '', 'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?ixlib=rb-4.0.3&w=400&q=80'],
        ['name' => 'Sepatu', 'icon' => '', 'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&w=400&q=80'],
        ['name' => 'Aksesoris', 'icon' => '', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&w=400&q=80'],
        ['name' => 'Tas', 'icon' => '', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&w=400&q=80'],
    ];

    $productsByCategory = [
        'Pakaian' => [
            ['name' => 'Kemeja Flannel Premium', 'price' => 250000, 'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Kaos Polos Katun', 'price' => 120000, 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Jaket Denim Vintage', 'price' => 450000, 'image' => 'https://images.unsplash.com/photo-1576871337622-98d48d1cf531?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Celana Chino Slim', 'price' => 220000, 'image' => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?ixlib=rb-4.0.3&w=400&q=80'],
        ],
        'Sepatu' => [
            ['name' => 'Sneakers Klasik Putih', 'price' => 550000, 'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Sepatu Lari Ringan', 'price' => 750000, 'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Boots Kulit Asli', 'price' => 1250000, 'image' => 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Slip-on Santai', 'price' => 300000, 'image' => 'https://images.unsplash.com/photo-1512374382149-233c42b6a83b?ixlib=rb-4.0.3&w=400&q=80'],
        ],
        'Aksesoris' => [
            ['name' => 'Jam Tangan Minimalis', 'price' => 899000, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Kacamata Hitam Aviator', 'price' => 350000, 'image' => 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Dompet Kulit Pria', 'price' => 250000, 'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Topi Baseball Polos', 'price' => 950000, 'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?ixlib=rb-4.0.3&w=400&q=80'],
        ],
        'Tas' => [
            ['name' => 'Tas Ransel Kanvas', 'price' => 320000, 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Tas Selempang Kulit', 'price' => 450000, 'image' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Duffle Bag Olahraga', 'price' => 275000, 'image' => 'https://images.unsplash.com/photo-1692506530242-c12d6c3ae2e2?ixlib=rb-4.0.3&w=400&q=80'],
            ['name' => 'Tote Bag Minimalis', 'price' => 120000, 'image' => 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?ixlib=rb-4.0.3&w=400&q=80'],
        ]
    ];

    $popularProducts = [
        [
            'id' => 1,
            'name' => 'Kemeja Flannel Premium',
            'price' => 250000,
            'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
            'category' => 'Pakaian'
        ],
        [
            'id' => 2,
            'name' => 'Sepatu Sneakers Klasik',
            'price' => 550000,
            'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
            'category' => 'Sepatu'
        ],
        [
            'id' => 3,
            'name' => 'Jam Tangan Minimalis',
            'price' => 899000,
            'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
            'category' => 'Aksesoris'
        ],
        [
            'id' => 4,
            'name' => 'Tas Ransel Kanvas',
            'price' => 320000,
            'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
            'category' => 'Tas'
        ],
    ];

    // Passing data 'products' ke view 'welcome'
    return view('welcome', compact('popularProducts','heroSlides','categories','productsByCategory'));
})->name('home');

// --- Rute User ---
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// --- Rute Admin ---
// Rute Login Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// --- Rute Dashboard Admin (Semua sub-rute di sini otomatis terlindungi) ---
Route::prefix('admin/dashboard')
    ->middleware(EnsureAdminIsLoggedIn::class)
    ->name('admin.dashboard.')
    ->group(function () {

        // Redirect otomatis dari /admin/dashboard ke /admin/dashboard/profile
        Route::get('/', function () {
            return redirect()->route('admin.dashboard.profile');
        });

        // 1. Profil Toko (Route: /admin/dashboard/profile | Name: admin.dashboard.profile)
        Route::get('/profile', function () {
            return view('admin.dashboard.profile');
        })->name('profile');

        // 2. Kustomasi Toko (Route: /admin/dashboard/customization)
        Route::get('/customization', function () {
            return view('admin.dashboard.customization'); // Sesuaikan dengan nama view Anda
        })->name('customization');

        // 3. Item Produk (Route: /admin/dashboard/items)
        Route::get('/items', [ItemController::class, 'index'])->name('items');
        // CRUD Kategori
        Route::post('/categories', [ItemController::class, 'storeCategory'])->name('categories.store');
        Route::delete('/categories/{id}', [ItemController::class, 'destroyCategory'])->name('categories.destroy');
        Route::put('/categories/{id}', [ItemController::class, 'updateCategory'])->name('categories.update');

        // CRUD Produk
        Route::post('/products', [ItemController::class, 'storeProduct'])->name('products.store');
        Route::delete('/products/{id}', [ItemController::class, 'destroyProduct'])->name('products.destroy');
        Route::put('/products/{id}', [ItemController::class, 'updateProduct'])->name('products.update');

        // 4. Log Aktivitas Toko (Route: /admin/dashboard/logs)
        Route::get('/logs', function () {
            return view('admin.dashboard.logs');
        })->name('logs');

    });