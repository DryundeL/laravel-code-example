<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

return new class extends Migration {
    public function up(): void
    {
        $eventsResponse = Http::post('https://api.inpsycho.ru/api/get-content', [
            'route' => '/lecture_hall'
        ])->json();

        $events = $eventsResponse['sections']['cards']['data'];

        $visibleEvents = array_filter($events, static function ($item) {
            return $item['is_visible_ru'];
        });

        $universityEvents = array_map(static function ($item) {
            $dateStart = Carbon::parse($item['date_start']);
            $dateEnd = Carbon::parse($item['date_end']);

            return [
                'id' => $item['id'],
                'semester' => 0,
                'discipline' => $item['title'],
                'datetime' => $dateStart->format('Y-m-d H:i:s'),
                'durationTime' => $dateStart->diffInSeconds($dateEnd),
                'exam' => 5,
                'online' => $item['format'] === 'Онлайн' ? 1 : 0,
                'url' => 'https://inpsycho.ru/lecture_hall/' . $item['id'],
                'cancel' => 0
            ];
        }, $visibleEvents);

        Cache::set('university_events', $universityEvents);
    }

    public function down(): void
    {
    }
};
