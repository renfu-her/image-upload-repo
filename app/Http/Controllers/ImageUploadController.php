<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // 記錄請求資訊
        Log::info('Image upload request received', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'has_file' => $request->hasFile('image'),
            'all_data' => $request->all()
        ]);

        // 檢查是否有檔案上傳
        if (!$request->hasFile('image')) {
            Log::error('No image file found in request');
            return response()->json([
                'status' => 'error',
                'path' => null
            ], 400);
        }

        // 驗證請求
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'path' => null
            ], 422);
        }

        try {
            // 取得上傳的檔案
            $file = $request->file('image');
            
            Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension()
            ]);
            
            // 生成唯一的檔案名稱
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // 確保目錄存在
            $directory = 'public/images';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
            
            // 儲存檔案到 storage/app/public/images 目錄
            $path = $file->storeAs($directory, $fileName);
            
            Log::info('File saved successfully', [
                'path' => $path,
                'full_path' => Storage::path($path)
            ]);
            
            // 返回成功回應
            return response()->json([
                'status' => 'success',
                'path' => config('app.url') . Storage::url($path)
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'path' => null
            ], 500);
        }
    }
} 