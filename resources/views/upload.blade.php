<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>圖片上傳測試</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <button class="upload-btn" onclick="document.getElementById('fileInput').click()">選擇檔案</button>
        <button class="upload-btn" id="uploadBtn" style="display: none;">上傳圖片</button>
        <button class="upload-btn" id="testBtn" style="display: none; background-color: #28a745;">測試上傳</button>
    </div>
    
    <div id="result"></div>
    <div id="debugInfo" class="debug-info" style="display: none;"></div>
    <img id="preview" class="preview" style="display: none;">
    
    <script>
        const uploadContainer = document.getElementById('uploadContainer');
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtn');
        const testBtn = document.getElementById('testBtn');
        const result = document.getElementById('result');
        const preview = document.getElementById('preview');
        const debugInfo = document.getElementById('debugInfo');
        
        // 拖拽功能
        uploadContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadContainer.classList.add('dragover');
        });
        
        uploadContainer.addEventListener('dragleave', () => {
            uploadContainer.classList.remove('dragover');
        });
        
        uploadContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadContainer.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });
        
        uploadBtn.addEventListener('click', uploadFile);
        testBtn.addEventListener('click', testUpload);
        
        function handleFile(file) {
            if (!file.type.startsWith('image/')) {
                showResult('請選擇圖片檔案', 'error');
                return;
            }
            
            // 顯示預覽
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            
            uploadBtn.style.display = 'inline-block';
            testBtn.style.display = 'inline-block';
            uploadBtn.dataset.file = file;
            testBtn.dataset.file = file;
        }
        
        function uploadFile() {
            const file = uploadBtn.dataset.file;
            if (!file) return;
            
            const formData = new FormData();
            formData.append('image', file);
            
            // 顯示除錯資訊
            showDebugInfo(`準備上傳檔案:
檔案名稱: ${file.name}
檔案大小: ${file.size} bytes
檔案類型: ${file.type}
目標 URL: /upload/image`);
            
            fetch('/upload/image', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                showDebugInfo(`伺服器回應:
狀態碼: ${response.status}
狀態文字: ${response.statusText}
Content-Type: ${response.headers.get('Content-Type')}`);
                
                // 檢查回應類型
                const contentType = response.headers.get('Content-Type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // 如果不是 JSON，讀取文字內容
                    return response.text().then(text => {
                        throw new Error(`伺服器返回非 JSON 格式: ${text.substring(0, 200)}...`);
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    showResult(`上傳成功！檔案路徑: ${data.data.path}`, 'success');
                    showDebugInfo(`成功回應: ${JSON.stringify(data, null, 2)}`);
                } else {
                    showResult(`上傳失敗: ${data.message}`, 'error');
                    showDebugInfo(`錯誤回應: ${JSON.stringify(data, null, 2)}`);
                }
            })
            .catch(error => {
                showResult(`上傳錯誤: ${error.message}`, 'error');
                showDebugInfo(`錯誤詳情: ${error.stack}`);
            });
        }
        
        function testUpload() {
            const file = testBtn.dataset.file;
            if (!file) return;
            
            const formData = new FormData();
            formData.append('image', file);
            
            showDebugInfo(`測試上傳檔案:
檔案名稱: ${file.name}
檔案大小: ${file.size} bytes
檔案類型: ${file.type}
目標 URL: /test/upload`);
            
            fetch('/test/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                showDebugInfo(`測試伺服器回應:
狀態碼: ${response.status}
狀態文字: ${response.statusText}
Content-Type: ${response.headers.get('Content-Type')}`);
                
                return response.json();
            })
            .then(data => {
                showResult(`測試結果: ${data.message}`, data.success ? 'success' : 'error');
                showDebugInfo(`測試回應: ${JSON.stringify(data, null, 2)}`);
            })
            .catch(error => {
                showResult(`測試錯誤: ${error.message}`, 'error');
                showDebugInfo(`測試錯誤詳情: ${error.stack}`);
            });
        }
        
        function showResult(message, type) {
            result.innerHTML = `<div class="result ${type}">${message}</div>`;
        }
        
        function showDebugInfo(info) {
            debugInfo.textContent = info;
            debugInfo.style.display = 'block';
        }
    </script>
</body>
</html> 