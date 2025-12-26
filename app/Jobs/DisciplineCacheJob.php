<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Traits\LazyServiceLoader;
use App\Modules\Education\Interfaces\DataTransfer\EducationServiceInterface;

class DisciplineCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, LazyServiceLoader;

    private mixed $profile;

    public function __construct(mixed $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Выполняет кеширование дисциплин в фоновом режиме
     */
    public function handle(): void
    {
        try {
            // Валидация профиля перед началом обработки
            if (!$this->profile) {
                throw new \RuntimeException('Профиль не передан в job');
            }

            Log::info('Начинаем кеширование дисциплин', [
                'profile_id' => $this->profile->id,
            ]);

            $educationService = $this->getService(EducationServiceInterface::class);
            $disciplines = $educationService->getDisciplines($this->profile);

            if (array_key_exists('errors', $disciplines)) {
                Log::error('Ошибка при получении дисциплин', [
                    'profile_id' => $this->profile->id,
                    'errors' => $disciplines['errors']
                ]);
                return;
            }

            $cacheKey = "profile_{$this->profile->id}_discipline_ids";
            $cacheExisted = Cache::has($cacheKey);

            if ($cacheExisted) {
                $existingDisciplineIds = Cache::get($cacheKey, []);
                $allDisciplineIds = array_unique(array_merge($existingDisciplineIds, $disciplines));
                Cache::set($cacheKey, $allDisciplineIds);
            } else {
                Cache::set( $cacheKey, $disciplines);
                $allDisciplineIds = $disciplines;
            }

            Log::info('Дисциплины успешно закешированы', [
                'profile_id' => $this->profile->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при кешировании дисциплин', [
                'profile_id' => $this->profile->id,
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
        Log::error('Job кеширования дисциплин завершился с ошибкой', [
            'error' => $exception->getMessage()
        ]);
    }
}
