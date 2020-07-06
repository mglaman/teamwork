<?php

/**
 * (c) Stephen Pennell <ste@steyep.com>
 */

namespace App\Components\Estimate;

use App\Components\Task\TaskCollection;

/**
 * Estimate class.
 */
class Estimate extends TaskCollection
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Estimate');
        $codeReview = (new TaskCollection('code review'));
        $codeReview->createTask('2 Code reviews to merge to develop', 0.50);
        $codeReview->createTask('1 Code review to merge to master', 0.25);

        $this->createTask('Task estimation', 0.25);
        $this->addTask(new TaskCollection('dev'));
        $this->addTask($codeReview);
        $this->createTask('QA', 0.25);
        $this->createTask('Deploy change to production', 0.25);
    }
}
