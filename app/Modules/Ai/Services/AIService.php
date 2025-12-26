<?php

namespace App\Modules\Ai\Services;

use App\Modules\Ai\Models\AiChatHistory;
use App\Modules\Ai\Traits\AIServiceHelpers;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class AIService extends BaseService
{
    use AIServiceHelpers;

    private array $catalogRoutesMap = [];

    /**
     * AIService constructor.
     */
    public function __construct(AiChatHistory $chatHistory)
    {
        $this->model = $chatHistory;
    }

    /**
     * Get chat history for the authenticated user in a unified shape.
     */
    public function getChatHistory(): array
    {
        $user = Auth::user();
        if (!$user) return [];

        $history = $user->aiChatHistories()->orderBy('created_at', 'asc')->get();

        return $history->map(function (AiChatHistory $m) {
            return [
                'id' => $m->id,
                'query' => $m->query,
                'response' => $m->response,
                'created_at' => $m->created_at?->copy()->timezone(config('app.timezone'))->format('d.m.Y H:i:s'),
                'updated_at' => $m->updated_at?->copy()->timezone(config('app.timezone'))->format('d.m.Y H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Main entrypoint: build prompt, call LLM, persist and return result in unified shape.
     */
    public function response(array $attributes): array
    {
        $query = trim($attributes['query'] ?? '');
        $isMobile = (bool)($attributes['is_mobile'] ?? false);

        if (empty($query)) {
            Log::warning('Empty query received', ['attributes' => $attributes]);
            return $this->saveAndFormatResponse('', [
                'expert' => 'Пожалуйста, введите ваш вопрос.',
                'routes' => []
            ]);
        }

        try {
            $pages = $this->getCatalogWithCache($isMobile);

            if ($this->isSupportQuery($query)) {
                if ($isMobile) {
                    $title = $this->findTitleByRoute($pages, '/feedback') ?? 'Центр поддержки';
                    return $this->saveAndFormatResponse($query, [
                        'expert' => null,
                        'routes' => [ ['title' => $title, 'route' => '/feedback'] ]
                    ]);
                }

                $helpMail = (string)(env('HELP_MAIL') ?: 'support@example.com');
                return $this->saveAndFormatResponse($query, [
                    'expert' => "Напишите в поддержку на {$helpMail}.",
                    'routes' => []
                ]);
            }

            $prompt = $this->buildRoutingPrompt($pages, $query);

            $optimizedForMap = $this->optimizeCatalogForPrompt($pages);
            $this->catalogRoutesMap = [];
            foreach ($optimizedForMap as $p) {
                if (!empty($p['route']) && !empty($p['title'])) {
                    $this->catalogRoutesMap[(string)$p['route']] = (string)$p['title'];
                }
            }

            $gptResponse = $this->callGptApi($prompt);
            $extractedResponse = $this->extractStructuredResponse($gptResponse);
            return $this->saveAndFormatResponse($query, $extractedResponse);

        } catch (Exception $e) {
            Log::info('AI Service error', [
                'query' => $query,
                'error' => $e->getMessage(),
                'error_type' => get_class($e)
            ]);

            return $this->saveAndFormatResponse($query, ['expert' => null, 'routes' => []]);
        }
    }

    /**
     * Get catalog with cache (per-user and per-device).
     */
    private function getCatalogWithCache(bool $isMobile): array
    {
        $user = Auth::user();
        $suffix = $isMobile ? 'm' : 'd';

        return Cache::remember('ai_catalog_' . $user->id . '_' . $suffix, 3600, function () use ($isMobile) {
            return $this->getCatalog($isMobile);
        });
    }

    /**
     * Build catalog from JSON and ESB.
     */
    private function getCatalog(bool $isMobile): array
    {
        try {
            $fileName = $isMobile ? 'mobileNavigation' : 'desktopNavigation';
            $jsonData = Storage::disk('yandexCloud')->get("navigation/{$fileName}.json");

            if (!$jsonData) {
                throw new RuntimeException("Failed to fetch {$fileName}.json");
            }

            $defaultData = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
            $esbData = $this->getData();

            return array_merge($defaultData ?: [], $esbData ?: []);
        } catch (Exception $e) {
            Log::error('Failed to build catalog', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get data from ESB.
     */
    private function getData(): array
    {
        $identKeys = ['gid', 'semester'];
        $payloadData = $this->prepareDataToESB(true, true, $identKeys);
        return $this->handleRequestToESB('getEduStruct', $payloadData);
    }

    /**
     * Call the LLM API with error handling and safe logging.
     *
     * @throws RequestException|ConnectionException
     */
    private function callGptApi(string $prompt): string
    {
        $body = [
            'gpt_model' => 'local',
            'request' => $prompt,
            'temperature' => 0.0,
        ];

        $url = config('inStudy.gpt_integration_domain') . '/api/completions';
        $requestSize = strlen(json_encode($body, JSON_UNESCAPED_UNICODE));

        Log::info('GPT API request', [
            'url' => $url,
            'request_size_bytes' => $requestSize,
            'request_size_kb' => round($requestSize / 1024, 2),
            'prompt_length' => strlen($prompt),
        ]);

        try {
            $startTime = microtime(true);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'x-api-key: ' . config('inStudy.gpt_api_key'),
                    'Content-Type: application/json',
                    'Content-Length: ' . $requestSize,
                ],
            ]);

            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($curlError) {
                Log::info('GPT API request failed', [
                    'error' => $curlError,
                    'response_time_ms' => $responseTime,
                    'url' => $url
                ]);
                throw new ConnectionException($curlError);
            }

            // Безопасное логирование ответа
            $this->logResponseSafelyCurl($responseBody, $httpCode, $responseTime, $url);

            if ($httpCode < 200 || $httpCode >= 300) {
                Log::info('GPT API request failed', [
                    'status' => $httpCode,
                    'response_time_ms' => $responseTime,
                    'url' => $url
                ]);

                throw new RequestException(new \Illuminate\Http\Client\Response(
                    new \GuzzleHttp\Psr7\Response($httpCode, [], $responseBody)
                ));
            }

            $data = json_decode($responseBody, true);

            if (!isset($data['data']['content'])) {
                Log::info('Invalid GPT API response format', [
                    'response_data' => $data,
                    'url' => $url
                ]);

                throw new RuntimeException('Invalid GPT API response format');
            }

            return $data['data']['content'];

        } catch (ConnectionException $e) {
            Log::info('GPT API connection error', [
                'error' => $e->getMessage(),
                'url' => $url,
                'request_size' => $requestSize
            ]);

            throw $e;

        } catch (RequestException $e) {
            Log::info('GPT API request error', [
                'error' => $e->getMessage(),
                'url' => $url,
                'request_size' => $requestSize
            ]);

            throw $e;

        } catch (Exception $e) {
            Log::info('GPT API unexpected error', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'url' => $url,
                'request_size' => $requestSize
            ]);

            throw $e;
        }
    }

    /**
     * Log cURL response safely with size limits.
     */
    private function logResponseSafelyCurl(string $responseBody, int $httpCode, float $responseTime, string $url): void
    {
        try {
            $responseSize = strlen($responseBody);

            // Ограничиваем размер логируемого ответа
            $maxLogSize = 1000; // 1KB максимум для логов
            $logBody = $responseSize > $maxLogSize
                ? substr($responseBody, 0, $maxLogSize) . '... [truncated]'
                : $responseBody;

            Log::info('GPT API response', [
                'status' => $httpCode,
                'response_time_ms' => $responseTime,
                'response_size_bytes' => $responseSize,
                'url' => $url,
                'body' => $logBody
            ]);

        } catch (Exception $e) {
            // Если логирование не удалось, логируем только основную информацию
            Log::warning('Failed to log response details', [
                'error' => $e->getMessage(),
                'status' => $httpCode,
                'response_time_ms' => $responseTime,
                'url' => $url
            ]);
        }
    }

    /**
     * Persist response and return formatted record.
     */
    private function saveAndFormatResponse(string $query, array $response): array
    {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('User not authenticated for AI request', ['query' => $query]);
                return [
                    'id' => null,
                    'query' => $query,
                    'response' => $this->ensureResponseShape($response),
                    'created_at' => now()->format('d.m.Y H:i:s'),
                    'updated_at' => now()->format('d.m.Y H:i:s'),
                ];
            }

            $normalized = $this->ensureResponseShape($response);

            return DB::transaction(function () use ($query, $normalized, $user) {
                $record = new AiChatHistory([
                    'query' => $query,
                    'response' => $normalized,
                ]);

                $record->profile()->associate($user);
                $record->save();

                return $this->formatChatRecord($record);
            });

        } catch (Exception $e) {
            Log::info('Failed to save AI response', [
                'query' => $query,
                'error' => $e->getMessage(),
                'error_type' => get_class($e)
            ]);

            // Возвращаем ответ без сохранения в БД
            return [
                'id' => null,
                'query' => $query,
                'response' => $this->ensureResponseShape($response),
                'created_at' => now()->format('d.m.Y H:i:s'),
                'updated_at' => now()->format('d.m.Y H:i:s'),
            ];
        }
    }

    /**
     * Ensure response has the unified shape; fall back to empty structure if invalid.
     */
    private function ensureResponseShape($response): array
    {
        $expert = null;
        $routes = [];

        if (is_array($response)) {
            if (array_key_exists('expert', $response) || array_key_exists('routes', $response)) {
                $expertVal = $response['expert'] ?? null;
                if (is_string($expertVal) && $expertVal !== '') {
                    [$cleanText, $extractedRoutes] = $this->sanitizeAndExtractRoutesFromExpert($expertVal);
                    if ($cleanText !== '') {
                        $expert = $cleanText;
                    }
                    if (!empty($extractedRoutes)) {
                        $routes = array_merge($routes, $extractedRoutes);
                    }
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
        }

        return ['expert' => null, 'routes' => []];
    }

    /**
     * Format a single chat record for API output.
     */
    private function formatChatRecord(AiChatHistory $record): array
    {
        return [
            'id' => $record->id,
            'query' => $record->query,
            'response' => $this->ensureResponseShape($record->response),
            'created_at' => $record->created_at?->copy()->timezone(config('app.timezone'))->format('d.m.Y H:i:s'),
            'updated_at' => $record->updated_at?->copy()->timezone(config('app.timezone'))->format('d.m.Y H:i:s'),
        ];
    }

}
