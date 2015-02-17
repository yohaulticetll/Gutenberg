<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 13.02.15
 * Time: 16:05
 */
namespace Gutenberg\Printable\ZPL\Replacer\Filter;

interface FilterInterface
{
    /**
     * Filter given value
     * @param $value
     * @param array $arguments optional arguments list
     */
    public function filterValue($value, $arguments = []);

    /**
     * Get filter name used to identify in ZPL tag
     * @return string
     */
    public function getName();
}