@extends('layouts.docs')

@section('title', 'Документация Instudy API')

@section('content')
    <!-- Overview Section -->
    <div id="overview" class="mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Обзор API</h2>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong class="text-blue-800">📍 Базовый URL:</strong>
                    <code class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ config('app.url') }}/api</code>
                </div>
                <div>
                    <strong class="text-blue-800">🔑 Аутентификация:</strong> Bearer Token
                </div>
                <div>
                    <strong class="text-blue-800">📝 Формат данных:</strong> JSON
                </div>
                <div>
                    <strong class="text-blue-800">⚠️ Статус:</strong> Некоторые эндпоинты могут не работать
                </div>
            </div>
        </div>
    </div>

    <!-- User Section -->
    <div id="user" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="text-3xl mr-3">👤</span>Пользователь
        </h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Получение профиля пользователя</h3>
                        <p class="text-gray-600 mt-2">
                            Получение полной информации о текущем профиле пользователя, включая доступные модули и виджеты.
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                        <code class="bg-gray-100 text-gray-800 px-3 py-1 rounded text-sm">/profile</code>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                </div>
            </div>
            <div class="p-6">
                <pre><code class="language-json">{
  "profile": {
    "id": 1,
    "access": "Студент (дистант)",
    "group": "ПС5А24/10 ДСК",
    "org": "mipvo",
    "modules": [...],
    "widgets": ["schedule", "finance", "session", "support"]
  },
  "user": {
    "firstName": "Михаил",
    "lastName": "Молдованов",
    "email": "",
    "lang": "ru"
  }
}</code></pre>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Обновление данных пользователя</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/user/update</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Обновление личных данных пользователя: email, имя, фамилия, отчество, фото профиля.
                </div>
                <div class="params">
                    <strong>📝 Параметры запроса:</strong>
                    <div class="param-item">
                        <span class="param-name">email</span> <span
                            class="param-type">(обязательно, string, max: 255)</span><br>
                        Корректный адрес электронной почты
                    </div>
                    <div class="param-item">
                        <span class="param-name">first_name</span> <span class="param-type">(необязательно, string, max: 255)</span><br>
                        Имя пользователя
                    </div>
                    <div class="param-item">
                        <span class="param-name">last_name</span> <span class="param-type">(необязательно, string, max: 255)</span><br>
                        Фамилия пользователя
                    </div>
                    <div class="param-item">
                        <span class="param-name">middle_name</span> <span class="param-type">(необязательно, string, max: 255)</span><br>
                        Отчество пользователя
                    </div>
                    <div class="param-item">
                        <span class="param-name">photo_url</span> <span class="param-type">(необязательно, string, max: 255)</span><br>
                        URL изображения профиля
                    </div>
                </div>
                <pre><code class="language-json">
{
  "email": "moldovan@test.ru",
  "photoUrl": "dfjghdfkgd"
}
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Изменение языка интерфейса</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/settings</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Изменение языка интерфейса пользователя (ru, en и т.д.).
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">lang</span> <span
                            class="param-type">(обязательно, string, max: 255)</span><br>
                        Код языка (например, "ru", "en")
                    </div>
                </div>
                <pre><code class="language-json">
{
  "lang": "ru"
}
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение списка профилей</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/profiles</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение всех доступных профилей пользователя с информацией об обучении.
                </div>
                <pre><code class="language-json">
{
  "profiles": [
    {
      "id": 1,
      "group": "ПС5А24/10 ДСК",
      "specName": "37.03.01 Психология",
      "eduLevel": "Бакалавриат",
      "eduForm": "Заочная",
      "semester": 3,
      "course": 2
    }
  ],
  "user": {...}
}

                </code></pre>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Смена активного профиля</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/profiles/{id}/switch</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Переключение между различными профилями пользователя. Возвращает новый токен авторизации.
                </div>
                <pre><code class="language-json">
{
  "profile": {...},
  "user": {...},
  "token": "191|u0QuNIu8u2CxjojAOjACZb5hsGntv24Q2xFaasCu31dc981a"
}
                </code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="auth">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🔐</span>Аутентификация</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Авторизация по профилю</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/auth/profiles/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Авторизация пользователя через конкретный профиль. Возвращает токен и данные профиля.
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">email</span> <span
                            class="param-type">(обязательно, string, max: 255)</span><br>
                        Адрес электронной почты пользователя
                    </div>
                </div>
                <pre><code class="language-json">
{
  "email": "user@example.com"
}
                </code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="modules">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🧩</span>Модули системы</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Создание модуля</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/modules</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Создание нового модуля в системе с настройками отображения и доступов.
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">name</span> <span
                            class="param-type">(обязательно, string, max: 255)</span><br>
                        Название модуля
                    </div>
                    <div class="param-item">
                        <span class="param-name">desc</span> <span
                            class="param-type">(обязательно, string, max: 255)</span><br>
                        Описание модуля
                    </div>
                    <div class="param-item">
                        <span class="param-name">icon</span> <span class="param-type">(обязательно, boolean)</span><br>
                        Наличие иконки модуля
                    </div>
                    <div class="param-item">
                        <span class="param-name">show</span> <span class="param-type">(необязательно, array)</span><br>
                        Массив платформ для отображения: ["mobile", "desktop"]
                    </div>
                    <div class="param-item">
                        <span class="param-name">inuse</span> <span class="param-type">(обязательно, boolean)</span><br>
                        Активность модуля
                    </div>
                </div>
                <pre><code class="language-json">
{
  "name": "education",
  "desc": "Обучение",
  "icon": true,
  "show": ["mobile", "desktop"],
  "inuse": true
}
                </code></pre>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение списка модулей</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/modules</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Получение всех модулей системы с информацией о доступах и настройках.
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Удаление модуля</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/modules/{id}/delete</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Удаление модуля из системы по его идентификатору.
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Изменение порядка модулей</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/modules/{id}/swap</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Изменение порядка отображения модулей в интерфейсе.
                </div>
                <pre><code class="language-json">
{
    "order": 1
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="widgets">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📊</span>Виджеты</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Виджет расписания</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/widgets/schedule</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение данных для виджета расписания на главной странице.
                </div>
                <pre><code class="language-json">
[
  {
    "id": 1068,
    "discipline": "Марафон магистерских программ",
    "exam": 5,
    "online": 1,
    "cancel": 0,
    "period": "18:00 - 20:00",
    "isPassed": false,
    "autoRegLink": false,
    "route": ""
  }
]
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Виджет финансов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/widgets/finance</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">⚠️ Нет данных</span>
                <div class="description">
                    Получение финансовой информации для отображения в виджете.
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Виджет сессии</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/widgets/session</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение информации о текущей экзаменационной сессии.
                </div>
                <pre><code class="language-json">
{
    "errors":
    {
        "error": ["Данных нет"]
    }
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Виджет куратора</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/curator</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение информации о кураторе группы.
                </div>
                <pre><code class="language-json">
{
    "avatar": ""
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="calendar">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📅</span>Календарь</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение ссылки на календарь</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/calendar</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение персональной ссылки на календарь событий в формате iCal.
                </div>
                <pre><code class="language-json">
{
    "link": "http://localhost/api/calendar/hRtpJwd3iEi9zpwTcOYz3AmEfXhsWOCK"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Календарь в формате iCal</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/calendar/{calendar_token}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                <div class="description">
                    Получение календаря событий в формате iCal для импорта в календарные приложения.
                </div>
                <pre><code class="language-json">
BEGIN:VCALENDAR
VERSION:2.0
PRODID:spatie/icalendar-generator
NAME:InStudy Events

BEGIN:VEVENT
UID:6833aa7694732
SUMMARY:Научные школы и теории в современной психологии
DESCRIPTION:Онлайн событие
URL:https://video.instudy.online/webinar/441032b0...
DTSTART;TZID=Europe/Moscow:20240904T185000
END:VEVENT

END:VCALENDAR
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Проверка курса</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/course/check</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Проверка доступности курса (тестовый эндпоинт без валидации).
                </div>
                <pre><code class="language-json">
{
    "test": "test"
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="announces">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📢</span>Анонсы и объявления</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список анонсов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/announces</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка анонсов и объявлений с фильтрацией и пагинацией.
                </div>
                <div class="params">
                    <strong>🔍 Query-параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">type</span> - Тип анонсов: "Все" или "Важно"
                    </div>
                    <div class="param-item">
                        <span class="param-name">limit</span> - Количество записей (по умолчанию: 15)
                    </div>
                    <div class="param-item">
                        <span class="param-name">offset</span> - Смещение для пагинации (по умолчанию: 0)
                    </div>
                </div>
                <pre><code class="language-json">
{
  "errors": {
    "error": ["Данных нет"]
  }
}
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение анонса по ID</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/announces/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">⚠️ Нет данных</span>
                <div class="description">
                    Получение подробной информации об анонсе по его идентификатору.
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="schedule">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📅</span>Расписание</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение расписания</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/schedule</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение расписания занятий с различными фильтрами и параметрами.
                </div>
                <div class="params">
                    <strong>🔍 Query-параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">semester</span> <span class="param-type">(integer, 1-10)</span> - Номер
                        семестра
                    </div>
                    <div class="param-item">
                        <span class="param-name">date</span> <span class="param-type">(date)</span> - Конкретная дата
                    </div>
                    <div class="param-item">
                        <span class="param-name">date_from</span> <span class="param-type">(date)</span> - Дата начала
                        периода
                    </div>
                    <div class="param-item">
                        <span class="param-name">date_to</span> <span class="param-type">(date)</span> - Дата окончания
                        периода
                    </div>
                    <div class="param-item">
                        <span class="param-name">info</span> <span class="param-type">("full" | "short")</span> - Объем
                        информации
                    </div>
                    <div class="param-item">
                        <span class="param-name">search</span> <span class="param-type">(string)</span> - Поисковый
                        запрос
                    </div>
                    <div class="param-item">
                        <span class="param-name">disciplines[]</span> <span class="param-type">(array)</span> - ID
                        дисциплин
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение события по ID</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/schedule/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">⚠️ Нет данных</span>
                <div class="description">
                    Получение подробной информации о конкретном событии расписания.
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Информация о семестре</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/semester</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение информации о датах начала и окончания текущего семестра.
                </div>
                <pre><code class="language-json">
{
  "start": "01.04.2025",
  "finish": "11.10.2025"
}
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Авто-регистрация на вебинар</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/schedule/link</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">❌ Не работает</span>
                <div class="description">
                    Автоматическая регистрация на вебинар по ссылке.
                </div>
                <pre><code class="language-json">
{
  "route": "https://events.webinar.ru/3888915/1604766315"
}
                </code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Дисциплины расписания</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/schedule/disciplines</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">❌ Не работает</span>
                <div class="description">
                    Получение списка дисциплин для фильтрации расписания.
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="finances">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">💰</span>Финансы</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Финансовая информация</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/finances</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение полной финансовой информации: задолженности, платежи, скидки, график платежей.
                </div>
                <pre><code class="language-json">
{
  "currentSemester": 2,
  "isVacation": false,
  "expectedPayment": 0,
  "financeInfo": {
    "paidSemesters": 2,
    "totalSemesters": 5,
    "debt": 0,
    "paidSum": 220000,
    "nextPayment": {
      "sum": 24200,
      "date": "07.10.2025"
    },
    "totalSum": 583000,
    "activeDiscounts": [...]
  },
  "paymentsHistory": [...],
  "paymentsSchedule": {...},
  "invoices": [...]
}
</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Загрузка документа об оплате</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/payments</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Загрузка документа, подтверждающего оплату (чек, квитанция и т.д.).
                </div>
                <div class="params">
                    <strong>📝 Form-data параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">file</span> <span class="param-type">(file)</span> - Файл документа
                    </div>
                    <div class="param-item">
                        <span class="param-name">summ</span> <span class="param-type">(text)</span> - Сумма платежа
                    </div>
                    <div class="param-item">
                        <span class="param-name">date</span> <span class="param-type">(text)</span> - Дата платежа
                    </div>
                    <div class="param-item">
                        <span class="param-name">comment</span> <span class="param-type">(text)</span> - Комментарий
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="history">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📚</span>Истории и новости</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список историй</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/stories</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка новостных историй с изображениями и заголовками.
                </div>
                <pre><code class="language-json">
[
  {
    "id": 7,
    "name": "Название истории",
    "title": "Заголовок истории",
    "description": "Подробное описание истории",
    "photoUrl": "https://storage.yandexcloud.net/instudy-dev/stories/00eb6be0-6bd6-4f6d-9c22-98a054bd388e.svg",
    "pinned": false,
    "link": null,
    "previewUrl": "https://storage.yandexcloud.net/instudy-dev/stories/1a89d082-1c28-4728-8da1-3b7fd2fad42a.svg"
  },
  {
    "id": 5,
    "name": "Сбой рунета",
    "title": "В работе Рунета произошел масштабный сбой",
    "description": "В работе Рунета произошел масштабный сбой...",
    "photoUrl": "https://storage.yandexcloud.net/instudy-dev/stories/1736867927.jpg",
    "pinned": false,
    "link": null,
    "previewUrl": null
  }
]
                </code></pre>
                <div class="description">
                    <strong>📝 Описание полей:</strong><br>
                    <strong>pinned</strong> - Закреплена ли история в топе<br>
                    <strong>description</strong> - Полное описание истории<br>
                    <strong>previewUrl</strong> - URL превью изображения (может быть null)<br>
                    <strong>link</strong> - Ссылка на внешний ресурс (может быть null)
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Просмотр истории</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/stories/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение подробной информации об истории по ID.
                </div>
                <pre><code class="language-json">
{
  "id": 6,
  "name": "fffffffffffffffff aasd",
  "title": "fasdads",
  "description": "123",
  "photoUrl": "https://storage.yandexcloud.net/instudy-dev/stories/68fcdfc1-8352-4967-a55c-efdd1433c3d7.jpg",
  "createdAt": "2025-05-26T00:31:47.000000Z",
  "updatedAt": "2025-05-27T12:19:36.000000Z",
  "pinned": true,
  "link": null,
  "previewUrl": "https://storage.yandexcloud.net/instudy-dev/stories/045bea71-fd99-44c4-aeda-720599359379.svg",
  "pivot": {
    "accessId": 1,
    "storyId": 6
  }
}
                </code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="education">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🎓</span>Образование</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список семестров</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/semesters</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение информации о доступных семестрах и задолженностях.
                </div>
                <pre><code class="language-json">
{
    "currentSemester": 3,
    "retake": true,
    "availableSemesters": [
       {
          "semester": 1,
          "hasDebts": true
       },
       {
          "semester": 2,
          "hasDebts": true
       },
       {
          "semester": 3,
          "hasDebts": false
      }
   ]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Дисциплины семестра</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/semesters/{semester}/disciplines</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка дисциплин конкретного семестра с информацией об экзаменах и оценках.
                </div>
                <pre><code class="language-json">
{
    "disciplines": [
        {
            "id": 34,
            "name": "Учебная практика...",
            "exam": "Диф. зачёт",
            "workTypes": ["work"],
            "hasGrade": false
        }
    ],
    "extraDisciplines": [...]
    }</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Информация о дисциплине</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/disciplines/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение подробной информации о дисциплине: преподаватели, видео, вебинары, материалы.
                </div>
                <pre><code class="language-json">
{
    "name": "Анатомия и физиология человека",
    "teachers": [...],
    "teacherSelect": true,
    "exam": "Диф. зачёт",
    "grade": {...},
    "videos": [...],
    "webinars": [],
    "workTypes": [],
    "hasMaterials": false
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Материалы дисциплины</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/materials</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка материалов по дисциплине или от куратора.
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">type</span> <span class="param-type">(обязательно)</span> -
                        "discipline" или "curator"
                    </div>
                    <div class="param-item">
                        <span class="param-name">discipline_id</span> <span
                            class="param-type">(для type="discipline")</span> - ID дисциплины
                    </div>
                    <div class="param-item">
                        <span class="param-name">path</span> <span class="param-type">(необязательно)</span> - Путь к
                        папке
                    </div>
                </div>
                <pre><code class="language-json">
{
    "type": "discipline",
    "discipline_id": 3863,
    "path": "1.Онлайн-курс"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Скачивание материалов</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/download/materials</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Скачивание файлов материалов по пути.
                </div>
                <pre><code class="language-json">
{
    "path": "Лицензия МИП/document.pdf",
    "type": "curator"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список тестов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/disciplines/{id}/tests</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка доступных тестов по дисциплине.
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">workType</span> - "tests" или "final_tests"
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Прохождение теста</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/disciplines/{disciplineId}/tests/{testId}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">❌ Ошибка</span>
                <div class="description">
                    Отправка ответов на тест с историей выбранных вариантов.
                </div>
                <pre><code class="language-json">
{
    "rtime": 12,
    "history": {
       "Вопрос 1": [2],
       "Вопрос 2": [1, 3]
    }
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список работ</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/disciplines/{id}/works</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка отправленных работ по дисциплине.
                </div>
                <pre><code class="language-json">
[
    {
        "id": 1172910,
        "workName": "Реферат",
        "upload": "f1573c11d9d52f015c91a4738185395e",
        "comm": "Комментарий студента",
        "teacherName": "Бороздина Надежда Олеговна",
        "point": null,
        "date": "18.02.2025 12:53:08"
    }
]</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Отправка работы</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/disciplines/{disciplineId}/works/{workId}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Отправка выполненной работы преподавателю.
                </div>
                <div class="params">
                    <strong>📝 Form-data параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">assent_id</span> <span
                            class="param-type">(обязательно, integer > 0)</span>
                    </div>
                    <div class="param-item">
                        <span class="param-name">teacher_id</span> <span
                            class="param-type">(необязательно, integer > 0)</span>
                    </div>
                    <div class="param-item">
                        <span class="param-name">comment</span> <span class="param-type">(обязательно, string)</span>
                    </div>
                    <div class="param-item">
                        <span class="param-name">file</span> <span class="param-type">(обязательно, file)</span> - doc,
                        docx, pdf, ppt, pptx, xls, xlsx, png, jpg, jpeg
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Параметры работы</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/disciplines/{id}/options/work</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка преподавателей и доступных работ для отправки.
                </div>
                <pre><code class="language-json">
{
    "teachers": [
    {
        "id": 16188,
        "name": "Клемешов Алексей Станиславович"
    }
    ],
    "works": []
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Скачивание работы</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/download/work</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">❌ Файл не найден</span>
                <div class="description">
                    Скачивание ранее отправленной работы по имени файла.
                </div>
                <pre><code class="language-json">
{
    "name": "25122024154747676bfef39dea5"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Материалы куратора</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/curator/materials</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка вебинаров и материалов от куратора группы.
                </div>
                <pre><code class="language-json">
{
    "webinars": [
        {
            "id": 65115,
            "name": "Организационное собрание с куратором",
            "url": ""
        }
    ]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Выбор дисциплины</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/semesters/{semester}/disciplines/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Выбор элективной дисциплины для изучения в семестре.
                </div>
                <pre><code class="language-json">
true

// Дисциплина уже выбрана
{
    "errors": {
    "discipline": [
    "Дисциплина уже выбрана"
        ]
    }
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Элективные дисциплины</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/elective/disciplines</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка элективных (по выбору) дисциплин.
                </div>
                <pre><code class="language-json">
[
    {
        "id": 2837,
        "name": "English for Beginners. Онлайн-курс"
    },
    {
        "id": 2417,
        "name": "Актуальные проблемы клинической и медицинской психологии"
    },
    {
        "id": 1402,
        "name": "Киноклуб"
    },
    {
        "id": 116,
        "name": "Психоанализ: За и против"
    }
]</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Видео элективов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/elective/videos</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение видеоматериалов по элективным дисциплинам.
                </div>
                <pre><code class="language-json">
[
    {
        "date": "05 октября 2020",
        "name": "Открытые лекции и семинары преподавателей института",
        "link": "https://clck.ru/RJVB4"
    },
    {
        "date": "30 сентября 2020",
        "name": "Презентационные ролики по программам образования",
        "link": "https://clck.ru/RJVFp"
    },
    {
        "date": "23 марта 2020",
        "name": "Горячая линия психолога (Лекции по Covid19)",
        "link": "https://clck.ru/RJV4M"
    }
]</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="chat">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">💬</span>Чат и сообщения</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Отправка сообщения</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/messages/{recipientId}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Отправка текстового сообщения или файлов конкретному получателю.
                </div>
                <div class="params">
                    <strong>📝 Form-data параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">message</span> <span class="param-type">(необязательно, string)</span>
                        - Текст сообщения
                    </div>
                    <div class="param-item">
                        <span class="param-name">attachments[]</span> <span class="param-type">(необязательно, files, max: 10 шт, 10MB каждый)</span><br>
                        Прикрепляемые файлы: jpeg, jpg, png, gif, pdf, csv, xlsx, xls, docx, doc, pptx, ppt
                    </div>
                </div>
                <pre><code class="language-json">
{
    "id": 1279039,
    "text": "Привет",
    "read": 0,
    "answered": 0,
    "date": "06.03.2025 19:17:59",
    "sendByCurrentUser": true,
    "type": "text"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение сообщений</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/messages/{recipientId}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение истории сообщений с конкретным пользователем.
                </div>
                <div class="params">
                    <strong>🔍 Query-параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">limit</span> <span
                            class="param-type">(необязательно, integer ≥ 0)</span> - Количество сообщений
                    </div>
                    <div class="param-item">
                        <span class="param-name">offset</span> <span
                            class="param-type">(необязательно, integer ≥ 0)</span> - Смещение для пагинации
                    </div>
                </div>
                <pre><code class="language-json">
{
    "partner": {
    "id": 777,
    "fullName": "пс1а18/08",
    "role": "Студент",
    "photoUrl": null,
    "messages": [
    {
        "text": "Привет",
        "read": 0,
        "answered": 0,
        "auto": 0,
        "date": "19.02.2025 18:09:57",
        "sendByCurrentUser": true,
        "id": 1261040,
        "type": "text"
    },
    {
        "text": "приём",
        "read": 1,
        "answered": 1,
        "auto": 0,
        "date": "06.10.2015 16:21:27",
        "sendByCurrentUser": false,
        "id": 34024,
        "type": "text"
        },
    {
        "read": 1,
        "answered": 0,
        "auto": 0,
        "date": "12.04.2018 15:30:07",
        "link": "https://dist.inpsycho.ru/uploads/message/fc45cd8f3b1eeaa90b71c474711439c05acf514f40d50.zip",
        "fileName": "Новый текстовый документ.txt",
        "sendByCurrentUser": false,
        "id": 262548,
        "type": "file"
         },
         {
             "read": 1,
             "answered": 0,
             "auto": 1,
             "date": "16.01.2023 13:44:49",
             "link": "/spravkanew",
             "fileName": "Справки и заявления",
             "sendByCurrentUser": false,
             "id": 880325,
             "type": "text"
         }
    ]
}</code></pre>
                <div class="description">
                    <strong>📝 Описание полей сообщения:</strong><br>
                    <strong>type</strong> - Тип: "text" (текст) или "file" (файл)<br>
                    <strong>read</strong> - Статус прочтения (0/1)<br>
                    <strong>sendByCurrentUser</strong> - Отправлено ли текущим пользователем<br>
                    <strong>auto</strong> - Автоматическое сообщение (0/1)<br>
                    <strong>link</strong> - Ссылка на файл (для type="file")<br>
                    <strong>fileName</strong> - Имя файла (для type="file")
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список чатов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/chats</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка всех чатов пользователя с фильтрацией и поиском.
                </div>
                <div class="params">
                    <strong>🔍 Query-параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">group</span> <span class="param-type">(необязательно)</span> -
                        "classmates", "teachers", "curators"
                    </div>
                    <div class="param-item">
                        <span class="param-name">query</span> <span class="param-type">(необязательно, string)</span> -
                        Поисковый запрос
                    </div>
                </div>
                <pre><code class="language-json">
{
    "curator": {
    id": 33521,
    "name": "Шилягина Екатерина Николаевна",
    "role": "Куратор",
    "haveUnreadMessages": false,
    "photoUrl": "https://..."
    },
    "chats": [...]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Отметка о прочтении</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/read/messages</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Отметка сообщений как прочитанных.
                </div>
                <pre><code class="language-json">
{
    "messages_id": [1248459, 1248394]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Авторизация вещания чата</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/broadcasting/auth</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Авторизация для получения real-time уведомлений через WebSocket.
                </div>
                <pre><code class="language-json">
{
    "socket_id": "228459032.938609974",
    "channel_name": "private-chat.1.777"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">ESB сообщение</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/esb/messages</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    API для интеграции с корпоративной шиной сообщений (Enterprise Service Bus).
                </div>
                <pre><code class="language-json">
{
    "message": {
    "from": 777,
    "to": 5,
    "auto": 0,
    "text": "Текст сообщения",
    "partner": {
        "id": 777,
        "full_name": "Имя отправителя"
    }
 }
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="notifications">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🔔</span>Уведомления</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Статус уведомлений</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/notifications</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение информации о непрочитанных уведомлениях и сообщениях.
                </div>
                <pre><code class="language-json">
{
        "chat": {
            "unreadMessages": 0
        }
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="portfolio">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📋</span>Портфолио</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Категории портфолио</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/portfolios</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка категорий портфолио для группировки документов.
                </div>
                <pre><code class="language-json">
[
    {"id": 1, "name": "Резюме"},
    {"id": 2, "name": "Грамоты"},
    {"id": 3, "name": "Свидетельства"},
    {"id": 4, "name": "Удостоверения и дипломы"},
    {"id": 5, "name": "Письменные работы"},
    {"id": 6, "name": "Рекомендательные письма"}
    {"id": 7, "name": "Сертификаты"}
]</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Файлы категории</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/portfolios/{portfolio}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка файлов в конкретной категории портфолио.
                </div>
                <pre><code class="language-json">
{
    "name": "Грамоты",
    "files": [
        {
            "id": 6855,
            "name": "Грамота за участие",
            "file": "3108201710521559a7c02f97785"
        }
    ]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Загрузка файла</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/portfolios/{portfolio}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Загрузка нового файла в категорию портфолио.
                </div>
                <div class="params">
                    <strong>📝 Form-data параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">name</span> <span
                            class="param-type">(обязательно, string, max: 255)</span> - Название файла
                    </div>
                    <div class="param-item">
                        <span class="param-name">file</span> <span
                            class="param-type">(обязательно, file, max: 30MB)</span><br>
                        Файл: jpg, jpeg, png, pdf, doc, docx
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Удаление файла</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/portfolio/{id}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Удаление файла из портфолио.
                </div>
                <pre><code class="language-json">
{
    "file": "abcdef1234567890"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Скачивание файла</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/download/portfolios</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Скачивание файла из портфолио в ZIP-архиве.
                </div>
                <pre><code class="language-json">
{
    "file": "abcdef1234567890"
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="redirect">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🔄</span>Перенаправления</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Получение перенаправления</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/redirect</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение корректного маршрута для перенаправления пользователя.
                </div>
                <pre><code class="language-json">
{
    "path": "/some/route"
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="library">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📖</span>Библиотека</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Доступ к библиотеке</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/library</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение ссылки для автоматического входа в электронную библиотеку.
                </div>
                <pre><code class="language-json">
{
    "url": "https://www.iprbookshop.ru/autologin?..."
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Внешние ресурсы</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/external_resources</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка внешних образовательных ресурсов по категориям.
                </div>
                <pre><code class="language-json">
[
    {
        "category": "Психология",
        "resources": [
            {
                "name": "Психологический журнал",
                "url": "https://..."
           }
        ]
   }
]</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="support">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🆘</span>Техническая поддержка</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Отправка запроса в поддержку</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/assists</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Отправка запроса в техническую поддержку с описанием проблемы.
                </div>
                <div class="params">
                    <strong>📝 Form-data параметры:</strong>
                    <div class="param-item">
                        <span class="param-name">description</span> <span
                            class="param-type">(обязательно, string)</span> - Описание проблемы
                    </div>
                    <div class="param-item">
                        <span class="param-name">url</span> <span class="param-type">(обязательно, url)</span> - URL
                        страницы с проблемой
                    </div>
                    <div class="param-item">
                        <span class="param-name">file</span> <span
                            class="param-type">(необязательно, file, max: 10MB)</span> - Скриншот (jpg, jpeg, png)
                    </div>
                    <div class="param-item">
                        <span class="param-name">is_automatic</span> <span
                            class="param-type">(необязательно, boolean)</span> - Автоматический отчет
                    </div>
                </div>
                <pre><code class="language-json">
{
    "description": "Описание проблемы",
    "url": "https://example.com/problem",
    "is_automatic": false
}</code></pre>
            </div>
        </div>
    </div>

    <div class="section" id="ai">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🤖</span>ИИ Помощник</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Запрос к ИИ</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/ai/request</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Отправка запроса к ИИ помощнику для получения рекомендаций по навигации.
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">query</span> <span class="param-type">(обязательно, string)</span> -
                        Поисковый запрос
                    </div>
                    <div class="param-item">
                        <span class="param-name">score</span> <span
                            class="param-type">(необязательно, integer, 1-100)</span> - Минимальный порог релевантности
                    </div>
                </div>
                <pre><code class="language-json">
{
    "query": "куратор",
    "score": 80
}</code></pre>
                <div class="description">
                    <strong>📤 Ответ:</strong>
                </div>
                <pre><code class="language-json">
{
    "id": 1,
    "query": "куратор",
    "response": [
    {
        "route": "/curator",
        "score": 100,
        "title": "Куратор"
    }
],
    "createdAt": "26.05.2025 00:21:01"
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">История запросов к ИИ</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/ai/chat_history</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение истории всех запросов пользователя к ИИ помощнику.
                </div>
                <div class="language-json">
                    [
                    {
                    "id": 1,
                    query": "куратор",
                    "response": [...],
                    "createdAt": "26.05.2025 00:21:01"
                    }
                    ]
                </div>
            </div>
        </div>
    </div>

    <div class="section" id="documents">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📄</span>Электронные документы (ЭЦП)</h2>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Типы документов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/documents/types</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка доступных типов электронных документов для оформления.
                </div>
                <pre><code class="language-json">
[
    {
        "id": 1,
        "name": "Заявление об изменении персональных данных",
        "type": "personal"
    },
    {
        "id": 2,
        "name": "Заявление об отчислении",
        "type": "expel"
    },
    {
        "id": 3,
        "name": "Заявление о переводе внутри ВУЗа",
        "type": "transfer"
    },
    {
        "id": 4,
        "name": "Заявление о переводе на ИУП",
        "type": "iup"
    },
    {
        "id": 5,
        "name": "Заявление о получении выписки из ЗЭВ",
        "type": "vypiska"
    },
    {
        "id": 6,
        "name": "Заявление о предоставлении академического отпуска",
        "type": "academ"
    },
    {
        "id": 7,
        "name": "Заявление о предоставлении скидки за единовременный платеж",
        "type": "discount"
    },
    {
        "id": 8,
        "name": "Заявление о предоставлении социальной скидки",
        "type": "discount_social"
    }
]</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Информация о типе документа</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/documents/types/{type}</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение подробной информации о конкретном типе документа, включая описание процедуры и активные
                    скидки.
                </div>
                <pre><code class="language-json">
{
    "id": 8,
    "name": "Заявление о предоставлении социальной скидки",
    "type": "discount_social",
    "description": "Уважаемые студенты, для подтверждения скидки по указанной в списке ниже категории обязательно предоставление подтверждающего документа. Документ, подтверждающий основание для предоставления скидки необходимо загрузить в графе ниже в формате скана/ фото в хорошем качестве. Скидка подается заранее перед началом каждого учебного года. Скидки не суммируются (кроме единовременной оплаты перед началом семестра).",
    "activeDiscounts": [
        {
            "discountId": "000000023",
            "discountName": "Скидка за единовременный платеж",
            "discountSize": 11000,
            "discountType": "fixed",
            "semesterBegin": 2,
            "semesterEnd": 2
        }
    ]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Параметры для документа</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/documents/types/{type}/options</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение доступных параметров для оформления документа (факультеты, специальности, формы обучения).
                </div>
                <pre><code class="language-json">
{
    "educationLevel": [
        {
            "name": "Магистратура",
            "id": "000000003",
            "faculty": [
                {
                    "name": "Департамент культурных исследований",
                    "id": "000000076",
                    "speciality": [
                        {
                            "name": "51.04.01 Культурология",
                            "id": "000226",
                            "profile": [
                                {
                                    "name": "Визуальная антропология...",
                                    "id": "0000000128",
                                    "educationForm": [...]
                                }
                            ]
                        }
                    ]
                }
            ]
        }
    ]
}</code></pre>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Список документов</div>
                <div class="method-url">
                    <span class="method-get px-3 py-1 rounded-full text-sm font-medium">GET</span>
                    <span>/documents</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Получение списка всех электронных документов пользователя и информации об электронной подписи.
                </div>
                <pre><code class="language-json">
{
    "signature": {
    "haveSign": true,
    "isActive": true,
    "dateBegin": "21.05.2025 15:44:32",
    "dateEnd": "20.05.2027 23:59:59",
    "signatureUid": "66215503-32f4-4038-8fcd-40c46a10b17d",
    "file": "531b2987-3641-11f0-8c8b-00155d017d0f"
    },
    "requests": [
        {
            "id": 33197,
            "status": {
                "name": "Выполнено",
                "type": "success"
                },
            "title": "Заявление о предоставлении электронной подписи",
            "date": "21.05.2025 15:44:33"
        },
        {
            "id": 33192,
            "status": {
            "name": "Подано",
            "type": "default"
        },
        "title": "Заявление о предоставлении социальной скидки",
        "date": "21.05.2025 09:12:19"
    }
    ],
    "documents": []
}</code></pre>
                <div class="description">
                    <strong>📝 Структура ответа:</strong><br>
                    <strong>signature</strong> - Информация об ЭЦП (наличие, период действия, идентификаторы)<br>
                    <strong>requests</strong> - Массив поданных заявлений с их статусами<br>
                    <strong>documents</strong> - Дополнительные документы (в данном случае пустой)
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Отправка SMS-кода</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/eds/send_code</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Отправка SMS с кодом подтверждения для работы с электронной подписью.
                </div>
                <pre><code class="language-json">
{
    "success": true,
    "device": "phone"
}</code></pre>
                <div class="description">
                    <strong>📝 Описание:</strong><br>
                    Эндпоинт инициирует отправку SMS-кода на номер телефона, привязанный к учетной записи пользователя.
                    Поле <code>device</code> указывает на способ доставки кода ("phone" для SMS).
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md border mb-6">
            <div class="border-b p-6">
                <div class="endpoint-title">Проверка SMS-кода</div>
                <div class="method-url">
                    <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                    <span>/eds/verify_code</span>
                </div>
            </div>
            <div class="p-6">
                <span class="auth-badge px-3 py-1 rounded-full text-sm font-medium">🔐 Bearer Token</span>
                <div class="description">
                    Верификация SMS-кода для активации работы с электронными документами.
                </div>
                <div class="params">
                    <div class="param-item">
                        <span class="param-name">code</span> <span class="param-type">(необязательно, integer, max: 6 цифр)</span>
                        - SMS-код подтверждения
                    </div>
                </div>
                <pre><code class="language-json">
    {
        "code": 458964
    }</code></pre>
                <div class="description">
                    <strong>📤 Ответ:</strong>
                </div>
                <pre><code class="language-json">
{
    "success": true,
    "message": "Код успешно подтвержден."
}</code></pre>
            </div>
        </div>


        <div class="section" id="integration">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">🌐</span>Внешние интеграции</h2>

            <div class="bg-white rounded-lg shadow-md border mb-6">
                <div class="border-b p-6">
                    <div class="endpoint-title">События университета</div>
                    <div class="method-url">
                        <span class="method-post px-3 py-1 rounded-full text-sm font-medium">POST</span>
                        <span>https://api.inpsycho.ru/api/get-content</span>
                    </div>
                </div>
                <div class="p-6">
                    <span class="no-auth-badge px-3 py-1 rounded-full text-sm font-medium">🚫 Без аутентификации</span>
                    <span class="error-badge px-3 py-1 rounded-full text-sm font-medium">🔗 Внешний API</span>
                    <div class="description">
                        Получение контента с внешнего API университета (не относится к основной системе).
                    </div>
                    <div class="params">
                        <strong>📝 Form-data параметры:</strong>
                        <div class="param-item">
                            <span class="param-name">route</span> <span class="param-type">(text)</span> - Маршрут для
                            получения контента (например: "/lecture_hall")
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center"><span class="text-3xl mr-3">📊</span>Сводка по API</h2>
            <div class="warning">
                <h3>⚠️ Важные замечания:</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Некоторые эндпоинты возвращают ошибки "Данных нет" - это нормальное поведение при отсутствии
                        контента
                    </li>
                    <li>Эндпоинты с пометкой "❌ Не работает" требуют доработки на стороне сервера</li>
                    <li>Для работы с файлами используются разные методы: form-data для загрузки, JSON для получения
                        ссылок
                    </li>
                    <li>Bearer токены получаются при авторизации и имеют ограниченное время жизни</li>
                    <li>Некоторые эндпоинты требуют специальных прав доступа в зависимости от роли пользователя</li>
                </ul>
            </div>

            <div class="params">
                <h3>🔧 Рекомендации для разработчиков:</h3>
                <div class="param-item">
                    <strong>Обработка ошибок:</strong> Всегда проверяйте поле "errors" в ответах API
                </div>
                <div class="param-item">
                    <strong>Пагинация:</strong> Используйте параметры limit и offset для больших списков
                </div>
                <div class="param-item">
                    <strong>Файлы:</strong> Максимальные размеры файлов варьируются от 2MB до 30MB в зависимости от типа
                </div>
                <div class="param-item">
                    <strong>Валидация:</strong> Соблюдайте ограничения на длину строк и типы данных
                </div>
                <div class="param-item">
                    <strong>WebSocket:</strong> Используйте /broadcasting/auth для real-time уведомлений
                </div>
            </div>
        </div>
@endsection
