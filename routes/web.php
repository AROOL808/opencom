<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAdminIsLoggedIn;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product_id}', [HomeController::class, 'showProduct'])->name('product.show');
Route::post('/cart/add/{product_id}', [HomeController::class, 'addToCart'])->name('cart.add');

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

        // Profil Toko (Route: /admin/dashboard/profile | Name: admin.dashboard.profile)
        Route::get('/profile', function () {
            return view('admin.dashboard.profile');
        })->name('profile');

        // Kustomasi Toko (Route: /admin/dashboard/customization)
        Route::get('/customization', function () {
            return view('admin.dashboard.customization'); // Sesuaikan dengan nama view Anda
        })->name('customization');

        // Item Produk (Route: /admin/dashboard/items)
        Route::get('/items', [ItemController::class, 'index'])->name('items');
        // CRUD Kategori
        Route::post('/categories', [ItemController::class, 'storeCategory'])->name('categories.store');
        Route::delete('/categories/{id}', [ItemController::class, 'destroyCategory'])->name('categories.destroy');
        Route::put('/categories/{id}', [ItemController::class, 'updateCategory'])->name('categories.update');

        // CRUD Produk
        Route::post('/products', [ItemController::class, 'storeProduct'])->name('products.store');
        Route::delete('/products/{id}', [ItemController::class, 'destroyProduct'])->name('products.destroy');
        Route::put('/products/{id}', [ItemController::class, 'updateProduct'])->name('products.update');

        // Log Aktivitas Toko (Route: /admin/dashboard/logs)
        Route::get('/logs', function () {
            return view('admin.dashboard.logs');
        })->name('logs');

    }
    );

    
// Rute Registrasi & OAuth Google
Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
Route::get('/auth/google', [UserAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [UserAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Rute Lengkapi Profil
Route::get('/register/complete', [UserAuthController::class, 'showCompleteRegister'])->name('register.complete');
Route::post('/register/complete', [UserAuthController::class, 'storeCompleteRegister'])->name('register.complete.post');

// Tampilkan halaman login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function (Request $request) {
    // Hapus session login yang sebelumnya kita set
    $request->session()->forget(['user_logged_in', 'username', 'register_email', 'register_name']);
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home')->with('success', 'Berhasil keluar akun.');
})->name('logout');

// Pastikan rute ini berada di area yang bisa diakses publik (Controller sudah menangani proteksi login)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::patch('/cart/{item_cart_id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item_cart_id}', [CartController::class, 'destroy'])->name('cart.destroy');