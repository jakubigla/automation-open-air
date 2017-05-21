<?php declare(strict_types=1);

namespace App\Filter;

/**
 * Class DateFormat
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
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

    /**
     * @param $value
     *
     * @return mixed
     */
    public function filter($value)
    {
        return \date($this->format, \strtotime($value));
    }
}
