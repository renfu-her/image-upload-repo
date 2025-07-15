<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testUpload(Request $request)
    {
        // 記錄所有請求資訊
        $debug = [
            'method' => $request->method(),
            'url' => $request->url(),
            'headers' => $request->headers->all(),
            'all_data' => $request->all(),
            'files' => $request->allFiles(),
            'has_file' => $request->hasFile('image'),
            'content_type' => $request->header('Content-Type'),
            'content_length' => $request->header('Content-Length'),
        ];
        
        // 檢查是否有檔案
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $debug['file_info'] = [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->getClientOriginalExtension(),
                'is_valid' => $file->isValid(),
                'error' => $file->getError(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => '檔案接收成功',
                'debug' => $debug
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => '沒有找到檔案',
                'debug' => $debug
            ]);
        }
    }
} 