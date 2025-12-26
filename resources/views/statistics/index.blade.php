@extends('layouts.statistics')

@section('title', 'Статистика пользователей')

@section('content')
<div class="px-4 py-8">
    <div class="mb-8">
        <p class="text-gray-600">Общая информация о пользователях и профилях в системе</p>
    </div>

    <!-- Основная статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Всего пользователей</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Пользователи с профилями</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($usersWithProfiles) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Всего профилей</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProfiles) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Новых за 24 часа</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($newUsersLast24h) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика по долгам -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Профилей с долгами</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($profilesWithDebt) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Общая сумма долгов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalDebtAmount) }} ₽</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Средний размер долга</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($averageDebtAmount, 0) }} ₽</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Группа с наибольшими долгами</p>
                    <p class="text-lg font-bold text-gray-900">{{ $groupWithMostDebts ? $groupWithMostDebts->group : 'Нет данных' }}</p>
                    @if($groupWithMostDebts)
                        <p class="text-sm text-gray-500">{{ $groupWithMostDebts->count }} профилей</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Статистика по группам -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика по группам</h3>
            @if($groupStatistics->count() > 0)
                <div class="space-y-3">
                    @foreach($groupStatistics as $group)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">{{ $group->group ?: 'Без группы' }}</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ number_format($group->count) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Нет данных о группах</p>
            @endif
        </div>

        <!-- Статистика по уровням доступа -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика по уровням доступа</h3>
            @if($accessStatistics->count() > 0)
                <div class="space-y-3">
                    @foreach($accessStatistics as $access)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium text-gray-700">{{ $access->name }}</span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                {{ number_format($access->profiles_count) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Нет данных об уровнях доступа</p>
            @endif
        </div>
    </div>

    <!-- Статистика по долгам по группам -->
    @if($debtByGroups->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Долги по группам</h3>
            <div class="space-y-3">
                @foreach($debtByGroups as $group)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <span class="font-medium text-gray-700">{{ $group->group ?: 'Без группы' }}</span>
                            <div class="text-sm text-gray-500">
                                {{ number_format($group->count) }} профилей • {{ number_format($group->total_debt) }} ₽
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                {{ number_format($group->count) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Статистика по организациям -->
    @if($orgStatistics->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика по организациям</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($orgStatistics as $org)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700 truncate">{{ $org->org ?: 'Без организации' }}</span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium ml-2">
                            {{ number_format($org->count) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- График активности за неделю -->
    @if($weeklyActivity->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Количество регистраций за 7 дней</h3>
            <div class="space-y-2">
                @foreach($weeklyActivity as $day)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $day->count > 0 ? min(100, ($day->count / $weeklyActivity->max('count')) * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-12 text-right">{{ $day->count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Дополнительная информация -->
    <div class="mt-8 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">Дополнительная информация</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="font-medium text-blue-800">Процент пользователей с профилями:</span>
                <span class="text-blue-600 ml-2">
                    {{ $totalUsers > 0 ? number_format(($usersWithProfiles / $totalUsers) * 100, 1) : 0 }}%
                </span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Среднее количество профилей на пользователя:</span>
                <span class="text-blue-600 ml-2">
                    {{ $totalUsers > 0 ? number_format($totalProfiles / $totalUsers, 2) : 0 }}
                </span>
            </div>
            <div>
                <span class="font-medium text-blue-800">Процент профилей с долгами:</span>
                <span class="text-blue-600 ml-2">
                    {{ $totalProfiles > 0 ? number_format(($profilesWithDebt / $totalProfiles) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
