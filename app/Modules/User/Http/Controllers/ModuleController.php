<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\User\Http\Requests\DeleteModuleRequest;
use App\Modules\User\Http\Requests\StoreModuleRequest;
use App\Modules\User\Http\Requests\SwapModuleRequest;
use App\Modules\User\Models\Module;
use App\Modules\User\Services\ModuleService;
use Illuminate\Http\JsonResponse;

class ModuleController extends BaseController
{
    protected ModuleService $service;

    public function __construct(ModuleService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $modules = Module::with('accesses')->get();

        return $this->sendResponse($modules->toArray());
    }

    /**
     * @throws \Exception
     */
    public function store(StoreModuleRequest $request): JsonResponse
    {
        return $this->sendResponse($this->service->create($request->validated()));
    }

    /**
     * @throws \Exception
     */
    public function destroy(DeleteModuleRequest $request, Module $module): JsonResponse
    {
        return $this->sendResponse($this->service->delete($request->validated(), $module->id));
    }

    /**
     * @throws \Exception
     */
    public function swap(SwapModuleRequest $request, Module $module): JsonResponse
    {
        return $this->sendResponse($this->service->swap($request->validated(), $module->id));
    }
}
