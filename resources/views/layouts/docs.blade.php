<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Instudy API Documentation')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Prism.js for syntax highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>

    <style>
        /* Custom styles for API documentation */
        body {
            overflow-x: hidden;
        }
        html {
            overflow-x: hidden;
        }
        .method-get { @apply bg-green-100 text-green-800; }
        .method-post { @apply bg-blue-100 text-blue-800; }
        .method-put { @apply bg-yellow-100 text-yellow-800; }
        .method-delete { @apply bg-red-100 text-red-800; }

        .auth-badge { @apply bg-green-100 text-green-800; }
        .no-auth-badge { @apply bg-gray-100 text-gray-800; }
        .error-badge { @apply bg-red-100 text-red-800; }

        pre[class*="language-"] {
            @apply bg-gray-900 text-gray-100 rounded-lg p-4 overflow-x-auto;
        }

        code {
            @apply bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm;
        }

        pre code {
            @apply bg-transparent text-gray-100 p-0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-900">Instudy API</h1>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">v1.0</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/68c4283db8074b12df1660b31c0220a9" class="text-gray-600 hover:text-gray-900 text-sm">
                        📊 Статистика
                    </a>
                    <a href="/" class="text-gray-600 hover:text-gray-900 text-sm">
                        ← Главная
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content with Sidebar -->
    <div class="flex min-h-screen overflow-x-hidden">
        <!-- Navigation Sidebar -->
        <nav class="w-64 bg-white shadow-lg border-r border-gray-200 min-h-screen overflow-y-auto flex-shrink-0" style="width: 16rem; background-color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-right: 1px solid #e5e7eb; min-height: 100vh; overflow-y: auto; flex-shrink: 0;">
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-4">📚 Оглавление</h3>
                <div class="space-y-3">
                    <div>
                        <a href="#overview" class="text-gray-600 hover:text-blue-600 block py-1 font-medium">📋 Обзор API</a>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">👤 Пользователи</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#user" class="text-gray-600 hover:text-blue-600 block py-1">👤 Пользователь</a></li>
                            <li><a href="#auth" class="text-gray-600 hover:text-blue-600 block py-1">🔐 Аутентификация</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">⚙️ Система</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#modules" class="text-gray-600 hover:text-blue-600 block py-1">🧩 Модули</a></li>
                            <li><a href="#widgets" class="text-gray-600 hover:text-blue-600 block py-1">📊 Виджеты</a></li>
                            <li><a href="#notifications" class="text-gray-600 hover:text-blue-600 block py-1">🔔 Уведомления</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">📅 Расписание</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#calendar" class="text-gray-600 hover:text-blue-600 block py-1">📅 Календарь</a></li>
                            <li><a href="#schedule" class="text-gray-600 hover:text-blue-600 block py-1">📅 Расписание</a></li>
                            <li><a href="#announces" class="text-gray-600 hover:text-blue-600 block py-1">📢 Анонсы</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">🎓 Образование</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#education" class="text-gray-600 hover:text-blue-600 block py-1">🎓 Образование</a></li>
                            <li><a href="#library" class="text-gray-600 hover:text-blue-600 block py-1">📖 Библиотека</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">💰 Финансы</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#finances" class="text-gray-600 hover:text-blue-600 block py-1">💰 Финансы</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">💬 Коммуникации</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#chat" class="text-gray-600 hover:text-blue-600 block py-1">💬 Чат</a></li>
                            <li><a href="#history" class="text-gray-600 hover:text-blue-600 block py-1">📚 Истории</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">📄 Документы</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#portfolio" class="text-gray-600 hover:text-blue-600 block py-1">📋 Портфолио</a></li>
                            <li><a href="#documents" class="text-gray-600 hover:text-blue-600 block py-1">📄 ЭЦП</a></li>
                        </ul>
                    </div>

                    <div class="border-t pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">🔧 Сервисы</h4>
                        <ul class="space-y-1 text-sm ml-2">
                            <li><a href="#ai" class="text-gray-600 hover:text-blue-600 block py-1">🤖 ИИ Помощник</a></li>
                            <li><a href="#support" class="text-gray-600 hover:text-blue-600 block py-1">🆘 Поддержка</a></li>
                            <li><a href="#redirect" class="text-gray-600 hover:text-blue-600 block py-1">🔄 Перенаправления</a></li>
                            <li><a href="#integration" class="text-gray-600 hover:text-blue-600 block py-1">🌐 Интеграции</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-x-auto">
            <div class="max-w-none">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-500">
                <p>Instudy API Documentation</p>
                <p class="mt-1">Время генерации: {{ now()->format('d.m.Y H:i:s') }}</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
