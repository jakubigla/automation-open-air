<?php declare(strict_types=1);

namespace App\Filter;

/**
 * Interface FilterInterface
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
interface FilterInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value);
}
