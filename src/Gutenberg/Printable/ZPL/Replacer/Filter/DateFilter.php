<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 13.02.15
 * Time: 16:15
 */

namespace Gutenberg\Printable\ZPL\Replacer\Filter;


class DateFilter implements FilterInterface {
    /**
     * Filter given value
     * @param $value \DateTime
     * @param array $arguments optional arguments list
     */
    public function filterValue($value, $arguments = [])
    {
        if (!($value instanceof \DateTime)) {
            $value = new \DateTime($value);
        }

        return $value->format($arguments[0]);
    }

    /**
     * Get filter name used to identify in ZPL tag
     * @return string
     */
    public function getName()
    {
        return 'date';
    }

}
