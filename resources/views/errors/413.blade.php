<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Payload Too Large - CMS SJA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased overflow-hidden text-sm">
    <!-- 
      Wrapper Z-50 untuk menutupi raw text warning PHP dari server
    -->
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-gray-50 z-50">
        
        <!-- Clean Admin Card -->
        <div class="max-w-md w-full bg-white rounded-lg border border-gray-200 shadow-md overflow-hidden text-center p-8 relative">
            
            <!-- Warning Icon -->
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <!-- Typography -->
            <h1 class="text-xl font-semibold text-gray-800 mb-2">Unggahan Terlalu Besar</h1>
            <p class="text-gray-500 mb-8 text-sm leading-relaxed">
                Total ukuran file yang Anda unggah menembus batas maksimal server. 
                Silakan kembali, lalu kompres foto atau unggah dalam jumlah yang lebih sedikit.
            </p>
            
            <!-- Action Button -->
            <button onclick="window.history.back()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded transition-colors duration-200 shadow-sm">
                Kembali ke Form
            </button>
            
            <div class="mt-5 text-xs text-gray-400">Error 413: Payload Too Large</div>
        </div>
    </div>
</body>
</html>
