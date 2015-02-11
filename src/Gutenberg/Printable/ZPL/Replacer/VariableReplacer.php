<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 09:46
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\PreprocessorInterface;
use Gutenberg\Printable\ZPL\Replacer\Exception\OutOfBoundsException;
use Gutenberg\Printable\ZPL\Replacer\Exception\VariableNotFoundException;

class VariableReplacer implements PreprocessorInterface
{
    const REGEXP_VARIABLE_TAG   = '/<([^\>]*)>/';
    const SEPERATOR_CONTEXT     = '.';

    public function replace($zpl, $params)
    {
        if (empty($zpl))
            return;

        $zpl = preg_replace_callback(
            self::REGEXP_VARIABLE_TAG,
            function ($matches) use($params) {
                $context = trim($matches[1]);

                if (empty($context))
                    return;

                return (string)$this->extractValue($params, $context);
            },
            $zpl
        );

        return $zpl;
    }

    /**
     * @param $params
     * @param $context
     * @return mixed
     */
    function extractValue($params, $context)
    {
        if (is_callable($params))
            return $params($context);

        $value = $params;
        $parts = explode(self::SEPERATOR_CONTEXT, $context);
        for ($i = 0; $i < count($parts); $i++) {
            $key = $parts[$i];

            if (!isset($value[$key])) {
                $currentContext = implode(
                    self::SEPERATOR_CONTEXT,
                    array_slice($parts, 0, $i + 1)
                );

                if ($i == 0)
                    throw new VariableNotFoundException($currentContext);
                else
                    throw new OutOfBoundsException($currentContext);
            }

            $value = $value[$key];
        }

        return $value;
    }
}