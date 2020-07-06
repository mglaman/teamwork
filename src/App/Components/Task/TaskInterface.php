<?php

/**
 * (c) Stephen Pennell <ste@steyep.com>
 */

namespace App\Components\Task;

/**
 * TaskInterface interface.
 */
interface TaskInterface
{
    /**
     * Get the total estimate for completing this task.
     *
     * @param bool $round
     *   (Optional) Indicates if the total estimate should be rounded to the
     *   next hour. Defaults to false.
     *
     * @return number
     *   The numeric estimate (in hours).
     */
    public function totalEstimate($round = false);

    /**
     * Get the unique identifier for this task.
     */
    public function id();
}
