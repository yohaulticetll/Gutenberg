<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 13.02.15
 * Time: 15:59
 */

namespace Gutenberg\Printable\ZPL\Replacer\Filter;


class Code128Filter implements FilterInterface
{
    /**
     * Filter given value
     * @param $value
     * @param array $arguments optional arguments list
     */
    public function filterValue($value, $arguments = [])
    {
        $lastNumeric = null;

        for ($i = 0; $i < strlen($value); $i++) {
            $chr = $value[$i];

            if (!is_numeric($chr)) {
                $lastNumeric = null;
            }
            elseif (!$lastNumeric) {
                $lastNumeric = $i;
            }
        }

        if ($lastNumeric) {
            return substr($value, 0, $lastNumeric) . '>5' . substr($value, $lastNumeric);
        }

        return $value;
    }

    /**
     * Get filter name used to identify in ZPL tag
     * @return string
     */
    public function getName()
    {
        return 'code128';
    }
}