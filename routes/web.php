<?php

use Illuminate\Support\Facades\Route;


use Illuminate\Http\Request;

use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('mobile.index');
})->name('index');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::get('/login/bypass', [LoginController::class, 'bypass'])->name('login.bypass');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/booking', function () {
    return view('mobile.booking');
})->name('mobile.booking');

Route::get('/contact', function () {
    return view('mobile.contact');
})->name('mobile.contact');

Route::get('/admin/fields', function () {
    return view('admin.fields');
})->name('admin.fields');

Route::get('/admin/dashboard', function () {
    if (!session()->has('user') || session('user')['type'] !== 'owner') {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter en tant qu\'administrateur.');
    }
    return view('admin.dashboard');
})->name('admin.dashboard');
