<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Auth\Http\Requests\AuthByProfileRequest;
use App\Modules\Auth\Http\Requests\GetUserRequest;
use App\Modules\Auth\Http\Requests\LogoutUserRequest;
use App\Modules\Auth\Services\AuthService;
use App\Modules\User\Models\Profile;
use App\Resources\MiniProfileCollection;
use App\Resources\ProfileResource;
use App\Resources\UserResource;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JsonException;
use Throwable;


class AuthController extends BaseController
{
    protected AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Returns user info with token from sso
     *
     * @param GetUserRequest $request
     * @return JsonResponse|ProfileResource|AnonymousResourceCollection
     * @throws ConnectionException
     * @throws JsonException
     * @throws Throwable
     */
    public function getUser(GetUserRequest $request): JsonResponse|ProfileResource|AnonymousResourceCollection
    {
        $userInfo = $this->service->getUser($request->validated(), $request);

        if (isset($userInfo['errors'])) {
            return $this->sendErrorResponse($userInfo, 400);
        }

        if ($userInfo === false) {
            return $this->sendErrorResponse(['error' => 'Failed to fetch user info'], 401);
        }

        if (isset($userInfo['profiles'])) {
            return $this->sendResponse(
                [
                    'user' => new UserResource($userInfo['user']),
                    'profiles' => new MiniProfileCollection($userInfo['profiles']),
                    'accesses' => $userInfo['accesses']
                ]
            );
        }

        if (!isset($userInfo['profile'])) {
            if ($userInfo['errors']['profiles']) {
                return $this->sendErrorResponse($userInfo);
            }

            return $this->sendErrorResponse($userInfo, 400);
        }

        return $this->sendResponse([
            'profile' => new ProfileResource($userInfo['profile']),
            'user' => new UserResource($userInfo['user']),
            'token' => $userInfo['token'],
            'accesses' => $userInfo['accesses'],
            'notification_settings' => $userInfo['notification_settings'],
            'appearence' => $userInfo['appearence'],
        ]);
    }

    /**
     * Returns user info with token from sso
     *
     * @param AuthByProfileRequest $request
     * @param Profile $profile
     * @return ProfileResource|JsonResponse
     */
    public function authByProfile(AuthByProfileRequest $request, Profile $profile): ProfileResource|JsonResponse
    {
        $userInfo = $this->service->authByProfile($request->validated(), $profile->id, $request);

        if (isset($userInfo['errors'])) {
            return $this->sendErrorResponse($userInfo);
        }

        return $this->sendResponse([
            'profile' => new ProfileResource($userInfo['profile']),
            'user' => new UserResource($userInfo['user']),
            'token' => $userInfo['token'],
            'accesses' => $userInfo['accesses'],
            'notification_settings' => $userInfo['notification_settings'],
            'appearence' => $userInfo['appearence'],
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param LogoutUserRequest $request
     * @return JsonResponse
     */
    public function logoutUserByEmail(LogoutUserRequest $request): JsonResponse
    {
        $this->service->logoutUserByEmail($request->safe()->email);

        return $this->sendResponse();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->service->logout();

        return $this->sendResponse();
    }
}
