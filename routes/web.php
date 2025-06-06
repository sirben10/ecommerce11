<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('index ');
 