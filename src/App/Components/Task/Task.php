<?php

/**
 * (c) Stephen Pennell <ste@steyep.com>
 */

namespace App\Components\Task;

/**
 * Task class.
 */
class Task implements TaskInterface
{
    use \App\Components\Traits\FormatDurationTrait;

    /**
     * String used to identify this task.
     *
     * @var string
     */
    private $id;

    /**
     * The estimated number of hours to complete this task.
     *
     * @var number
     */
    private $estimate;

    /**
     * Creates a new task.
     *
     * @param string $id
     *   String used to identify this task.
     * @param number $estimate
     *   The estimate number time to complete this task (in hours).
     */
    public function __construct($id, $estimate = 0)
    {
        $this->id = $id;
        $this->setEstimate($estimate);
    }

    /**
     * Setter method for setting the estimate on this task.
     *
     * @param number $estimate
     *   The estimate number time to complete this task (in hours).
     */
    public function setEstimate($estimate)
    {
        $this->estimate = 0;
        if (is_numeric($estimate) && $estimate > 0) {
            $this->estimate = $this->roundToInterval($estimate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function totalEstimate($round = false)
    {
        return number_format($this->estimate, 2);
    }
}
