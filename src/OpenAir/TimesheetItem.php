<?php

namespace App\OpenAir;

use Assert\Assert;

/**
 * Class TimesheetItem
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class TimesheetItem
{
    const ALLOWED_HOUR_KEYS = [
        'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
    ];

    /** @var string */
    private $client;

    /** @var string */
    private $task;

    /** @var array */
    private $hours;

    /**
     * TimesheetItem constructor.
     *
     * @param string $client
     * @param string $task
     * @param array  $hours
     */
    public function __construct($client, $task, array $hours)
    {
        foreach (array_keys($hours) as $day) {
            Assert::that($day)->inArray(self::ALLOWED_HOUR_KEYS);
        }

        $this->client = $client;
        $this->task = $task;
        $this->hours = $hours;
    }

    /**
     * @return string
     */
    public function getClient(): string
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * @return array
     */
    public function getHours(): array
    {
        return $this->hours;
    }
}
