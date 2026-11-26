<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products');

Route::get('/products', [ProductController::class, 'index'])->name('products/index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products/create');
Route::post('/products', [ProductController::class, 'store'])->name('products/store');
Route::get('/products/{product}/show', [ProductController::class, 'show'])->name('products/show');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products/destroy');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products/edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products/update');
Route::post('/products/{product}/quantity', [ProductController::class, 'updateQuantity'])->name('products.updateQuantity');

Route::get('/tags/search', [TagController::class, 'search'])->name('tag/search');
Route::get('/tags', [TagController::class, 'index'])->name('tag/index');
Route::get('/tags/create', [TagController::class, 'create'])->name('tag/create');
Route::post('/tags', [TagController::class, 'store'])->name('tag/store');
Route::get('/tags/{tag}/show', [TagController::class, 'show'])->name('tag/show');
Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tag/destroy');
Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('tag/edit');
Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tag/update');
