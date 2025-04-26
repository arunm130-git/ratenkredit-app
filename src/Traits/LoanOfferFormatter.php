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

    protected function formatDuration(string $duration): string
    {
        if (is_numeric($duration)) {
            return $duration;
        }

        $duration = \DateInterval::createFromDateString($duration);

        if (isset($duration->y, $duration->m)) {
            return $duration->y * 12 + $duration->m;
        }

        throw new InvalidArgumentException('Invalid duration format: ' . $duration);
    }
}
