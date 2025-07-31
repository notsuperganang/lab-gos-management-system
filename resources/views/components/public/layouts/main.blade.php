<!-- resources/views/public/layouts/main.blade.php -->

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Judul halaman akan dinamis -->
    <title>{{ $title ?? 'Laboratorium GOS USK' }}</title>
    
    <!-- Laravel Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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