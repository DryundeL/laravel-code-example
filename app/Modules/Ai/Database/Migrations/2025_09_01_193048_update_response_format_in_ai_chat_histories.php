<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        DB::table('ai_chat_histories')
            ->orderBy('id')
            ->whereNotNull('response')
            ->select(['id', 'response'])
            ->chunkById(500, function ($rows) {
                DB::transaction(function () use ($rows) {
                    foreach ($rows as $row) {
                        $original = $row->response;

                        $decoded = is_string($original) ? json_decode($original, true, 512, JSON_THROW_ON_ERROR) : $original;

                        $normalized = $this->normalizeShape($decoded);

                        DB::table('ai_chat_histories')
                            ->where('id', $row->id)
                            ->update([
                                'response' => json_encode($normalized, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                            ]);
                    }
                });
            });
    }

    public function down(): void
    {
    }

    private function normalizeShape($response): array
    {
        $expert = null;
        $routes = [];

        if ($response === null) {
            return ['expert' => null, 'routes' => []];
        }

        if (is_array($response) && (array_key_exists('expert', $response) || array_key_exists('routes', $response))) {
            $expertVal = $response['expert'] ?? null;
            if (is_array($expertVal) && isset($expertVal['content']) && $expertVal['content'] !== '') {
                $expert = ['content' => (string)$expertVal['content']];
            }

            $routesRaw = $response['routes'] ?? [];
            if (is_array($routesRaw)) {
                foreach ($routesRaw as $r) {
                    if (is_array($r) && !empty($r['route'] ?? null) && isset($r['title'])) {
                        $routes[] = $r;
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
                    $routes[] = $item;
                }
            }
        }

        return ['expert' => $expert, 'routes' => $routes];
    }
};
