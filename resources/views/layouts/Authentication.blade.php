<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication - Laundry 6 Plus')</title>

    <!-- Browser Tab Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- CDN Lucide JS -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-blue-900 font-sans min-h-screen flex flex-col justify-center items-center p-4 overflow-hidden">
    <div class="w-full min-h-screen">
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Script to render Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
