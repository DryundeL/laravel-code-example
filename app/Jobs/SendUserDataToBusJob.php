<?php

namespace App\Jobs;

use App\Services\BaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Job для отправки данных пользователя на шину данных
 *
 * Пример использования:
 *
 * use App\Jobs\SendUserDataToBusJob;
 *
 * // Вариант 1: Создание из Request (рекомендуется)
 * $job = SendUserDataToBusJob::fromRequest($request, 'setVisit');
 * $job->dispatch();
 *
 * // Или короче:
 * SendUserDataToBusJob::fromRequest($request)->dispatch();
 *
 * // Вариант 2: Прямое создание с параметрами
 * SendUserDataToBusJob::dispatch(
 *     userId: 6,
 *     ip: $request->ip(),
 *     agent: $request->userAgent(),
 *     bus: 'setVisit'
 * );
 */
class SendUserDataToBusJob extends BaseService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param int $userId ID пользователя
     * @param string $ip IP адрес
     * @param string $agent User Agent
     * @param string $bus Название шины данных (по умолчанию 'setVisit')
     */
    public function __construct(
        public int $userId,
        public string $ip,
        public string $agent,
        public string $bus = 'setVisit'
    )
    {
    }

    /**
     * Создает Job из Request объекта
     *
     * @param Request $request
     * @param string $bus Название шины данных
     * @return self
     */
    public static function fromRequest(Request $request, string $bus = 'setVisit'): self
    {
        $profile = Auth::user();
        // Используем external_id профиля, так как это ID из внешней системы (ESB)
        $userId = $profile?->external_id ?? $profile?->id ?? 0;

        return new self(
            userId: $userId,
            ip: $request->ip(),
            agent: $request->userAgent() ?? '',
            bus: $bus
        );
    }

    /**
     * Выполняет отправку данных пользователя на шину данных
     */
    public function handle(): void
    {
        try {

            // Подготавливаем данные для отправки на шину
            // Передаем user_id напрямую в attributes, так как needProfile = false
            $payloadData = $this->prepareDataToESB(
                needProfile: false,
                attributes: [
                    'user_id' => $this->userId,
                    'ip' => $this->ip,
                    'agent' => $this->agent,
                ]
            );

            // Отправляем запрос на шину данных
            $result = $this->handleRequestToESB($this->bus, $payloadData);

            if (is_array($result) && array_key_exists('errors', $result)) {
                Log::error('Ошибка при отправке данных пользователя на шину', [
                    'user_id' => $this->userId,
                    'ip' => $this->ip,
                    'bus' => $this->bus,
                    'errors' => $result['errors']
                ]);
                return;
            }

            Log::info('Данные пользователя успешно отправлены на шину', [
                'user_id' => $this->userId,
                'ip' => $this->ip,
                'bus' => $this->bus
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при отправке данных пользователя на шину', [
                'user_id' => $this->userId,
                'ip' => $this->ip,
                'bus' => $this->bus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->fail($e);
        }
    }

    /**
     * Обработка неудачного выполнения job'а
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job отправки данных пользователя на шину завершился с ошибкой', [
            'user_id' => $this->userId,
            'ip' => $this->ip,
            'bus' => $this->bus,
            'error' => $exception->getMessage()
        ]);
    }
}

