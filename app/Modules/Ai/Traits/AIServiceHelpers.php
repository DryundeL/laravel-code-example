<?php

namespace App\Modules\Ai\Traits;

use Illuminate\Support\Facades\Log;

trait AIServiceHelpers
{
    /**
     * Build the system prompt for routing and expert modes.
     * @throws \JsonException
     */
    private function buildRoutingPrompt(array $pages, string $userQuery): string
    {
        $optimizedPages = $this->optimizeCatalogForPrompt($pages);

        $rules = <<<'RULES'
ТЫ — НАВИГАЦИОННЫЙ АССИСТЕНТ InStudy.
Задачи: 1) Находить разделы/страницы. 2) Давать краткие учебные определения.

ТЕМЫ
— Только нейтральные учебные/организационные вопросы. Запрещены политика, оружие, наркотики, экстремизм и т.п.

РЕЖИМЫ
ROUTING (по умолчанию) → до 3 страниц. Формат: $response[] = [ 'title' => '', 'route' => '' ];
EXPERT → ≤150 символов, 1 предложение. Формат: $response[] = [ 'type' => 'expert', 'content' => '<ОТВЕТ>' ];
COMBINED → 1 EXPERT, затем 1–3 ROUTING.

КЛАССИФИКАЦИЯ
— Привет/спасибо/эмодзи/нет задачи → EXPERT: 'Здравствуйте! Чем могу помочь?'
— «где/как найти/открыть/перейти» + любое упоминание раздела/страницы → только ROUTING
— Одно слово-название раздела (документы/финансы/расписание и т.п.) → только ROUTING
— Вопросы о переходе/навигации (как перейти/как открыть/где находится) → только ROUTING
— «что такое/объясни/расскажи про» (учебный термин, не про навигацию) → только EXPERT
— Нужны и пояснение, и переход → COMBINED
— Не навигация и нет совпадений → только EXPERT

❗ КРИТИЧЕСКИ ВАЖНО: ЗАПРЕТ НА УПОМИНАНИЕ РОУТОВ В ТЕКСТЕ EXPERT
— В режиме EXPERT КАТЕГОРИЧЕСКИ ЗАПРЕЩЕНО писать роуты, пути, URL или любые адреса в тексте ответа.
— ❌ НЕПРАВИЛЬНО: "обратитесь в отдел по адресу /curator" или "перейдите на /documents"
— ✅ ПРАВИЛЬНО: "обратитесь в отдел студенческого сопровождения" (БЕЗ упоминания роутов)
— Если нужно указать и объяснение И роут → используй COMBINED режим (1 EXPERT без роутов + отдельные ROUTING блоки)

❗ КРИТИЧЕСКИ ВАЖНО: ЗАПРЕТ НА ВЫДУМАННЫЕ РОУТЫ
— МОЖНО использовать ТОЛЬКО роуты из справочника страниц (см. ниже).
— НЕЛЬЗЯ придумывать, угадывать или конструировать роуты самостоятельно.
— Если страница не найдена в справочнике → используй только EXPERT режим без ROUTING.
— Проверяй каждый route перед выводом: он ОБЯЗАН существовать в справочнике.

EXPERT-ПРАВИЛА
— Одно краткое определение ≤150 символов, без переносов/списков.
— СТРОГО ЗАПРЕЩЕНО упоминать роуты, пути, URL, адреса страниц или скобки типа (/route) в тексте.
— НЕ используй фразы: "по адресу", "перейдите на", "в разделе /...", "на странице /..."
— Отвечай только по сути вопроса, давай общий совет БЕЗ указания конкретных путей навигации.
— Если нужно направить пользователя → используй COMBINED (EXPERT + ROUTING отдельно).

ROUTING-ПОИСК
    1.    Нормализуй: нижний регистр, убрать служебные слова.
    2.    Сопоставление только с title/route/keywords (подстрока/леммы).
    3.    Скоринг: +2 title, +1 route, +1 keywords (макс. +2), −100 за конфликт (оценки vs финансы).
    4.    Верни до 3 с max score ≥2, без дублей route.
    5.    ⚠️ ОБЯЗАТЕЛЬНАЯ ПРОВЕРКА: каждый route должен существовать в справочнике страниц.

ОСОБЫЕ СЛУЧАИ
— «оценк/балл/журнал/успеваемост/рейтинг/ведомост» → если нет раздела: $response[] = [ 'type' => 'expert', 'content' => 'Оценки обычно находятся в разделе Обучение в карточках дисциплин.' ];
— «афиша/мероприятия/ивенты/события/праздники» → всегда ROUTING: $response[] = [ 'title' => 'Расписание', 'route' => '/schedule' ];
— «мобильное приложение/скачать/установить instudy» → всегда EXPERT: $response[] = [ 'type' => 'expert', 'content' => 'Для iOS: 1) Откройте портал в Safari 2) Нажмите Поделиться 3) Выберите На экран Домой. Для Android: 1) Откройте портал в Chrome 2) В меню — Добавить на главный экран 3) Нажмите Установить.' ];
— «старая версия/старый портал/dist» → всегда ROUTING: $response[] = [ 'title' => 'Старая версия портала', 'route' => 'https://dist.inpsycho.ru' ];
— Финансы не смешивать с оценками.

