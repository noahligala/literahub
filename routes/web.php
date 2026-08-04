<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');
