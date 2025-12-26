<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\User\Models\Profile;
use App\Modules\User\Services\UserService;
use App\Resources\MiniProfileCollection;
use App\Resources\ProfileResource;
use App\Resources\UserResource;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws \Exception
     */
    public function index(): MiniProfileCollection
    {
        $data = $this->service->getAllProfiles();
        $user = UserResource::make($data['user']);

        return (new MiniProfileCollection($data['profiles']))->additional(['user' => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(Request $request): JsonResponse
    {
        $data = $this->service->getAuthUser($request);

        if (array_key_exists('errors', $data)) {
            return $this->sendErrorResponse($data, 400);
        }

        return $this->sendResponse([
            'profile' => new ProfileResource($data['profile']),
            'user' => new UserResource($data['user']),
            'accesses' => $data['accesses'],
            'notification_settings' => $data['notification_settings'],
            'appearence' => $data['appearence'],
        ]);
    }

    /**
     * @throws \Exception
     */
    public function switch(Profile $profile): JsonResponse
    {
        $data = $this->service->switchProfile($profile);

        if (array_key_exists('errors', $data)) {
            return $this->sendErrorResponse($data, 400);
        }

        return $this->sendResponse([
            'profile' => new ProfileResource($data['profile']),
            'user' => new UserResource($data['user']),
            'token' => $data['token'],
            'accesses' => $data['accesses'],
            'notification_settings' => $data['notification_settings'],
            'appearence' => $data['appearence'],
        ]);
    }
}
