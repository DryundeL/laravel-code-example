<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\User\Services\CalendarService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CalendarController extends BaseController
{
    protected CalendarService $service;

    public function __construct(CalendarService $service)
    {
        $this->service = $service;
    }

    /**
     * Display calendar link.
     *
     * @return JsonResponse
     */
    public function getCalendarLink(): JsonResponse
    {
        return $this->sendResponse($this->service->getCalendarLink());
    }

    /**
     * Generate calendar ics file.
     *
     * @unauthenticated
     *
     * @param string $token
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws Exception
     */
    public function getCalendar(string $token): Application|ResponseFactory|\Illuminate\Foundation\Application|Response
    {
        return $this->service->getCalendar($token);
    }
}
