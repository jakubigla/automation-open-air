<?php

namespace OpenAir\Filter;

class DateFormat implements FilterInterface
{
    /** @var string */
    private $format;

    /**
     * DateFormat constructor.
     *
     * @param string $format
     */
    public function __construct(string $format = \DateTime::ISO8601)
    {
        $this->format = $format;
    }

    public function filter($value)
    {
        return \date($this->format, \strtotime($value));
    }
}
