<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('ai_chat_histories')
            ->whereNotNull('response')
            ->orderBy('id')
            ->select(['id', 'response'])
            ->chunkById(500, function ($rows) {
                DB::transaction(function () use ($rows) {
                    foreach ($rows as $row) {
                        $original = $row->response;

                        $decoded = $this->safeDecode($original);

                        $normalized = $this->normalizeToStringExpert($decoded);

                        DB::table('ai_chat_histories')
                            ->where('id', $row->id)
                            ->update([
                                'response' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                            ]);
                    }
                });
            });
    }

    public function down(): void
    {
        DB::table('ai_chat_histories')
            ->whereNotNull('response')
            ->orderBy('id')
            ->select(['id', 'response'])
            ->chunkById(500, function ($rows) {
                DB::transaction(function () use ($rows) {
                    foreach ($rows as $row) {
                        $decoded = $this->safeDecode($row->response);

                        $normalized = $this->normalizeToObjectExpert($decoded);

                        DB::table('ai_chat_histories')
                            ->where('id', $row->id)
                            ->update([
                                'response' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                            ]);
                    }
                });
            });
    }

    private function safeDecode($value): array|null
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            try {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }

    private function normalizeToStringExpert($response): array
    {
        $expert = null;
        $routes = [];

        if ($response === null) {
            return ['expert' => null, 'routes' => []];
        }

        if (is_array($response) && (array_key_exists('expert', $response) || array_key_exists('routes', $response))) {
            $expertVal = $response['expert'] ?? null;
            if (is_string($expertVal) && $expertVal !== '') {
                $expert = $expertVal;
            } elseif (is_array($expertVal) && isset($expertVal['content']) && is_string($expertVal['content']) && $expertVal['content'] !== '') {
                $expert = $expertVal['content'];
            } else {
                $expert = null;
            }

            $routesRaw = $response['routes'] ?? [];
            if (is_array($routesRaw)) {
                foreach ($routesRaw as $r) {
                    if (is_array($r) && !empty($r['route'] ?? null) && isset($r['title'])) {
                        $routes[] = [
                            'title' => (string)$r['title'],
                            'route' => (string)$r['route'],
                        ];
                    }
                }
            }

            return ['expert' => $expert, 'routes' => $routes];
        }

        if (is_array($response)) {
            foreach ($response as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (($item['type'] ?? null) === 'expert') {
                    $content = $item['content'] ?? null;
                    if (is_string($content) && $content !== '') {
                        $expert = $content;
                    }
                    continue;
                }

                if (!empty($item['route'] ?? null) && isset($item['title'])) {
                    $routes[] = [
                        'title' => (string)$item['title'],
                        'route' => (string)$item['route'],
                    ];
                }
            }
        }

        return ['expert' => $expert, 'routes' => $routes];
    }

    private function normalizeToObjectExpert($response): array
    {
        $expert = null;
        $routes = [];

        if ($response === null) {
            return ['expert' => null, 'routes' => []];
        }

        if (is_array($response) && (array_key_exists('expert', $response) || array_key_exists('routes', $response))) {
            $expertVal = $response['expert'] ?? null;
            if (is_string($expertVal) && $expertVal !== '') {
                $expert = ['content' => $expertVal];
            } elseif (is_array($expertVal) && isset($expertVal['content']) && is_string($expertVal['content']) && $expertVal['content'] !== '') {
                $expert = ['content' => $expertVal['content']];
            } else {
                $expert = null;
            }

            $routesRaw = $response['routes'] ?? [];
            if (is_array($routesRaw)) {
                foreach ($routesRaw as $r) {
                    if (is_array($r) && !empty($r['route'] ?? null) && isset($r['title'])) {
                        $routes[] = [
                            'title' => (string)$r['title'],
                            'route' => (string)$r['route'],
                        ];
                    }
                }
            }

            return ['expert' => $expert, 'routes' => $routes];
        }

        if (is_array($response)) {
            foreach ($response as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (($item['type'] ?? null) === 'expert') {
                    $content = $item['content'] ?? null;
                    if (is_string($content) && $content !== '') {
                        $expert = ['content' => $content];
                    }
                    continue;
                }

                if (!empty($item['route'] ?? null) && isset($item['title'])) {
                    $routes[] = [
                        'title' => (string)$item['title'],
                        'route' => (string)$item['route'],
                    ];
                }
            }
        }

        return ['expert' => $expert, 'routes' => $routes];
    }
};
