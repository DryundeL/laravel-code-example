<?php

namespace App\Modules\User\Services;

use App\Services\BaseService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Modules\User\Models\Profile;
use Carbon\Carbon;
use DateTimeZone;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Traits\ProcessLink;

class CalendarService extends BaseService
{
    use ProcessLink;

    public function getCalendarLink(): array
    {
        $profile = Auth::user();

        if (!$profile) {
            return ['link' => null];
        }

        DB::transaction(function () use ($profile) {
            $profile->calendar_token = $this->generateUniqueToken($profile);
            $profile->save();
            $profile->refresh();
        });

        return [
            'link' => config('app.url') . '/api/calendar/' . $profile->calendar_token
        ];
    }

    /**
     * Генерирует уникальный токен для календаря
     * Токен содержит информацию о пользователе и временную метку для уникальности
     *
     * @param mixed $profile
     * @return string
     */
    private function generateUniqueToken(mixed $profile): string
    {
        $timestamp = time();
        $randomString = Str::random(16);
        $profileId = $profile->id;

        $tokenData = "{$profileId}-{$timestamp}-{$randomString}";

        return base64_encode($tokenData);
    }

    /**
     * @throws \JsonException
     */
    public function getCalendar(string $token): Application|ResponseFactory|\Illuminate\Foundation\Application|Response
    {
        $request = request();
        $profile = Profile::where('calendar_token', $token)->first();

        if (!$profile) {
            $calendar = Calendar::create('InStudy Events');
            $iCalContent = $calendar->get();

            Log::channel('calendar_log')->info('Запрос на получение данных календаря', [
                'result' => 'fail',
                'reason' => 'profile_not_found',
                'calendar_token' => $token,
            ]);

            return response($iCalContent)
                ->header('Content-Type', 'text/calendar; charset=utf-8')
                ->header('Content-Disposition', 'inline; filename="calendar.ics"');
        }

        $incUniversityEvents = $profile->settings->inc_university_events_calendar;

        Log::info('incUniversityEvents', [$incUniversityEvents]);

        if ($incUniversityEvents) {
            $universityEvents = Cache::get('university_events') ?? [];

            $schedule = $this->getAllScheduleFromESB($profile);
            if (!is_array($schedule)) {
                $schedule = [];
            }
            if (!empty($universityEvents)) {
                $schedule = array_merge($schedule, $universityEvents);
            }
        } else {
            $schedule = $this->getAllScheduleFromESB($profile);
            if (!is_array($schedule)) {
                $schedule = [];
            }
        }

        $schedule = $this->sortScheduleByDateTime($schedule);
        $calendar = Calendar::create('InStudy Events');

        foreach ($schedule as $item) {
            if (array_key_exists('discipline', $item)) {
                $startTime = Carbon::parse($item['datetime'], new DateTimeZone('Europe/Moscow'));

                // Определяем продолжительность события
                $durationTime = $this->calculateEventDuration($item, $startTime);
                $endTime = $startTime->copy()->addSeconds($durationTime);

                $event = Event::create($item['discipline'])
                    ->startsAt($startTime)
                    ->endsAt($endTime)
                    ->description($item['online'] === 1 ? 'Онлайн событие' : 'Оффлайн событие')
                    ->alertMinutesBefore(30, $item['discipline'] . ' начнется через полчаса');

                $eventUrl = null;
                if (isset($item['webinar']) && $item['webinar'] !== '' && is_string($item['webinar'])) {
                    $eventUrl = $item['webinar'];
                } elseif (isset($item['mp4']) && $item['mp4'] !== '' && is_string($item['mp4'])) {
                    $eventUrl = $item['mp4'];
                } elseif (isset($item['url']) && $item['url'] !== '' && is_string($item['url'])) {
                    $date = Carbon::parse($item['datetime']);
                    $eventUrl = $item['exam'] === 5 ? $this->processRouteLink($item, $date, true) : $this->processRouteLink($item, $date);
                }

                if (is_string($eventUrl)) {
                    $event->url($eventUrl);
                }

                $calendar->event($event);
            }
        }

        $iCalContent = $calendar->get();

        Log::channel('calendar_log')->info('Запрос на получение данных календаря', [
            'result' => 'success',
            'calendar_token' => $token,
            'email' => $profile->user->email ?? null,
        ]);

        return response($iCalContent)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="calendar.ics"');
    }

    /**
     * @throws \JsonException
     */
    private function getAllScheduleFromESB(Profile $profile)
    {
        $currentMonth = Carbon::now()->month;

        $startDate = ($currentMonth >= 9) ? Carbon::now()->year . '-09-01' : Carbon::now()->subYear()->year . '-09-01';
        $endDate = ($currentMonth >= 9) ? Carbon::now()->addYear()->year . '-08-01' : Carbon::now()->year . '-08-01';

        $attributes = [
            'date_from' => Carbon::parse($startDate)->format('Y-m-d'),
            'date_to' => Carbon::parse($endDate)->format('Y-m-d'),
        ];

        $identKeys = ['gid'];
        $payloadData = $this->prepareDataToESB(false, true, $identKeys, $attributes, $profile);

        return $this->handleRequestToESB('getScheduleForCalendar', $payloadData, 'activeDays');
    }

    /**
     * Сортирует массив событий по datetime в хронологическом порядке
     *
     * @param array $schedule
     * @return array
     */
    private function sortScheduleByDateTime(array $schedule): array
    {
        usort($schedule, function ($a, $b) {
            $dateA = Carbon::parse($a['datetime'] ?? '1970-01-01 00:00:00');
            $dateB = Carbon::parse($b['datetime'] ?? '1970-01-01 00:00:00');
            return $dateA->timestamp <=> $dateB->timestamp;
        });

        return $schedule;
    }

    /**
     * Рассчитывает продолжительность события в секундах
     *
     * @param array $item
     * @param Carbon $startTime
     * @return int
     */
    private function calculateEventDuration(array $item, Carbon $startTime): int
    {
        // Если есть явная продолжительность
        if (isset($item['durationTime']) && $item['durationTime'] > 0) {
            return (int) $item['durationTime'];
        }

        // Если есть datetimeEnd, используем его
        if (isset($item['datetimeEnd'])) {
            try {
                $endTime = Carbon::parse($item['datetimeEnd'], new DateTimeZone('Europe/Moscow'));
                return $startTime->diffInSeconds($endTime);
            } catch (\Exception $e) {
                // Если не удалось распарсить, используем значение по умолчанию
            }
        }

        // Определяем продолжительность по типу события
        $exam = (int) ($item['exam'] ?? 0);

        return match ($exam) {
            1 => 5400,  // Лекция: 1.5 часа
            2 => 3600,  // Семинар: 1 час
            3 => 7200,  // Лабораторная: 2 часа
            4 => 10800, // Экзамен: 3 часа
            5 => 3600,  // Университетское событие: 1 час
            default => 3600, // По умолчанию 1 час
        };
    }
}
