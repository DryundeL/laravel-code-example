<?php

namespace App\Helpers;

if (!function_exists('formatTeacher')) {

    /**
     * @param array $data
     * @return array|null
     */
    function formatTeacher(array $data): ?array
    {
        if (array_key_exists('teachers', $data)) {
            if (empty($data['teachers'])) {
                return [];
            }

            $teachers = [];
            foreach ($data['teachers'] as $teacher) {
                $teachers[] = [
                    'id' => $teacher['id'] ?? null,
                    'name' => $teacher['name'] ?? null,
                    'degree' => $teacher['stepen'] ?? null,
                    'rank' => $teacher['zvanie'] ?? null,
                    'gender' => $teacher['gender'] ?? null,
                    'photo_url' => $teacher['avatar'] ?? null,
                ];
            }

            return $teachers;
        }

        if (array_key_exists('teacher', $data)) {
            if (empty($data['teacher'])) {
                return null;
            }

            if (is_array($data['teacher'])) {
                $teacher = $data['teacher'];

                return [
                    'id' => $teacher['id'] ?? null,
                    'email' => $teacher['email'] ?? null,
                    'name' => $teacher['name'] ?? null,
                    'degree' => $teacher['stepen'] ?? null,
                    'description' => $teacher['description'] ?? null,
                    'rank' => $teacher['zvanie'] ?? null,
                    'photo_url' => $teacher['avatar'] ?? null,
                ];
            }

            return [
                'id' => $data['uid'] ?? null,
                'name' => $data['teacher'],
                'degree' => $data['degree'] ?? null,
                'rank' => $data['rank'] ?? null,
                'gender' => $data['gender'] ?? null,
                'photo_url' => $data['avatar'] ?? null,
            ];
        }

        return null;
    }
}
