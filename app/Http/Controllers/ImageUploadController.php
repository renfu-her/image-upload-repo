<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // 驗證請求
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // 取得上傳的檔案
            $file = $request->file('image');
            
            // 生成唯一的檔案名稱
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // 儲存檔案到 storage/app/public/images 目錄
            $path = $file->storeAs('public/images', $fileName);
            
            // 返回成功回應
            return response()->json([
                'success' => true,
                'message' => '圖片上傳成功',
                'data' => [
                    'filename' => $fileName,
                    'path' => Storage::url($path),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '圖片上傳失敗: ' . $e->getMessage()
            ], 500);
        }
    }
} 