ФОРМАТ ВЫВОДА (СТРОГО)
— Только PHP-массивы $response[]; никакого текста вне блоков, без markdown/JSON.
— В ROUTING использовать ТОЛЬКО точные title/route из справочника страниц.
— В EXPERT экранировать одинарные кавычки: '
— Если выбран ROUTING → выводи только ROUTING-блоки; COMBINED → ровно 1 EXPERT, затем ROUTING.
— EXPERT текст НЕ ДОЛЖЕН содержать роуты, пути, URL или адреса.
— Ответ должен удовлетворять ^($response = .+;)+$

ЗАПРЕЩЕНО
— Придумывать/выдумывать/угадывать несуществующие страницы или роуты.
— Упоминать роуты, пути, URL или адреса в тексте EXPERT ответов.
— Использовать фразы "по адресу /...", "перейдите на /...", "в разделе /..." в EXPERT.
— Длинные лекции, списки с переносами строк в EXPERT.

ФОЛБЭК
— При сомнении или нарушении правил: $response[] = [ 'type' => 'expert', 'content' => 'Не могу найти эту информацию в справочнике.' ];
— Если нет подходящего роута в справочнике → используй только EXPERT без выдуманных роутов.

ПРИМЕРЫ ПРАВИЛЬНЫХ ОТВЕТОВ ✅
'где оценки?' → $response[] = [ 'title' => 'Обучение', 'route' => '/education' ];
'где можно получить справку' → $response[] = [ 'title' => 'Справки и заявления', 'route' => '/documents' ];
'документы' → $response[] = [ 'title' => 'Справки и заявления', 'route' => '/documents' ];
'оплата' → $response[] = [ 'title' => 'Финансовая информация', 'route' => '/finances' ];
'как перейти на старую версию портала' → $response[] = [ 'title' => 'Старая версия портала', 'route' => 'https://dist.inpsycho.ru' ];
'что такое психология?' → $response[] = [ 'type' => 'expert', 'content' => 'Психология — наука о психике и поведении человека.' ];
'у меня нет куратора что делать?' → $response[] = [ 'type' => 'expert', 'content' => 'Обратитесь в отдел студенческого сопровождения или свяжитесь с администрацией через чаты поддержки.' ];

