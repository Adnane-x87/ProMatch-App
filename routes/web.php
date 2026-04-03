<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('mobile.index');
});

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
    return view('admin.dashboard');
})->name('admin.dashboard');
