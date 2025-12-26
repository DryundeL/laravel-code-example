<?php

namespace App\Traits;

use Carbon\Carbon;

trait FinanceProcessing
{
    /**
     * @param array $receipts Массив платежей (receipts).
     * @param array $refunds Массив возвратов (refunds).
     * @return array Объединенный и отсортированный массив 'paymentHistory'.
     */
    public function groupPaymentHistory(array $receipts, array $refunds): array
    {
        $paymentHistory = [];

        foreach ($receipts as $receipt) {
            if (isset($receipt['date'], $receipt['sum'])) {
                $date = Carbon::parse($receipt['date']);

                $formattedDate = $date->format('d.m.Y');

                $paymentHistory[] = [
                    'date' => $formattedDate,
                    'sum' => $receipt['sum'],
                    'type' => 'receipt'
                ];
            }
        }

        foreach ($refunds as $refund) {
            if (isset($refund['date'], $refund['sum'])) {
                $date = Carbon::parse($refund['date']);

                $formattedDate = $date->format('d.m.Y');

                $paymentHistory[] = [
                    'date' => $formattedDate,
                    'sum' => $refund['sum'],
                    'type' => 'refund'
                ];
            }
        }

        usort($paymentHistory, static function ($a, $b) {
            $dateA = Carbon::createFromFormat('d.m.Y', $a['date']);
            $dateB = Carbon::createFromFormat('d.m.Y', $b['date']);

            if ($dateA == $dateB) {
                return 0;
            }

            return ($dateA > $dateB) ? -1 : 1;
        });

        return $paymentHistory;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function processPaymentData(array $data): array
    {
        $semesterSums = [];
        foreach ($data['paymentShedule'] as $schedule) {
            $semester = $schedule['semestr'];
            if (!isset($semesterSums[$semester])) {
                $semesterSums[$semester] = 0;
            }
            $semesterSums[$semester] += $schedule['sum'];
        }

        $totalSemesters = count($semesterSums) > 0 ? max(array_keys($semesterSums)) : 0;

        $paidSemesters = 0;
        $remainingPaidSum = $data['payedSum'];
        ksort($semesterSums);

        foreach ($semesterSums as $semester => $requiredSum) {
            if ($remainingPaidSum >= $requiredSum) {
                $paidSemesters++;
                $remainingPaidSum -= $requiredSum;
            } else {
                break;
            }
        }

        $debt = $data['debtSum'];
        $paidSum = $data['payedSum'];

        $nextPayment = null;
        $nextPaymentDate = null;
        $currentDate = now();

        foreach ($data['paymentShedule'] as $schedule) {
            $scheduleDate = new \DateTime($schedule['date']);
            if (($data['expectedPayment'] > 0) && $scheduleDate > $currentDate) {
                $nextPayment = $schedule['sum'];
                $nextPaymentDate = $scheduleDate->format('d.m.Y');
                break;
            }
        }

        $totalToPay = array_sum($semesterSums);

        if ($totalToPay > 0) {
            $percentages = [
                [
                    'type' => 'paidSum',
                    'percents' => round(($paidSum / $totalToPay) * 100, 2)
                ],
                [
                    'type' => 'debt',
                    'percents' => round(($debt / $totalToPay) * 100, 2)
                ],
                [
                    'type' => 'nextPayment',
                    'percents' => $nextPayment ? round(($nextPayment / $totalToPay) * 100, 2) : 0.00
                ],
                [
                    'type' => 'totalSum',
                    'percents' => 100.00
                ]
            ];
        } else {
            $percentages = [
                ['type' => 'paid_sum', 'percents' => 0.00],
                ['type' => 'debt', 'percents' => 0.00],
                ['type' => 'next_payment', 'percents' => 0.00],
                ['type' => 'total_sum', 'percents' => 0.00]
            ];
        }

        return [
            'paid_semesters' => $paidSemesters,
            'total_semesters' => $totalSemesters,
            'debt' => $data['debtSum'],
            'paid_sum' => $paidSum,
            'next_payment' => [
                'sum' => $nextPayment,
                'date' => $nextPaymentDate,
            ],
            'total_sum' => $totalToPay,
            'percentages' => $percentages,
            'active_discounts' => $data['activeDiscounts'],
        ];
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function groupPaymentDataBySemesters(array $data): array
    {
        $paymentSchedule = $data['paymentShedule'];
        $paidSum = $data['payedSum'];
        $currentDate = now();

        $semestersData = [];
        foreach ($paymentSchedule as $schedule) {
            $semester = $schedule['semestr'];
            if (!isset($semestersData[$semester])) {
                $semestersData[$semester] = [
                    'total_required' => 0,
                    'paid_amount' => 0,
                    'is_fully_paid' => false,
                    'payments' => []
                ];
            }
            $semestersData[$semester]['total_required'] += $schedule['sum'];
            $semestersData[$semester]['payments'][] = [
                'date' => $schedule['date'],
                'sum' => $schedule['sum'],
                'is_paid' => false
            ];
        }

        $remainingPaidSum = $paidSum;
        foreach ($semestersData as $semester => &$item) {
            if ($remainingPaidSum >= $item['total_required']) {
                $item['paid_amount'] = $item['total_required'];
                $remainingPaidSum -= $item['total_required'];
                $item['is_fully_paid'] = true;
                foreach ($item['payments'] as &$payment) {
                    $payment['is_paid'] = true;
                }
                unset($payment);
            } else {
                $item['paid_amount'] = $remainingPaidSum;
                $item['is_fully_paid'] = false;
                $semesterRemainingSum = $remainingPaidSum;

                foreach ($item['payments'] as &$payment) {
                    if ($semesterRemainingSum >= $payment['sum']) {
                        $payment['is_paid'] = true;
                        $semesterRemainingSum -= $payment['sum'];
                    } else {
                        $payment['is_paid'] = false;
                    }
                }
                unset($payment);
                $remainingPaidSum = 0;
            }
        }
        unset($item);

        $semesters = [];

        foreach ($semestersData as $semester => $info) {
            $debt = 0;
            foreach ($info['payments'] as $payment) {
                $paymentDate = new \DateTime($payment['date']);
                if ($paymentDate < $currentDate && !$payment['is_paid']) {
                    $debt += $payment['sum'];
                }
            }

            $semesters[] = [
                'semester' => $semester,
                'is_fully_paid' => $info['is_fully_paid'],
                'paid_amount' => $info['paid_amount'],
                'total_required' => $info['total_required'],
                'debt' => $debt,
                'payments' => array_map(static function ($payment) {
                    return [
                        'date' => (new \DateTime($payment['date']))->format('d.m.Y'),
                        'sum' => $payment['sum'],
                        'is_paid' => $payment['is_paid']
                    ];
                }, $info['payments'])
            ];
        }

        $nextPayment = null;
        $nextPaymentDate = null;
        foreach ($paymentSchedule as $schedule) {
            $scheduleDate = new \DateTime($schedule['date']);
            if (($data['expectedPayment'] > 0) && $scheduleDate > $currentDate) {
                $nextPayment = $schedule['sum'];
                $nextPaymentDate = $scheduleDate->format('d.m.Y');
                break;
            }
        }

        return [
            'semesters' => $semesters,
            'current' => [
                'next_payment_date' => $nextPaymentDate,
                'recommended_payment' => $nextPayment
            ]
        ];
    }

    private function processPayment(array $payment): mixed
    {
        $payment['date'] = date('d.m.Y', strtotime($payment['date']));

        if (!empty($payment['file'])) {
            $payment['link'] = config('app.url') . '/storage/payments/' . $payment['file'] . '.zip';
        } else {
            $payment['link'] = null;
        }

        unset($payment['file']);

        return $payment;
    }
}
