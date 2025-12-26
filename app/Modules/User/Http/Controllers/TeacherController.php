<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\User\Http\Requests\StoreUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Services\AccessService;
use App\Modules\User\Services\TeacherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class TeacherController extends BaseController
{
    protected TeacherService $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    public function getTeacherById(int $id): JsonResponse
    {
        $teacher = $this->service->getTeacherById($id);

        if (is_array($teacher) && array_key_exists('errors', $teacher)) {
            return $this->sendErrorResponse($teacher);
        }

        return $this->sendResponse($teacher);
    }

}
