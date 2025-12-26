# 📚 InStudy API Documentation

> **Современная образовательная платформа с модульной архитектурой**

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

## 🚀 Быстрый старт

### Базовые настройки
- **Базовый URL:** `{{ config('app.url') }}/api`
- **Аутентификация:** Bearer Token
- **Формат данных:** JSON
- **Кодировка:** UTF-8

### Ссылки на документацию
- **Продакшен:** https://v2api.instudy.online/docs/api
- **Стейджинг:** https://dev-v2api.instudy.online/docs/api

---

## 🔐 Аутентификация

### Получение пользователя
```http
GET /user
```
**Описание:** Получение информации о текущем пользователе

**Ответ:**
```json
{
  "id": 1,
  "email": "user@example.com",
  "first_name": "Иван",
  "last_name": "Иванов",
  "middle_name": "Иванович",
  "photo_url": "https://example.com/photo.jpg",
  "lang": "ru",
  "created_at": "01.01.2024, 12:00:00",
  "updated_at": "01.01.2024, 12:00:00"
}
```

### Выход из системы
```http
DELETE /user/logout
```
**Описание:** Выход пользователя по email

### Аутентификация по профилю
```http
POST /profiles/{profile}
```
**Описание:** Аутентификация через профиль

### Выход (требует аутентификации)
```http
DELETE /logout
```
**Описание:** Выход из текущей сессии

---

## 👤 Пользователи и профили

### Получение всех профилей
```http
GET /profiles
```
**Middleware:** `auth:profiles`

**Ответ:**
```json
{
  "profiles": [
    {
      "id": 1,
      "group": "ПС5А24/10 ДСК",
      "spec_name": "Программная инженерия",
      "edu_form": "Дистанционная",
      "semester": 5,
      "course": 3,
      "unread_notifications_count": 3,
      "created_at": "01.01.2024, 12:00:00",
      "updated_at": "01.01.2024, 12:00:00"
    }
  ],
  "user": {
    "id": 1,
    "email": "user@example.com",
    "first_name": "Иван",
    "last_name": "Иванов",
    "middle_name": "Иванович",
    "photo_url": "https://example.com/photo.jpg",
    "lang": "ru"
  }
}
```

### Переключение профиля
```http
POST /profiles/{profile}/switch
```
**Middleware:** `auth:profiles`

### Получение текущего профиля
```http
GET /profile
```
**Middleware:** `auth:profiles`

### Создание пользователя SSO
```http
POST /sso/users
```

### Обновление пользователя
```http
POST /user/update
```

---

## 🧩 Модули

### Получение всех модулей
```http
GET /modules
```

### Создание модуля
```http
POST /modules
```

### Удаление модуля
```http
POST /modules/{module}/delete
```

### Перестановка модулей
```http
POST /modules/{module}/swap
```

---

## 📅 Расписание

### Получение расписания
```http
GET /schedule
```

### Получение конкретного расписания
```http
GET /schedule/{scheduleId}
```

### Получение семестра
```http
GET /semester
```

### Получение ссылки для автозаписи
```http
POST /schedule/link
```

### Получение дисциплин
```http
GET /schedule/disciplines
```

---

## 💰 Финансы

### Получение финансовой информации
```http
GET /finances
```

### Создание платежа
```http
POST /payments
```

### Получение квитанции об оплате
```http
GET /payment-receipt
```

---

## 🔔 Уведомления

### Получение количества уведомлений
```http
GET /notifications
```

### Получение списка уведомлений
```http
GET /notifications/list
```

### Подписка на push-уведомления
```http
POST /push/subscribe
```

### Отписка от push-уведомлений
```http
DELETE /push/subscribe
```

### Тестовое уведомление
```http
GET /push/test
```

---

## 💬 Чаты

### Получение чатов
```http
GET /chats
```
**Middleware:** `auth:profiles`

### Отправка сообщения
```http
POST /messages/{recipientId}
```
**Middleware:** `auth:profiles`

### Получение сообщений
```http
GET /messages/{recipientId}
```
**Middleware:** `auth:profiles`

### Отметка сообщений как прочитанных
```http
POST /read/messages
```
**Middleware:** `auth:profiles`

### Получение сообщений из ESB
```http
POST /esb/messages
```

---

## 📚 Образование

### Семестры
```http
GET /semesters
```

### Дисциплины семестра
```http
GET /semesters/{semester}/disciplines
```

### Выбор дисциплины
```http
POST /semesters/{semester}/disciplines/{disciplineId}
```
**Middleware:** `discipline.access`

### Получение дисциплины
```http
GET /disciplines/{disciplineId}
```
**Middleware:** `discipline.access`

### Тесты
```http
GET /disciplines/{disciplineId}/tests
```
**Middleware:** `discipline.access`

```http
GET /disciplines/{disciplineId}/tests/{testId}
```
**Middleware:** `discipline.access`

```http
POST /disciplines/{disciplineId}/tests/{testId}
```
**Middleware:** `discipline.access`, `debt.check:1`

### Работы
```http
GET /disciplines/{disciplineId}/works
```
**Middleware:** `discipline.access`

```http
GET /disciplines/{disciplineId}/options/work
```
**Middleware:** `discipline.access`

```http
POST /disciplines/{disciplineId}/works/{workId}
```
**Middleware:** `discipline.access`, `debt.check:1`

