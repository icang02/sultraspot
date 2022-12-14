<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\TourPlaceController;
use App\Http\Controllers\UserOrderController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home dan Dashboard Index
Route::get('/', fn () => view('home'))->name('home');
Route::get('/dashboard', fn () => Inertia::render('Dashboard/Index', [
    'title' => 'SultraSpot'
]))->name('dashboard');

Route::get('profile', function () {
    return Inertia::render('Dashboard/Profil', [
        'user' => User::find(auth()->user()->id),
    ]);
})->name('profile');

// ADMIN / Manage User
Route::controller(ManageUserController::class)->group(function () {
    Route::get('/users/{role:name}', 'index')->middleware('auth')->can('admin');
    Route::get('/users/{role}/{user:username}', 'edit')->middleware('auth')->can('admin');
    Route::patch('/users/{user:username}', 'update');
    Route::delete('/users/{user:username}', 'destroy');

    Route::post('update-profile/{id}', 'updateProfile');
});

// Auth / User Authentication
Route::controller(AuthenticateController::class)->group(function () {
    Route::get('/login', 'login')->name('login')->middleware('guest');
    Route::post('/login', 'authenticate');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/forget-password', 'forgetPassword');
    Route::get('/register', 'register')->middleware('guest')->name('register');
    Route::post('/register', 'registerStore');
    Route::get('/forget-password', 'forgot')->name('forgot');
});

// ADMIN - PENGUNJUNG / Controller Wisata
Route::controller(TourPlaceController::class)->group(function () {
    Route::get('list-wisata', 'listWisata')->middleware('auth')->name('list-wisata');
    Route::get('list-wisata/{id}', 'detailWisata')->middleware('auth')->name('wisata.detail');
    Route::get('add-wisata', 'add')->middleware('auth')->name('wisata.add');
    Route::post('add-wisata', 'addStore')->middleware('auth')->name('wisata.addStore');
});

// PENGUNJUNG / Controller Keranjang
Route::controller(CartController::class)->group(function () {
    Route::get('carts', 'index')->middleware('auth')->can('pengunjung')->name('carts');
    Route::post('add-carts/{id}', 'add')->middleware('auth')->can('pengunjung');
    Route::post('checkout/{id}', 'checkout')->middleware('auth')->can('pengunjung');
    Route::patch('update-carts/{id}', 'update')->middleware('auth')->can('pengunjung');
    Route::delete('destroy-carts/{id}', 'destroy')->middleware('auth')->can('pengunjung');
});

// PENEGUNJUNG / Controller Kelola Checkout
Route::controller(CheckoutController::class)->group(function () {
    Route::get('checkout/{id_cart}', 'index')->middleware('auth')->can('pengunjung')->name('checkout');
    Route::get('order-success/{id_cart}', 'prosesCheckout')->middleware('auth')->name('order.success');
    Route::get('order/{id_place}', 'orderNowShow')->middleware('auth')->can('pengunjung')->name('order');
    Route::post('order', 'orderNowStore');
});

// PENEGUNJUNG / Controller Kelola Checkout
Route::controller(UserOrderController::class)->group(function () {
    Route::get('pesanan', 'index')->middleware('auth')->name('pesanan');
    Route::get('pesanan/{id}', 'show')->middleware('auth')->name('pesanan.show');
    Route::post('order-confirmation/{id}', 'orderConfirm');
    Route::post('upload-bukti-tf/{id}', 'uploadBuktiTf')->name('upload.bukti.tf');
    Route::get('riwayat', 'index')->middleware('auth')->name('riwayat');
    Route::delete('order-delete/{id}', 'delete');
});
