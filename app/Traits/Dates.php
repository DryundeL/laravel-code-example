<?php

namespace App\Traits;

use Carbon\Carbon;

trait Dates
{
    public function formatSessionDates(array $dates): array|null
    {
        $exam = $dates['exam'];

        $formatDates = static function (array $dates) {
            if (isset($dates['start'], $dates['finish']) && ($dates['start'] !== '0000-00-00 00:00:00')) {
                $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $dates['start']);

                if ($startDate?->year === -1) {
                    $formattedStart = null;
                    $daysUntilStart = null;
                } else {
                    $formattedStart = $startDate?->format('d.m.Y');
                    $today = Carbon::now()->startOfDay();
                    $startDateForDiff = $startDate?->startOfDay();
                    $diffStart = $today->diffInDays($startDateForDiff);
                    $daysUntilStart = ($diffStart <= 0) ? 0 : $diffStart;
                }

                $finishDate = Carbon::createFromFormat('Y-m-d H:i:s', $dates['finish']);

                if ($finishDate?->year === -1) {
                    $formattedFinish = null;
                    $daysUntilEnd = null;
                } else {
                    $formattedFinish = $finishDate?->format('d.m.Y');
                    $finishDateForDiff = $finishDate?->startOfDay();
                    $diffEnd = $today->diffInDays($finishDateForDiff);
                    $daysUntilEnd = ($diffEnd <= 0) ? 0 : $diffEnd;
                }

                $dates['start'] = $formattedStart;
                $dates['days_until_start'] = $daysUntilStart;
                $dates['finish'] = $formattedFinish;
                $dates['days_until_end'] = $daysUntilEnd;
            } else {
                $dates = null;
            }

            return $dates;
        };

        $exam = $formatDates($exam);

        return [
            'exam' => $exam,
        ];
    }

    private function groupByDateWithUniqueValues(array $items, string $dateKey = 'datetime', string $info = 'short'): array
    {
        $groupedByDate = [];

        foreach ($items as $item) {
            if (!empty($item[$dateKey])) {
                $date = Carbon::parse($item[$dateKey])->format('d.m.Y');

                if ($info === 'full') {
                    $groupedByDate[$date][] = $item;
                } else {
                    if (!isset($groupedByDate[$date])) {
                        $groupedByDate[$date] = [
                            'exam' => [],
                        ];
                    }

                    if (isset($item['exam']) && !in_array($item['exam'], $groupedByDate[$date]['exam'], true)) {
                        $groupedByDate[$date]['exam'][] = $item['exam'];
                    }
                }
            }
        }

        ksort($groupedByDate);

        return $groupedByDate;
    }

    public function processActiveDays(array $activeDays, Carbon $currentDate): array
    {
        $dates = $this->getWeekDates($currentDate);
        $activeDaysByDate = $this->groupByDateWithUniqueValues($activeDays);

        $prevDays = array_map(static function ($date) use ($activeDaysByDate) {
            $formattedDate = Carbon::parse($date)->format('d.m.Y');
            $day = $activeDaysByDate[$formattedDate] ?? null;

            return [
                'date' => $formattedDate,
                'exam' => $day ? $day['exam'] : [],
            ];
        }, $dates['prev_dates']);

        $nextDays = array_map(static function ($date) use ($activeDaysByDate) {
            $formattedDate = Carbon::parse($date)->format('d.m.Y');
            $day = $activeDaysByDate[$formattedDate] ?? null;

            return [
                'date' => $formattedDate,
                'exam' => $day ? $day['exam'] : [],
            ];
        }, $dates['next_dates']);

        return [
            'prev_days' => $prevDays,
            'next_days' => $nextDays,
        ];
    }

    public function getWeekDates(Carbon $currentDate): array
    {
        $datesBefore = [];
        $datesAfter = [];

        for ($i = 7; $i >= 1; $i--) {
            $dateBefore = $currentDate?->copy()->subDays($i)->format('d.m.Y');
            $datesBefore[] = $dateBefore;
        }

        for ($i = 1; $i <= 7; $i++) {
            $dateAfter = $currentDate?->copy()->addDays($i)->format('d.m.Y');
            $datesAfter[] = $dateAfter;
        }


        return [
            'prev_dates' => $datesBefore,
            'next_dates' => $datesAfter,
        ];
    }

    public function processTestDates(array &$data): void
    {
        if ($data['availableFrom']) {
            $startDate = Carbon::parse($data['availableFrom'])?->format('d.m.Y');
        }

        if ($data['availableTo']) {
            $endDate = $data['availableTo'] === '-' ? null : Carbon::parse($data['availableTo'])?->format('d.m.Y');
        }

        if (isset($data['ban']) && $data['ban']) {
            $banDate = Carbon::parse($data['ban']);

            $data['ban'] = $banDate?->isPast() ? null : $banDate?->format('d.m.Y H:i:s');
        }

        $data['availableFrom'] = $startDate ?? null;
        $data['availableTo'] = $endDate ?? null;
    }

    /**
     * Парсит строку времени в формате "HH:MM" и возвращает массив [час, минута]
     *
     * @param string $timeString Строка времени в формате "HH:MM"
     * @return array{0: int, 1: int} Массив [час, минута]
     * @throws \InvalidArgumentException
     */
    private function parseTime(string $timeString): array
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $timeString, $matches)) {
            throw new \InvalidArgumentException("Неверный формат времени: {$timeString}. Ожидается формат HH:MM");
        }

        $hour = (int)$matches[1];
        $minute = (int)$matches[2];

        if ($hour < 0 || $hour > 23) {
            throw new \InvalidArgumentException("Час должен быть в диапазоне 0-23: {$hour}");
        }

        if ($minute < 0 || $minute > 59) {
            throw new \InvalidArgumentException("Минута должна быть в диапазоне 0-59: {$minute}");
        }

        return [$hour, $minute];
    }

    /**
     * Проверяет, находится ли текущее время в ограниченном диапазоне
     *
     * Время ограничения берется из конфигурации:
     * - inStudy.time_restriction_start (по умолчанию: "18:40")
     * - inStudy.time_restriction_end (по умолчанию: "19:20")
     *
     * @return bool
     */
    public function isRestrictedTime(): bool
    {
        $startTimeConfig = config('inStudy.time_restriction_start', '18:40');
        $endTimeConfig = config('inStudy.time_restriction_end', '19:20');

        [$startHour, $startMinute] = $this->parseTime($startTimeConfig);
        [$endHour, $endMinute] = $this->parseTime($endTimeConfig);

        $currentTime = now();
        $currentHour = $currentTime->hour;
        $currentMinute = $currentTime->minute;

        // Конвертируем время в минуты для удобного сравнения
        $currentTimeInMinutes = $currentHour * 60 + $currentMinute;
        $startTimeInMinutes = $startHour * 60 + $startMinute;
        $endTimeInMinutes = $endHour * 60 + $endMinute;

        return $currentTimeInMinutes >= $startTimeInMinutes && $currentTimeInMinutes <= $endTimeInMinutes;
    }
}
