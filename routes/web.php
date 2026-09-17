<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController as AdminMachineController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik (Company Profile)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{machine}', [ProductController::class, 'show'])->name('products.show');

Route::get('/katalog', [CatalogController::class, 'index'])->name('catalogs.index');
Route::get('/katalog/{catalog}', [CatalogController::class, 'show'])->name('catalogs.show');

Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'sendContact'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Panel Admin (satu-satunya role: admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.attempt');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Jenis mesin (kategori)
        Route::resource('categories', AdminCategoryController::class)
            ->except('show')
            ->parameters(['categories' => 'category']);

        // Model mesin (fungsi mesin, spesifikasi, galeri foto)
        Route::resource('machines', AdminMachineController::class)
            ->except('show')
            ->parameters(['machines' => 'machine']);
        Route::put('machines/{machine}/images/{image}', [AdminMachineController::class, 'updateImage'])
            ->name('machines.images.update');
        Route::delete('machines/{machine}/images/{image}', [AdminMachineController::class, 'destroyImage'])
            ->name('machines.images.destroy');

        // Katalog + foto halaman katalog
        Route::resource('catalogs', AdminCatalogController::class)
            ->except('show')
            ->parameters(['catalogs' => 'catalog']);
        Route::put('catalogs/{catalog}/images/{image}', [AdminCatalogController::class, 'updateImage'])
            ->name('catalogs.images.update');
        Route::delete('catalogs/{catalog}/images/{image}', [AdminCatalogController::class, 'destroyImage'])
            ->name('catalogs.images.destroy');

        // Profil perusahaan
        Route::get('profil', [CompanyProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profil', [CompanyProfileController::class, 'update'])->name('profile.update');

        // Pesan dari formulir kontak
        Route::get('pesan', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('pesan/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('pesan/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        // Akun admin
        Route::get('akun', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('akun', [AccountController::class, 'update'])->name('account.update');
        Route::put('akun/password', [AccountController::class, 'updatePassword'])->name('account.password');
    });
});

