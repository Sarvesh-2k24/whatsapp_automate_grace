<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Bulk Messenger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --whatsapp-green: #25D366;
            --whatsapp-dark: #075E54;
        }
        
        body {
            background-color: #f0f2f5;
            min-height: 100vh;
        }

        .container-fluid {
            padding: 2rem;
        }

        .main-title {
            color: var(--whatsapp-dark);
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .preview-card {
            height: calc(100vh - 150px);
            background: #fff;
        }

        .preview-header {
            background: var(--whatsapp-dark);
            color: white;
            padding: 1rem;
            border-radius: 15px 15px 0 0;
        }

        .preview-content {
            padding: 1rem;
            background: #e5ddd5;
            height: calc(100% - 60px);
            border-radius: 0 0 15px 15px;
            overflow-y: auto;
        }

        .message-bubble {
            background: white;
            padding: 1rem;
            border-radius: 15px;
            max-width: 80%;
            margin-bottom: 1rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .message-time {
            font-size: 0.75rem;
            color: #666;
            position: absolute;
            bottom: 0.5rem;
            right: 1rem;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }

        .form-card {
            background: white;
        }

        .btn-whatsapp {
            background: var(--whatsapp-green);
            border: none;
            color: white;
            font-weight: bold;
        }

        .btn-whatsapp:hover {
            background: #1fab54;
            color: white;
        }

        .image-preview-container {
            position: relative;
            margin-top: 1rem;
        }

        .remove-image {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
        }

        .result-box {
            max-height: 300px;
            overflow-y: auto;
        }

        #dropZone {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #dropZone:hover {
            border-color: var(--whatsapp-green);
            background: #f8f9fa;
        }

        .drag-over {
            border-color: var(--whatsapp-green) !important;
            background: #e9ecef !important;
        }
    </style>
</head>
<body>
    <div id="backendStatus" class="alert d-none" role="alert"></div>
    <div class="container-fluid">
        <h1 class="text-center main-title">
            <i class="fab fa-whatsapp"></i> WhatsApp Bulk Messenger
        </h1>
        
        <div class="row">
            <!-- Preview Column -->
            <div class="col-md-5 mb-4">
                <div class="card preview-card">
                    <div class="preview-header">
                        <i class="fas fa-eye"></i> Message Preview
                    </div>
                    <div class="preview-content" id="previewContent">
                        <div class="message-bubble">
                            <div id="imagePreviewInBubble"></div>
                            <div id="messagePreview">Your message will appear here...</div>
                            <div class="message-time"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Column -->
            <div class="col-md-7">
                <div class="card form-card">
                    <div class="card-body">
                        <form id="messageForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="excelFile" class="form-label">Excel File (with 'phone' column) and maximum 50 contacts</label>
                                <div id="dropZone">
                                    <i class="fas fa-file-excel fa-2x mb-2"></i>
                                    <p class="mb-0">Drag & drop your Excel file here or click to browse</p>
                                    <input type="file" class="d-none" id="excelFile" accept=".xlsx,.xls" required>
                                </div>
                                <div id="fileNameDisplay" class="mt-2"></div>
                                <div class="invalid-feedback">Please select an Excel file.</div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" rows="4" required 
                                    placeholder="Type your message here..."></textarea>
                                <div class="invalid-feedback">Please enter a message.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image (optional)</label>
                                <div id="imageDropZone" class="dropZone">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p class="mb-0">Drag & drop an image here or click to browse</p>
                                    <input type="file" class="d-none" id="imageInput" accept="image/*">
                                </div>
                                <div id="imagePreview" class="image-preview-container"></div>
                            </div>

                            <button type="submit" class="btn btn-whatsapp w-100">
                                <i class="fas fa-paper-plane"></i> Send Messages
                            </button>
                        </form>
                    </div>
                </div>

                <div id="results" class="mt-4 d-none">
                    <div class="card shadow">
                        <div class="card-header">
                            <i class="fas fa-list-check"></i> Results
                        </div>
                        <div class="card-body result-box">
                            <div id="resultsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Backend URL configuration
        const BACKEND_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
            ? 'http://localhost:5000'
            : `http://${window.location.hostname}:5000`;
            
        // Backend management functions
        async function checkBackend() {
            try {
                const response = await fetch(`${BACKEND_URL}/send-whatsapp`, {
                    method: 'HEAD'
                });
                return response.ok;
            } catch (error) {
                return false;
            }
        }

        async function startBackend() {
            const statusDiv = document.getElementById('backendStatus');
            statusDiv.className = 'alert alert-info';
            statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting WhatsApp backend server...';
            
            try {
                const command = 'python';
                const args = ['"c:\\xampp\\htdocs\\whatsapp_automation\\backend\\app.py"'];
                
                const response = await fetch(`${BACKEND_URL}/send-whatsapp`, {
                    method: 'HEAD'
                });
                
                if (!response.ok) {
                    const startProcess = new Notification("Starting Backend", {
                        body: "Starting WhatsApp automation backend...",
                        icon: "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/svgs/brands/whatsapp.svg"
                    });
                    
                    const process = window.open('cmd.exe /c ' + command + ' ' + args.join(' '), '_blank');
                    if (process) process.blur();
                    window.focus();
                }
                
                // Wait for backend to start
                let attempts = 0;
                const maxAttempts = 10;
                while (attempts < maxAttempts) {
                    const isRunning = await checkBackend();
                    if (isRunning) {
                        statusDiv.className = 'alert alert-success';
                        statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> WhatsApp backend server is running';
                        setTimeout(() => {
                            statusDiv.classList.add('d-none');
                        }, 3000);
                        return true;
                    }
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    attempts++;
                }
                
                throw new Error('Backend failed to start after 30 seconds');
            } catch (error) {
                statusDiv.className = 'alert alert-danger';
                statusDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> Failed to start backend server: ${error.message}`;
                return false;
            }
        }

        // Check backend status on page load
        document.addEventListener('DOMContentLoaded', async () => {
            const isRunning = await checkBackend();
            if (!isRunning) {
                await startBackend();
            }
        });

        // Initialize time in preview
        document.querySelector('.message-time').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        // Message preview functionality
        document.getElementById('message').addEventListener('input', function(e) {
            document.getElementById('messagePreview').textContent = e.target.value || 'Your message will appear here...';
        });

        // Excel file drag and drop
        const dropZone = document.getElementById('dropZone');
        const excelInput = document.getElementById('excelFile');
        const fileNameDisplay = document.getElementById('fileNameDisplay');

        // Add function to format file sizes
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'));
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'));
        });

        dropZone.addEventListener('click', () => excelInput.click());
        
        dropZone.addEventListener('drop', function(e) {
            const file = e.dataTransfer.files[0];
            handleExcelFile(file);
        });

        excelInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            handleExcelFile(file);
        });

        function handleExcelFile(file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (file.size > maxSize) {
                alert('Excel file is too large. Maximum size is 5MB. Please choose a smaller file.');
                fileNameDisplay.innerHTML = '';
                excelInput.value = '';
                return;
            }

            if (file) {
                fileNameDisplay.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-file-excel"></i> ${file.name}<br>
                        <small>File size: ${formatFileSize(file.size)} / Maximum: ${formatFileSize(maxSize)}</small>
                    </div>`;
                excelInput.files = new DataTransfer().files;
                const dt = new DataTransfer();
                dt.items.add(file);
                excelInput.files = dt.files;
            }
        }

        // Image handling
        const imageDropZone = document.getElementById('imageDropZone');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewInBubble = document.getElementById('imagePreviewInBubble');

        imageDropZone.addEventListener('click', () => imageInput.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageDropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            imageDropZone.addEventListener(eventName, () => imageDropZone.classList.add('drag-over'));
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imageDropZone.addEventListener(eventName, () => imageDropZone.classList.remove('drag-over'));
        });

        imageDropZone.addEventListener('drop', function(e) {
            const file = e.dataTransfer.files[0];
            handleImageFile(file);
        });

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            handleImageFile(file);
        });

        async function handleImageFile(file) {
            if (file && file.type.startsWith('image/')) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (file.size > maxSize) {
                    // Show original size before compression
                    const originalSizeMsg = `Original size: ${formatFileSize(file.size)} - Compressing...`;
                    imagePreview.innerHTML = `<div class="alert alert-info">${originalSizeMsg}</div>`;
                    
                    // Compress the image
                    const compressedFile = await compressImage(file, maxSize);
                    if (compressedFile.size > maxSize) {
                        alert('Image is too large. Maximum size is 200kB. Please choose a smaller image.');
                        removeImage();
                        return;
                    }
                    file = compressedFile;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="preview-image">
                            <span class="remove-image" onclick="removeImage()">×</span>
                            <div class="mt-2 small text-muted">
                                File size: ${formatFileSize(file.size)} / Maximum: ${formatFileSize(2 * 1024 * 1024)}
                            </div>
                        </div>`;
                    imagePreview.innerHTML = img;
                    imagePreviewInBubble.innerHTML = `<img src="${e.target.result}" class="preview-image">`;
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            imageInput.value = '';
            imagePreview.innerHTML = '';
            imagePreviewInBubble.innerHTML = '';
        }

        // Image compression function
        async function compressImage(file, maxSize) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.src = e.target.result;
                    
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        
                        // Calculate new dimensions while maintaining aspect ratio
                        let quality = 0.7;
                        const maxDim = 1920; // Max dimension for WhatsApp
                        
                        if (width > height && width > maxDim) {
                            height = (height * maxDim) / width;
                            width = maxDim;
                        } else if (height > maxDim) {
                            width = (width * maxDim) / height;
                            height = maxDim;
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Try to compress until file size is under maxSize or quality is too low
                        const compress = () => {
                            const dataUrl = canvas.toDataURL('image/jpeg', quality);
                            const binaryImg = atob(dataUrl.split(',')[1]);
                            const imgSize = binaryImg.length;
                            
                            if (imgSize > maxSize && quality > 0.1) {
                                quality -= 0.1;
                                compress();
                            } else {
                                // Convert base64 to Blob
                                const byteArray = new Uint8Array(binaryImg.length);
                                for (let i = 0; i < binaryImg.length; i++) {
                                    byteArray[i] = binaryImg.charCodeAt(i);
                                }
                                const blob = new Blob([byteArray], { type: 'image/jpeg' });
                                resolve(new File([blob], file.name, { type: 'image/jpeg' }));
                            }
                        };
                        
                        compress();
                    };
                };
                reader.readAsDataURL(file);
            });
        }

        // Form submission
        document.getElementById('messageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData();
            formData.append('file', document.getElementById('excelFile').files[0]);
            formData.append('message', document.getElementById('message').value);
            
            // Add image if present
            if (imageInput.files[0]) {
                const reader = new FileReader();
                reader.onload = async function(e) {
                    formData.append('image', e.target.result);
                    await sendFormData(formData);
                };
                reader.readAsDataURL(imageInput.files[0]);
            } else {
                await sendFormData(formData);
            }
        });

        async function sendFormData(formData) {
            try {
                const response = await fetch(`${BACKEND_URL}/send-whatsapp`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                // Show results section
                document.getElementById('results').classList.remove('d-none');
                const resultsList = document.getElementById('resultsList');
                resultsList.innerHTML = '';

                if (data.success) {
                    data.results.forEach(result => {
                        const div = document.createElement('div');
                        div.className = `alert alert-${result.status === 'success' ? 'success' : 'danger'} mb-2`;
                        div.innerHTML = `
                            <strong>Phone:</strong> ${result.phone}<br>
                            <strong>Status:</strong> ${result.status}<br>
                            ${result.status === 'success' 
                                ? `<strong>Message ID:</strong> ${result.message_sid}` 
                                : `<strong>Error:</strong> ${result.error}`}
                        `;
                        resultsList.appendChild(div);
                    });
                } else {
                    const div = document.createElement('div');
                    div.className = 'alert alert-warning';
                    div.innerHTML = `
                        <strong>Cannot Send Message:</strong><br>
                        ${data.error || 'An error occurred while sending messages.'}
                    `;
                    resultsList.appendChild(div);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('results').classList.remove('d-none');
                const resultsList = document.getElementById('resultsList');
                resultsList.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to connect to the server. Please ensure the backend is running.
                    </div>
                `;
            }
        }
    </script>
</body>
</html>