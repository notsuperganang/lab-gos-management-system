<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login - Lab GOS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <img class="h-16 w-16 bg-blue-600 p-3 rounded-lg" 
                     src="{{ asset('assets/images/logo-fisika-putih.png') }}" 
                     alt="Lab GOS">
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                Admin Portal
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Laboratorium Gelombang, Optik dan Spektroskopi
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Authentication Required</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Please log in with your admin credentials to access the dashboard.
                    </p>
                    
                    <div class="mt-6">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Go to Login
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>© {{ date('Y') }} Laboratorium GOS - Departemen Fisika FMIPA Universitas Syiah Kuala</p>
            </div>
        </div>
    </div>
</body>
</html>