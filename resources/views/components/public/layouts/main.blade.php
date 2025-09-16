<!-- resources/views/public/layouts/main.blade.php -->

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Judul halaman akan dinamis -->
    <title>{{ $title ?? ($siteSettings['lab_name'] ?? 'Laboratorium GOS USK') }}</title>
    
    <!-- Console Override for Production -->
    <script>
        // Set to false for production to disable all console output
        var DEBUG = {{ config('app.debug') ? 'true' : 'false' }};

        if (!DEBUG) {
            // Override console methods if they exist
            if (!window.console) window.console = {};

            // Common console methods
            var methods = ["log", "debug", "warn", "info", "error", "trace", "dir", "dirxml", "profile", "profileEnd", "time", "timeEnd", "assert", "count"];

            for (var i = 0; i < methods.length; i++) {
                console[methods[i]] = function(){};
            }
        } else {
            // In debug mode, ensure console methods exist for older browsers
            if (window.console && !console.dir) {
                var fallbackMethods = ["dir", "dirxml", "trace", "profile", "profileEnd", "time", "timeEnd"];
                for (var i = 0; i < fallbackMethods.length; i++) {
                    if (!console[fallbackMethods[i]]) {
                        console[fallbackMethods[i]] = function(){};
                    }
                }
            }
        }
    </script>

    <!-- Laravel Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Alpine.js x-cloak style -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-800 overflow-x-hidden">

    {{-- Memanggil komponen Navbar --}}
    @include('public.partials.navbar')

    {{-- Ini adalah tempat konten spesifik halaman akan disuntikkan --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Memanggil komponen Footer --}}
    @include('public.partials.footer')

</body>
</html>