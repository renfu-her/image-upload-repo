<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

Route::get('/', function () {
    return view('welcome');
});

// 圖片上傳測試頁面
Route::get('/upload', function () {
    return view('upload');
})->name('upload.test');

// 圖片上傳路由
Route::post('/upload/image', [ImageUploadController::class, 'upload'])->name('upload.image');
