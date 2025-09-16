<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
