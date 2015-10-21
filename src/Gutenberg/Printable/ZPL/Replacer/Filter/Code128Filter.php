<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 13.02.15
 * Time: 15:59
 */

namespace Gutenberg\Printable\ZPL\Replacer\Filter;


class Code128Filter
{
    const CODE_A = 0x1;
    const CODE_B = 0x2; // normal
    const CODE_C = 0x3; // numeric

    /**
     * Filter given value
     * @param $value
     * @param array $arguments optional arguments list
     */
    public function filterValue($value, $arguments = [])
    {
        $value = (string)$value;
        $strlen     = strlen($value);
        $output     = '';
        $lastCode   = null;

        for ($i = 0; $i < $strlen; $i++) {
            $chr        = $value[$i];
            $chrNext    = isset($value[$i+1]) ? $value[$i+1] : null;
            $nextCode   = self::CODE_B;

            if (is_numeric($chr) && $chrNext !== NULL && is_numeric($chrNext)) {
                $nextCode = self::CODE_C;
            }

            if (empty($output)) {
                if ($nextCode == self::CODE_C) {
                    $output .= '>;';
                }
                else {
                    $output .= '>:';
                }
            }

            if ($lastCode && $nextCode != $lastCode) {
                if ($nextCode == self::CODE_C) {
                    $output .= '>5';
                }
                else {
                    $output .= '>6';
                }
            }

            $output     .= $chr;
            $lastCode   = $nextCode;

            if ($nextCode == self::CODE_C) {
                $output .= $chrNext;
                $i++;
            }
        }

        return $output;
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