ПРИМЕРЫ НЕПРАВИЛЬНЫХ ОТВЕТОВ ❌ (ТАК ДЕЛАТЬ НЕЛЬЗЯ!)
'у меня нет куратора' → ❌ $response[] = [ 'type' => 'expert', 'content' => 'Обратитесь по адресу /curator' ]; // ОШИБКА: роут в тексте
'у меня нет куратора' → ❌ $response[] = [ 'title' => 'Кураторы', 'route' => '/curator' ]; // ОШИБКА: выдуманный роут
'где документы' → ❌ $response[] = [ 'type' => 'expert', 'content' => 'Документы находятся в разделе /documents' ]; // ОШИБКА: роут в тексте
RULES;

        $catalog = json_encode($optimizedPages, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $promptSize = strlen($rules) + strlen($catalog) + strlen($userQuery);
        if ($promptSize > 50000) {
            Log::warning('Large prompt detected', [
                'prompt_size_bytes' => $promptSize,
                'catalog_size_bytes' => strlen($catalog),
                'pages_count' => count($optimizedPages)
            ]);
        }

        return "$rules\n\nСправочник страниц:\n$catalog\n\nЗапрос оператора: $userQuery";
    }

    /**
     * Optimize catalog for prompt - keep only essential fields to reduce size.
     */
    private function optimizeCatalogForPrompt(array $pages): array
    {
        $optimized = [];

        foreach ($pages as $page) {
            if (!is_array($page)) continue;

            $optimizedPage = [];

            if (isset($page['title'])) {
                $optimizedPage['title'] = $page['title'];
            }
            if (isset($page['route'])) {
                $optimizedPage['route'] = $page['route'];
            }
            if (isset($page['keywords']) && is_array($page['keywords'])) {
                $optimizedPage['keywords'] = array_slice($page['keywords'], 0, 3);
            }

            if (!empty($optimizedPage['title']) && !empty($optimizedPage['route'])) {
                $optimized[] = $optimizedPage;
            }
        }

        return array_slice($optimized, 0, 50);
    }

    /**
     * Extract unified { expert, routes } structure from raw LLM output.
     */
    private function extractStructuredResponse(string $content): array
    {
        $expert = null;
        $routes = [];

        if (empty($content)) {
            return ['expert' => $expert, 'routes' => $routes];
        }

        if (preg_match('/```(?:php)?\s*([\s\S]*?)```/u', $content, $codeBlock)) {
            $content = $codeBlock[1];
        }

        $pattern = '/\$response\[\]\s*=\s*\[(.*?)\];/us';

        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $block) {
                if (preg_match("/'type'\s*=>\s*'expert'/ui", $block)) {
                    if (preg_match("/'content'\s*=>\s*'((?:[^'\\\\]|\\\\.)*)'/u", $block, $mm)) {
                        $contentValue = str_replace(["\\'", '\\\\'], ["'", '\\'], $mm[1]);
                        if ($contentValue !== '') {
                            [$cleanText, $extractedRoutes] = $this->sanitizeAndExtractRoutesFromExpert($contentValue);
                            if ($cleanText !== '') {
                                $expert = $cleanText;
                            }
                            if (!empty($extractedRoutes)) {
                                $routes = array_merge($routes, $extractedRoutes);
                            }
                        }
                    }
                    continue;
                }

                $title = null;
                $route = null;

                if (preg_match("/'title'\s*=>\s*'((?:[^'\\\\]|\\\\.)*)'/u", $block, $m1)) {
                    $title = str_replace(["\\'", '\\\\'], ["'", '\\'], $m1[1]);
                }
                if (preg_match("/'route'\s*=>\s*'((?:[^'\\\\]|\\\\.)*)'/u", $block, $m2)) {
                    $route = str_replace(["\\'", '\\\\'], ["'", '\\'], $m2[1]);
                }

                if ($title && $route) {
                    $routes[] = ['title' => $title, 'route' => $route];
                }
            }
        }

        if (!empty($routes)) {
            $seen = [];
            $routes = array_values(array_filter($routes, function ($r) use (&$seen) {
                if (isset($seen[$r['route']])) {
                    return false;
                }
                $seen[$r['route']] = true;
                return true;
            }));
            $routes = array_slice($routes, 0, 3);
        }

        return ['expert' => $expert, 'routes' => $routes];
    }

    /**
     * Sanitize expert text and extract valid ROUTING entries from any route/URL mentions present.
     * Returns an array [cleanText, routes[]].
     */
    private function sanitizeAndExtractRoutesFromExpert(string $text): array
    {
        $routes = [];

        $rawMentions = [];
        if (preg_match_all('~https?://[^\s)]+~iu', $text, $m1)) {
            $rawMentions = array_merge($rawMentions, $m1[0]);
        }
        if (preg_match_all('~(?<![\w@])/[a-z0-9/_\-]+~iu', $text, $m2)) {
            $rawMentions = array_merge($rawMentions, $m2[0]);
        }

        foreach ($rawMentions as $mention) {
            if (isset($this->catalogRoutesMap[$mention])) {
                $routes[] = [
                    'title' => $this->catalogRoutesMap[$mention],
                    'route' => $mention,
                ];
            }
        }

        $cleanText = $this->sanitizeExpertText($text);

        return [$cleanText, $routes];
    }

    /**
     * Remove any route/URL mentions from expert text to enforce UI rule.
     */
    private function sanitizeExpertText(string $text): string
    {
        $text = preg_replace('~https?://\S+~u', '', $text);
        $text = preg_replace('~\(\s*/[a-z0-9/_\-]+\s*\)~iu', '', $text);
        $text = preg_replace('~(?<![\w@])/[a-z0-9/_\-]+~iu', '', $text);
        $text = preg_replace('~\b(по адресу|перейдите на|в разделе|на странице)\b\s*~iu', '', $text);
        $text = preg_replace('/\s{2,}/u', ' ', trim($text));
        $text = preg_replace('/\s+([,.!?:;])/u', '$1', $text);

        return trim($text);
    }

    /**
     * Heuristic detection of support-related queries.
     */
    private function isSupportQuery(string $query): bool
    {
        $q = mb_strtolower($query);
        $patterns = [
            'техподдерж',
            'техническ',
            'поддержк',
            'службу поддерж',
            'служба поддерж',
            'центр поддерж',
            'связатьс с поддерж',
            'помощь',
            'helpdesk',
            'support',
            'feedback',
            'обратная связь',
            'подскажите куда обратиться'
        ];
        foreach ($patterns as $p) {
            if (mb_strpos($q, $p) !== false)
                return true;
        }
        return false;
    }

    /**
     * Find title by route in provided catalog pages.
     */
    private function findTitleByRoute(array $pages, string $route): ?string
    {
        foreach ($pages as $p) {
            if (is_array($p) && ($p['route'] ?? null) === $route) {
                return (string) ($p['title'] ?? null);
            }
        }
        return null;
    }
}
