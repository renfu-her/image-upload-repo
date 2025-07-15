<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;

Route::get('/', function () {
    return view('welcome');
});

// 測試路由
Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => '伺服器正常運作',
        'timestamp' => now()
    ]);
});

// 圖片上傳測試頁面
Route::get('/upload', function () {
    return view('upload');
})->name('upload.test');

// 圖片上傳路由
Route::post('/upload/image', [ImageUploadController::class, 'upload'])->name('upload.image');
