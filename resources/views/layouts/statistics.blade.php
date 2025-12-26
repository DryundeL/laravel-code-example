<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Статистика пользователей - Instudy')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Heroicons -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js" type="module"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Статистика пользователей</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900 text-sm">
                        ← Вернуться на главную
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-500">
                <p>Instudy - Система управления образованием</p>
                <p class="mt-1">Время генерации: {{ now()->format('d.m.Y H:i:s') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
