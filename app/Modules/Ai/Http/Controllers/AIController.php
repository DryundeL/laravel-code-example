<?php

namespace App\Modules\Ai\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Ai\Http\Requests\AIRequest;
use App\Modules\Ai\Services\AIService;
use Illuminate\Http\JsonResponse;


class AIController extends BaseController
{
    protected AIService $service;

    public function __construct(AIService $service)
    {
        $this->service = $service;
    }

    public function getChatHistory(): JsonResponse
    {
        return $this->sendResponse($this->service->getChatHistory());
    }

    /**
     * Returns user info with token from sso
     *
     * @param AIRequest $request
     * @return JsonResponse
     */
    public function getPredict(AIRequest $request): JsonResponse
    {
        return $this->sendResponse($this->service->response($request->validated()));
    }
}
