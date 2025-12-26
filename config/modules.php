<?php

return [
    'user' => [
        'organizations' => [
            'mipvo' => [
                'user' => App\Modules\User\Services\Handlers\Mipvo\UserService::class,
            ],
            'default' => [
                'user' => App\Modules\User\Services\Handlers\DefaultUserService::class,
            ]
        ]
    ],
    'schedule' => [
        'organizations' => [
            'mipvo' => App\Modules\Schedule\Services\Handlers\Mipvo\ScheduleService::class,
        ]
    ],
    'education' => [
        'organizations' => [
            'mipvo' => [
                'discipline' => App\Modules\Education\Services\Handlers\Mipvo\DisciplineService::class,
                'material' => App\Modules\Education\Services\Handlers\Mipvo\MaterialService::class,
                'test' => App\Modules\Education\Services\Handlers\Mipvo\TestService::class,
                'work' => App\Modules\Education\Services\Handlers\Mipvo\WorkService::class,
                'elective' => \App\Modules\Education\Services\Handlers\Mipvo\ElectiveService::class,
                'academic_schedule' => App\Modules\Education\Services\Handlers\Mipvo\AcademicScheduleService::class,
            ],
            'default' => [
                'discipline' => App\Modules\Education\Services\Handlers\DefaultEducationService::class,
                'material' => App\Modules\Education\Services\Handlers\DefaultEducationService::class,
                'test' => App\Modules\Education\Services\Handlers\DefaultEducationService::class,
                'work' => App\Modules\Education\Services\Handlers\DefaultEducationService::class,
                'elective' => App\Modules\Education\Services\Handlers\DefaultEducationService::class,
                'academic_schedule' => App\Modules\Education\Services\Handlers\DefaultEducationService::class,
            ]
        ]
    ]
];
