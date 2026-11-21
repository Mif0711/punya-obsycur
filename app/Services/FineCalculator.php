<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Setting;

class FineCalculator
{
    /**
     * Calculate the fine for a loan. Grace days are counted after the due date.
     *
     * @return array{days_late: int, amount: float}
     */
    public function calculate(Loan $loan): array
    {
        $returnDate = $loan->return_date?->copy() ?? today();
        $graceDays = Setting::integer(Setting::KEY_GRACE_DAYS, 2);
        $fineStartsAt = $loan->due_date->copy()->addDays($graceDays);

        if ($returnDate->lessThanOrEqualTo($fineStartsAt)) {
            return ['days_late' => 0, 'amount' => 0];
        }

        $daysLate = $fineStartsAt->diffInDays($returnDate);
        $finePerDay = Setting::integer(Setting::KEY_FINE_PER_DAY, 5000);

        return ['days_late' => $daysLate, 'amount' => $daysLate * $finePerDay];
    }
}
