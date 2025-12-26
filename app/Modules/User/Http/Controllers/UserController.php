<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\User\Http\Requests\StoreUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Services\AccessService;
use App\Modules\User\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends BaseController
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return $this->sendResponse();
    }

    /**
     * @throws Exception|Throwable
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $oldEmail = $data['old_email'];
        $response = $this->service->updateUserByEmail($data, $oldEmail);

        if (is_array($response)) {
            return $this->sendErrorResponse($response);
        }

        return $this->sendResponse();
    }

    /**
     * @throws Exception|Throwable
     */
    public function getAccesses(AccessService $service): JsonResponse
    {
        return $this->sendResponse($service->getAll());
    }
}
