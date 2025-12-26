<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\User\Http\Requests\UpdateUserSettingsRequest;
use App\Modules\User\Services\SettingsService;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;

class SettingsController extends BaseController
{
    protected SettingsService $service;

    public function __construct(SettingsService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws \Exception
     */
    public function store(UpdateUserSettingsRequest $request): JsonResponse
    {
        $this->service->updateSettings($request->validated());

        return $this->sendResponse();
    }
}
