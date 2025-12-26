<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseCheckController extends BaseController
{

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->all();

        if ($request->files->count() > 0) {
            foreach ($request->files as $key => $files) {
                if (is_array($files)) {
                    $fileNames = [];
                    foreach ($files as $file) {
                        if ($file->isValid()) {
                            $fileNames[] = $file->getClientOriginalName();
                        }
                    }
                    $data[$key] = $fileNames;
                } elseif ($files->isValid()) {
                    $data[$key] = $files->getClientOriginalName();
                }
            }
        }

        return $this->sendResponse($data);
    }
}
