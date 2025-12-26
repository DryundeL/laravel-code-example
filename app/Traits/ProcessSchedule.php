<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait ProcessSchedule
{
    use ProcessLink;

    public function filterCachedUniversityEvents(array $attributes, bool $isProcess = true): array
    {
        if (empty($attributes)) {
            return [];
        }

        $cacheUniversityEvents = Cache::get('university_events') ?? [];
        $events = [];

        if (!empty($cacheUniversityEvents)) {
            $events = array_filter($cacheUniversityEvents, static function ($item) use ($attributes) {
                $isDisciplineMatch = true;
                $isDateMatch = true;

                if (!empty($attributes['discipline'])) {
                    $isDisciplineMatch = isset($item['discipline']) && is_string($item['discipline']) &&
                        mb_stripos($item['discipline'], trim($attributes['discipline']), 0, 'UTF-8') !== false;
                }

                if (isset($attributes['min_date'])) {
                    $dateTime = Carbon::parse($item['datetime']);
                    $isDateMatch = $dateTime->gte($attributes['min_date']);
                }

                if (isset($attributes['date'])) {
                    $dateTime = Carbon::parse($item['datetime']);
                    $isDateMatch = $isDateMatch && $dateTime->isSameDay($attributes['date']);
                } elseif (isset($attributes['date_from'], $attributes['date_to'])) {
                    $dateTime = Carbon::parse($item['datetime']);
                    $startDate = Carbon::parse($attributes['date_from'])->startOfDay();
                    $endDate = Carbon::parse($attributes['date_to'])->endOfDay();
                    $isDateMatch = $isDateMatch && $dateTime->between($startDate, $endDate);
                } elseif (isset($attributes['day'])) {
                    $dateTime = Carbon::parse($item['datetime']);
                    $startRange = $attributes['day']->copy()->subDays(7)->startOfDay();
                    $endRange = $attributes['day']->copy()->addDays(7)->endOfDay();
                    $isDateMatch = $isDateMatch && $dateTime->between($startRange, $endRange);
                }

                return $isDisciplineMatch && $isDateMatch;
            });

            if ($isProcess) {
                $events = $this->processSchedule($events, isUniversityEvents: true);
            }
        }

        return $events;
    }

    public function processSchedule(array $data, string $type = 'schedule', bool $isUniversityEvents = false): array
    {
        if ($type === 'schedule') {
            usort($data, static function ($a, $b) {
                $timeA = !empty($a['datetime']) ? Carbon::createFromFormat('Y-m-d H:i:s', $a['datetime'])->timestamp : Carbon::now()->timestamp;
                $timeB = !empty($b['datetime']) ? Carbon::createFromFormat('Y-m-d H:i:s', $b['datetime'])->timestamp : Carbon::now()->timestamp;

                return $timeA <=> $timeB;
            });
        }

        switch ($type) {
            case 'schedule':
                array_walk($data, function (&$event) use ($isUniversityEvents) {
                    if (empty($event['datetime'])) {
                        $date = Carbon::now();
                    } else {
                        $date = Carbon::createFromFormat('Y-m-d H:i:s', $event['datetime']);
                    }

                    $startTime = $date->format('H:i');

                    if (isset($event['durationTime'])) {
                        if ($event['durationTime'] === 0) {
                            $endTime = $date->copy()->addHour()->addMinutes(30)->format('H:i');
                            $date = $date->copy()->addHour()->addMinutes(30);
                        } else {
                            $endTime = $date->copy()->addSeconds($event['durationTime'])->format('H:i');
                            $date = $date->copy()->addSeconds($event['durationTime']);
                        }
                    } else {
                        $endTime = $date->copy()->addHour()->addMinutes(30)->format('H:i');
                        $date = $date->copy()->addHour()->addMinutes(30);
                    }

                    $event['period'] = "{$startTime} - {$endTime}";

                    $event['route'] = $this->processRouteLink($event, $date, $isUniversityEvents);

                    if (isset($event['teacher'])) {
                        $teacher = trim($event['teacher']);
                        $teacherElems = mb_split('\s+', $teacher);
                        $event['teacher'] = $teacherElems[0] . ' ' . mb_substr($teacherElems[1] ?? '' , 0, 1, 'UTF-8');

                        if (isset($teacherElems[2])) {
                            $event['teacher'] .= '.' . mb_substr($teacherElems[2], 0, 1, 'UTF-8') . '.';
                        }
                    }

                    unset(
                        $event['url'],
                        $event['durationTime'],
                        $event['datetime'],
                        $event['webinar'],
                        $event['mp4'],
                        $event['semester']
                    );
                });
                break;
            case 'widget':
                $processList = function (array $list) use ($isUniversityEvents): array {
                    array_walk($list, function (&$item) use ($isUniversityEvents) {
                        if (isset($item['datetimeStart'], $item['datetimeEnd'])) {
                            $dateTimeStart = Carbon::createFromFormat('Y-m-d H:i:s', $item['datetimeStart']);
                            $dateTimeEnd = Carbon::createFromFormat('Y-m-d H:i:s', $item['datetimeEnd']);

                            if (!$dateTimeEnd) {
                                $dateTimeStart = Carbon::now();
                            }

                            if ($dateTimeStart->equalTo($dateTimeEnd)) {
                                $dateTimeEnd = $dateTimeStart->copy()->addHour()->addMinutes(30);
                            }

                            $item['route'] = $this->processRouteLink($item, $dateTimeEnd, $isUniversityEvents);
                            unset($item['url']);

                            if ($dateTimeStart && $dateTimeEnd) {
                                $item['period'] = $dateTimeStart->format('H:i') . ' - ' . $dateTimeEnd->format('H:i');

                                unset(
                                    $item['datetimeStart'],
                                    $item['datetimeEnd'],
                                    $item['webinar'],
                                    $item['mp4']
                                );
                            }
                        }
                    });
                    return $list;
                };

                $currentItems = isset($data['current']) && is_array($data['current']) ? $data['current'] : [];
                $nearestBlock = isset($data['nearest']) && is_array($data['nearest']) ? $data['nearest'] : null;

                $current = $processList($currentItems);

                $nearest = null;
                if ($nearestBlock !== null) {
                    $nearest = [
                        'date' => $nearestBlock['date'] ? Carbon::parse($nearestBlock['date'])->format('d.m.Y') : null,
                        'schedule' => isset($nearestBlock['schedule']) && is_array($nearestBlock['schedule'])
                            ? $processList($nearestBlock['schedule'])
                            : [],
                    ];
                }

                $data = [
                    'current' => $current,
                    'nearest' => $nearest,
                ];
                break;
        }

        return $data;
    }
}
