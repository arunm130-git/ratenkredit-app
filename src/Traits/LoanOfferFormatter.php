<?php

namespace App\Traits;

use InvalidArgumentException;

trait LoanOfferFormatter
{
    protected function formatInterest(mixed $interest): float
    {
        $interest = trim((string) $interest);

        // Replace comma with dot for locale consistency
        $interest = str_replace(',', '.', $interest);

        // Extract the leading numeric part (integer or decimal)
        if (preg_match('/^(\d+(?:\.\d+)?)/', $interest, $matches)) {
            return (float) $matches[1];
        }

        throw new InvalidArgumentException('Invalid interest format: ' . $interest);
    }

    protected function formatDuration(string $duration): int
    {
        if (is_numeric($duration)) {
            return (int) $duration;
        }

        $durationInterval = \DateInterval::createFromDateString($duration);

        if (isset($durationInterval->y, $durationInterval->m)) {
            return (int) ($durationInterval->y * 12 + $durationInterval->m);
        }

        throw new InvalidArgumentException('Invalid duration format: ' . $duration);
    }
}
