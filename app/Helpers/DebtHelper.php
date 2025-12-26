<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

if (!function_exists('getDebtLevel')) {

    /**
     * Определяет уровень блокировки по сумме долга
     *
     * @param float $debt Сумма долга
     * @return int Уровень блокировки (0 = нет блокировки)
     */
    function getDebtLevel(float $debt): int
    {
        $DEBT_LEVEL_1 = [10000, 20000];
        $DEBT_LEVEL_2 = [20000, 40000];
        $DEBT_LEVEL_3 = 40000;

        $profile = Auth::user();

        if ($profile) {
            $access = $profile->access;

            if ($access->id === 9) {
                return 0;
            }
        }

        if ($debt >= $DEBT_LEVEL_3) {
            return 3;
        }

        if ($debt >= $DEBT_LEVEL_2[0] && $debt < $DEBT_LEVEL_2[1]) {
            return 2;
        }

        if ($debt >= $DEBT_LEVEL_1[0] && $debt < $DEBT_LEVEL_1[1]) {
            return 1;
        }

        return 0;
    }
}
