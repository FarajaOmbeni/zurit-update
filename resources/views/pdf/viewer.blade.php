<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $material->title }} - PDF Viewer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-family: sans-serif;
        }
        #viewer-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: auto;
        }
        #toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }
        #pdf-container {
            padding: 60px 20px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .page-container {
            margin-bottom: 20px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            background: white;
        }
        canvas {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        /* Loading spinner styles */
        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            min-height: 400px;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: #666;
            font-size: 16px;
            text-align: center;
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div id="toolbar">
        <h2>{{ $material->title }}</h2>
        <button id="close-btn" class="toolbar-button">Close</button>
    </div>
    
    <div id="viewer-container">
        <!-- Loading spinner -->
        <div id="loading-container" class="loading-container">
            <div class="spinner"></div>
            <div class="loading-text">Loading PDF...</div>
        </div>
        
        <!-- PDF content -->
        <div id="pdf-container" class="hidden"></div>
    </div>

    <script>
        // Configure PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
        
        // Get DOM elements
        const container = document.getElementById('pdf-container');
        const loadingContainer = document.getElementById('loading-container');
        
        // Load and render PDF
        (async function() {
            try {
                const loadingTask = pdfjsLib.getDocument('{{ route("course-materials.show", $material->id) }}');
                const pdfDoc = await loadingTask.promise;
                
                // Hide loading spinner and show PDF container
                loadingContainer.classList.add('hidden');
                container.classList.remove('hidden');
                
                // Render all pages
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    const page = await pdfDoc.getPage(i);
                    
                    // Set scale to 1.0 for original size, or calculate based on container width
                    const viewport = page.getViewport({ scale: 1.0 });
                    
                    // Create canvas
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    // Create page container
                    const pageDiv = document.createElement('div');
                    pageDiv.className = 'page-container';
                    pageDiv.appendChild(canvas);
                    container.appendChild(pageDiv);
                    
                    // Render page
                    await page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise;
                }
                
            } catch (err) {
                // Hide loading spinner and show error
                loadingContainer.classList.add('hidden');
                container.classList.remove('hidden');
                container.innerHTML = `<div style="color: #333; padding: 20px; text-align: center;">Error loading PDF: ${err.message}</div>`;
                console.error('PDF loading error:', err);
            }
        })();
        
        // Close button
        document.getElementById('close-btn').addEventListener('click', () => {
            window.close();
        });
        
        // Disable right-click
        document.addEventListener('contextmenu', e => e.preventDefault());
    </script>
</body>
</html>