<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>圖片上傳測試</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #1a1a1a;
            color: #ffffff;
        }
        .upload-container {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            background-color: #2a2a2a;
        }
        .upload-container.dragover {
            border-color: #007bff;
            background-color: #3a3a3a;
        }
        #fileInput {
            display: none;
        }
        .upload-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        .upload-btn:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #155724;
            border: 1px solid #c3e6cb;
            color: #d4edda;
        }
        .error {
            background-color: #721c24;
            border: 1px solid #f5c6cb;
            color: #f8d7da;
        }
        .preview {
            max-width: 300px;
            margin-top: 10px;
        }
        .debug-info {
            background-color: #2a2a2a;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <h1>圖片上傳測試</h1>
    
    <div class="upload-container" id="uploadContainer">
        <p>拖拽圖片到這裡或點擊選擇檔案</p>
        <input type="file" id="fileInput" accept="image/*">
        <button class="upload-btn" id="selectBtn">選擇檔案</button>
        <button class="upload-btn" id="uploadBtn" style="display: none;">上傳圖片</button>
        <button class="upload-btn" id="testBtn" style="display: none; background-color: #28a745;">測試上傳</button>
    </div>
    
    <div id="result"></div>
    <div id="debugInfo" class="debug-info" style="display: none;"></div>
    <img id="preview" class="preview" style="display: none;">
    
    <script>
        $(document).ready(function() {
            // 選擇檔案按鈕點擊事件
            $('#selectBtn').click(function() {
                $('#fileInput').click();
            });
            
            // 檔案選擇事件
            $('#fileInput').change(function(e) {
                if (e.target.files.length > 0) {
                    handleFile(e.target.files[0]);
                }
            });
            
            // 拖拽功能
            $('#uploadContainer').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });
            
            $('#uploadContainer').on('dragleave', function() {
                $(this).removeClass('dragover');
            });
            
            $('#uploadContainer').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    handleFile(files[0]);
                }
            });
            
            // 上傳按鈕點擊事件
            $('#uploadBtn').click(uploadFile);
            $('#testBtn').click(testUpload);
            
            function handleFile(file) {
                if (!file.type.startsWith('image/')) {
                    showResult('請選擇圖片檔案', 'error');
                    return;
                }
                
                // 顯示預覽
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
                
                $('#uploadBtn, #testBtn').show();
                $('#uploadBtn, #testBtn').data('file', file);
            }
            
            function uploadFile() {
                const file = $('#uploadBtn').data('file');
                if (!file) return;
                
                const formData = new FormData();
                formData.append('image', file);
                
                // 顯示除錯資訊
                showDebugInfo(`準備上傳檔案:
檔案名稱: ${file.name}
檔案大小: ${file.size} bytes
檔案類型: ${file.type}
目標 URL: /upload/image`);
                
                $.ajax({
                    url: '/upload/image',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status === 'success') {
                            showResult(`上傳成功！檔案路徑: ${data.path}`, 'success');
                            showDebugInfo(`成功回應: ${JSON.stringify(data, null, 2)}`);
                        } else {
                            showResult(`上傳失敗`, 'error');
                            showDebugInfo(`錯誤回應: ${JSON.stringify(data, null, 2)}`);
                        }
                    },
                    error: function(xhr, status, error) {
                        showResult(`上傳錯誤: ${error}`, 'error');
                        showDebugInfo(`錯誤詳情: ${xhr.responseText}`);
                    }
                });
            }
            
            function testUpload() {
                const file = $('#testBtn').data('file');
                if (!file) return;
                
                const formData = new FormData();
                formData.append('image', file);
                
                showDebugInfo(`測試上傳檔案:
檔案名稱: ${file.name}
檔案大小: ${file.size} bytes
檔案類型: ${file.type}
目標 URL: /test/upload`);
                
                $.ajax({
                    url: '/test/upload',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        showResult(`測試結果: ${data.message}`, data.success ? 'success' : 'error');
                        showDebugInfo(`測試回應: ${JSON.stringify(data, null, 2)}`);
                    },
                    error: function(xhr, status, error) {
                        showResult(`測試錯誤: ${error}`, 'error');
                        showDebugInfo(`測試錯誤詳情: ${xhr.responseText}`);
                    }
                });
            }
            
            function showResult(message, type) {
                $('#result').html(`<div class="result ${type}">${message}</div>`);
            }
            
            function showDebugInfo(info) {
                $('#debugInfo').text(info).show();
            }
        });
    </script>
</body>
</html> 