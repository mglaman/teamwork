<?php

/**
 * (c) Stephen Pennell <ste@steyep.com>
 */

namespace App\Components\Task;

/**
 * TaskCollection class.
 */
class TaskCollection implements TaskInterface
{
    use \App\Components\Traits\FormatDurationTrait;

    /**
     * Array of tasks.
     *
     * @var App\Components\Task\TaskInterface[]
     */
    private $tasks = [];

    /**
     * String used to identify this collection.
     *
     * @var string
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Add a task to the estimate.
     *
     * @param string $id
     *   Description of the task.
     * @param float  $estimate
     *   Estimated time to complete the task (in hours).
     *
     * @return \App\Components\Task\Task
     */
    public function createTask($id, $estimate = 0)
    {
        if (($id = trim($id)) && $this->getTask($id) === null) {
            $task = new Task($id, $estimate);

            return $this->addTask($task);
        }
    }

    /**
     * Add a task to the collection.
     *
     * @param \App\Components\Task\TaskInterface $task
     *
     * @return \App\Components\Task\TaskInterface
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks[strtolower($task->id())] = $task;

        return $task;
    }

    /**
     * Get a task by ID from the collection.
     *
     * @param string $id
     *   Identifier of the task.
     *
     * @return \App\Components\TaskInterface\Task
     */
    public function getTask($id)
    {
        return $this->tasks[strtolower($id)] ?? null;
    }

    /**
     * Determine if a task is contained in this collection.
     *
     * @param \App\Components\Task\Task $task
     *   The task being checked.
     *
     * @return bool
     *   True if the collection contains the provided task; false otherwise.
     */
    public function contains(Task $task)
    {
        return $this->getTask($task->id()) !== null;
    }

    /**
     * Get the tasks associated with this estimate.
     */
    public function getTasks()
    {
        foreach ($this->tasks as $task) {
            /** @var \App\Components\Task\TaskInterface $task */
            if ($task instanceof TaskCollection) {
                yield from $task->getTasks();
                continue;
            }
            yield $task;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function totalEstimate($round = false)
    {
        $total = 0;
        foreach ($this->getTasks() as $task) {
            /** @var \App\Components\Task\TaskInterface $task */
            $total += (float) $task->totalEstimate($round);
        }

        return $total ? number_format($total, 2) : '';
    }
}