```http
POST /download/work
```
**Middleware:** `debt.check:1`

### Материалы
```http
POST /materials
```

```http
POST /download/materials
```

```http
GET /curator/materials
```

---

## 📄 Документы

### Получение документов
```http
GET /documents
```

### Получение файла документа
```http
GET /documents/{documentId}/file
```

### Типы документов
```http
GET /documents/types
```

### Конкретный тип документа
```http
GET /documents/types/{documentType}
```

### Опции типа документа
```http
GET /documents/types/{documentType}/options
```

### Отправка документа
```http
POST /documents/types/{documentType}
```

### Зачетная книжка
```http
GET /documents/zbook
```

### Студенческий билет
```http
GET /documents/ticket
```

### QR-код
```http
GET /documents/qr-code
```

### Подписание документов
```http
GET /sign/documents/{documentId}
```

```http
POST /sign/hand/documents/{documentId}
```

```http
POST /sign/documents/{documentId}
```

### Электронная цифровая подпись
```http
POST /eds/send_code
```

```http
POST /eds/verify_code
```

---

## 🎨 Портфолио

### Получение портфолио
```http
GET /portfolios
```

### Получение конкретного портфолио
```http
GET /portfolios/{portfolio}
```

### Создание портфолио
```http
POST /portfolios/{portfolio}
```

### Удаление портфолио
```http
POST /portfolio/{id}
```

### Скачивание портфолио
```http
POST /download/portfolios
```

---

## 📖 Библиотека

### Внешние ресурсы
```http
GET /external_resources
```

### Внешние статьи
```http
GET /external_articles
```

### Электронная библиотека
```http
GET /library
```
**Middleware:** `auth:profiles`

---

## 🤖 Искусственный интеллект

### Запрос к ИИ
```http
POST /ai/request
```

### История чата с ИИ
```http
GET /ai/chat_history
```

---

## 📢 Объявления

### Получение объявлений
```http
GET /announces
```

### Получение конкретного объявления
```http
GET /announces/{announceId}
```

---

## 📖 Истории

### Получение историй
```http
GET /stories
```
**Middleware:** `auth:profiles`, `debt.check:2`

### Получение конкретной истории
```http
GET /stories/{story}
```
**Middleware:** `auth:profiles`, `debt.check:2`

### Админ-панель историй
```http
GET /admin/stories
```
**Middleware:** `admin`

```http
GET /admin/stories/{story}
```
**Middleware:** `admin`

```http
POST /admin/stories
```
**Middleware:** `admin`

```http
PUT /admin/stories/{story}
```
**Middleware:** `admin`

```http
POST /admin/stories/{story}/pin
```
**Middleware:** `admin`

```http
POST /admin/stories/{story}/unpin
```
**Middleware:** `admin`

```http
DELETE /admin/stories/{story}
```
**Middleware:** `admin`

---

## 🎛️ Виджеты

### Получение виджета
```http
GET /widgets/{widget}
```
**Middleware:** `debt.check:2`

### Куратор
```http
GET /curator
```

---

## ⚙️ Настройки

### Получение настроек
```http
GET /settings
```
**Middleware:** `auth:profiles`

### Обновление настроек уведомлений
```http
PUT /settings/{settings}/notifications
```
**Middleware:** `auth:profiles`

### Сохранение настроек
```http
POST /settings
```
**Middleware:** `auth:profiles`, `debt.check:2`

---

## 🔄 Перенаправления

### Получение перенаправления
```http
POST /redirect
```

---

## 🆘 Поддержка

### Отправка отчета
```http
POST /assists
```

---

## 📱 Календарь

### Получение календаря по токену
```http
GET /calendar/{token}
```

### Получение ссылки на календарь
```http
GET /calendar
```
**Middleware:** `auth:profiles`, `debt.check:2`

---

## 🛠️ Техническое обслуживание

### Получение статуса обслуживания
```http
GET /maintenance
```

### Изменение статуса обслуживания
```http
POST /maintenance
```

---

## 🔐 Административные функции

### Получение доступов
```http
GET /admin/accesses
```
**Middleware:** `admin`

---

## 📊 Структура ответов

### Успешный ответ
```json
{
  "success": true,
  "data": {
    // Данные ответа
  },
  "message": "Операция выполнена успешно"
}
```

### Ошибка
```json
{
  "success": false,
  "errors": {
    "field": ["Сообщение об ошибке"]
  },
  "message": "Описание ошибки"
}
```

---

## 🔒 Middleware

- **`auth:profiles`** - Требует аутентификации через профиль
- **`admin`** - Требует административных прав
- **`debt.check:1`** - Проверка долга уровня 1
- **`debt.check:2`** - Проверка долга уровня 2
- **`discipline.access`** - Доступ к дисциплине

---

## 📝 Примечания

- Все даты возвращаются в формате `d.m.Y, H:i:s`
- Некоторые эндпоинты могут быть недоступны в зависимости от уровня долга пользователя
- Для работы с API требуется валидный Bearer Token
- Все запросы должны содержать заголовок `Content-Type: application/json`

---

## 🤝 Поддержка

Если у вас возникли вопросы или проблемы с API, обратитесь к команде разработки или создайте issue в репозитории проекта.

---

*Документация обновлена: {{ date('d.m.Y') }}*
