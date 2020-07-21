<?php

/**
 * (c) Stephen Pennell <ste@steyep.com>
 */

namespace Teamwork\App\Commands;

use App\Components\Estimate\Estimate;
use App\Components\Task\Task;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

/**
 * CreateEstimate class.
 */
class CreateEstimate extends Command
{
    /**
     * Validation callback to ensure a value is numeric.
     *
     * @param string $value
     *   The value being validated.
     *
     * @return number
     *   The numeric value.
     */
    public static function isNumeric($value)
    {
        if ($value && !is_numeric($value)) {
            throw new \RuntimeException('The value must be numeric');
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('create-estimate')
            ->setDescription('Create an estimate')
            ->addOption('use-defaults', null, InputOption::VALUE_OPTIONAL, 'Prompt for default tasks', false);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $estimate = new Estimate();
        $helper = $this->getHelper('question');
        $promptForEstimate = function (Task $task) use ($input, $output, $helper) {
            $default = $task->totalEstimate();
            $question = new Question("{$task->id()} [{$default}]: ", $default);
            $question->setValidator([static::class, 'isNumeric']);
            $task->setEstimate($helper->ask($input, $output, $question));
        };

        /** @var \App\Components\Task\TaskCollection $developmentTasks */
        $developmentTasks = $estimate->getTask('dev');
        $calculateQaTime = false;
        // Set the default development task description to "Development time".
        $defaultDevTask = 'Development time';

        // Allow development tasks to be created dynamically.
        while (true) {
            $developmentTaskQuestion = 'Enter a description of the dev task '.($defaultDevTask ? "[{$defaultDevTask}]" : '(Leave blank to continue)').': ';
            $question = new Question($developmentTaskQuestion, $defaultDevTask);
            $taskDescription = $helper->ask($input, $output, $question);
            /** @var \App\Components\Task\Task $task */
            if (!($task = $developmentTasks->createTask($taskDescription, 1.00))) {
                break;
            }
            $calculateQaTime = true;
            $promptForEstimate($task);
            // After the first dev task has been created, nullify the default.
            $defaultDevTask = null;
        }

        // If development tasks were added to the estimate, we can revise the
        // QA estimate to be 1/3 the development time.
        if ($calculateQaTime && ($qa = $estimate->getTask('qa'))) {
            $devEstimate = $developmentTasks->totalEstimate();
            $qa->setEstimate($devEstimate / 3);
        }

        // If the user is running this command without the use-defaults flag,
        // we need to loop through the default tasks and allow their estimates
        // to be altered.
        if ($input->getOption('use-defaults') === false) {
            foreach ($estimate->getTasks() as $task) {
                if (!$developmentTasks->contains($task)) {
                    $promptForEstimate($task);
                }
            }
        }

        $output->writeln('');
        $output->writeln('Estimate breakdown:');
        foreach ($estimate->getTasks() as $task) {
            if ($total = $task->totalEstimate()) {
                $output->writeln(sprintf('<info>  %s H: %s</info>', $total, $task->id()));
            }
        }
        $output->writeln(sprintf('%s Hours Total', $estimate->totalEstimate()));

        return Command::SUCCESS;
    }
}
