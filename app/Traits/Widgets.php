<?php

namespace App\Traits;

trait Widgets
{
    use Dates, FinanceProcessing;

    public function importantDates(array $dates): ?array
    {
        return $this->formatSessionDates($dates);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function financeInfo(array $data): ?array
    {
        return $this->processPaymentData($data);
    }
}
