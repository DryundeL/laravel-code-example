<?php

namespace App\Modules\User\Services;

use App\Services\BaseService;
use App\Traits\LazyServiceLoader;
use function App\Helpers\formatTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TeacherService extends BaseService
{
    use LazyServiceLoader;

    public function getTeacherById(int $id): array
    {
        $attributes['id'] = $id;
        $payloadData = $this->prepareDataToESB(false,  attributes: $attributes);
        $teacherInfo = $this->handleRequestToESB('getTeacher', $payloadData);

        if (is_array($teacherInfo) && array_key_exists('errors', $teacherInfo)) {
            return $teacherInfo;
        }

        $profileId = Auth::id();
        $userDisciplines = Cache::get("profile_{$profileId}_discipline_ids", []);
        $teacherInfo['teacher'] = formatTeacher($teacherInfo);

        if (empty($userDisciplines)) {
            $teacherInfo['disciplines'] = [];
        } else {
            $userDisciplinesMap = array_flip($userDisciplines);
            $teacherInfo['disciplines'] = array_values(array_filter(
                $teacherInfo['disciplines'] ?? [],
                fn($discipline) => isset($userDisciplinesMap[$discipline['id']])
            ));
        }

        return $teacherInfo;
    }
}
