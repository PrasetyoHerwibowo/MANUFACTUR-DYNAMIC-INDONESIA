<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController as AdminMachineController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
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

        // Model mesin (fungsi mesin dan spesifikasi)
        Route::resource('machines', AdminMachineController::class)
            ->except('show')
            ->parameters(['machines' => 'machine']);

        // Profil perusahaan
        Route::get('profil', [CompanyProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profil', [CompanyProfileController::class, 'update'])->name('profile.update');

        // Pesan dari formulir kontak
        Route::get('pesan', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('pesan/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::delete('pesan/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

        // Balasan pesan (draf & kirim langsung via Gmail SMTP)
        Route::post('pesan/{message}/balasan', [ContactMessageController::class, 'storeReply'])
            ->name('messages.replies.store');
        Route::post('pesan/{message}/balasan/kirim', [ContactMessageController::class, 'sendReply'])
            ->name('messages.replies.send');
        Route::delete('pesan/{message}/balasan/{reply}', [ContactMessageController::class, 'destroyReply'])
            ->name('messages.replies.destroy');

        // Pesanan + pembayaran (ditampilkan dalam satu daftar)
        Route::get('pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('pesanan/tambah', [AdminOrderController::class, 'create'])->name('orders.create');
        Route::post('pesanan', [AdminOrderController::class, 'store'])->name('orders.store');
        Route::get('pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::delete('pesanan/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('pesanan/{order}/pembayaran-baru', [AdminOrderController::class, 'renewPayment'])
            ->name('orders.payments.renew');

        // Pembayaran: verifikasi lunas atau tandai gagal.
        Route::put('pembayaran/{payment}/lunas', [AdminOrderController::class, 'verifyPayment'])
            ->name('payments.verify');
        Route::put('pembayaran/{payment}/gagal', [AdminOrderController::class, 'failPayment'])
            ->name('payments.fail');

        // Pelanggan terdaftar
        Route::get('pelanggan', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('pelanggan/tambah', [AdminCustomerController::class, 'create'])->name('customers.create');
        Route::post('pelanggan', [AdminCustomerController::class, 'store'])->name('customers.store');
        Route::get('pelanggan/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::get('pelanggan/{customer}/ubah', [AdminCustomerController::class, 'edit'])->name('customers.edit');
        Route::put('pelanggan/{customer}', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::delete('pelanggan/{customer}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');

        // Akun admin
        Route::get('akun', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('akun', [AccountController::class, 'update'])->name('account.update');
        Route::put('akun/password', [AccountController::class, 'updatePassword'])->name('account.password');
    });
});

