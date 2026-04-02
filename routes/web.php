<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('mobile.index');
});

Route::get('/booking', function () {
    return view('mobile.booking');
})->name('mobile.booking');
