<?php

namespace App\Services;

use App\ActionHandler\ActionHandler;
use App\Exception\ActionTimeoutException;

class ActionRunner
{
    /**
     * @throws ActionTimeoutException
     */
    public function run(
        ActionHandler $decider,
        int $maximumDurationInMicroseconds,
        int $retryPeriodInMicroseconds
    ): void {
        $duration = 0;

        while (false === ($decision = $decider()) && $duration < $maximumDurationInMicroseconds) {
            usleep($retryPeriodInMicroseconds);
            $duration += $retryPeriodInMicroseconds;
        }

        if (false === $decision) {
            throw new ActionTimeoutException();
        }
    }
}
