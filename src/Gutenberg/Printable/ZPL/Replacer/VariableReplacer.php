<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 09:46
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\PreprocessorInterface;
use Gutenberg\Printable\ZPL\Replacer\Exception\FilterNotFoundException;
use Gutenberg\Printable\ZPL\Replacer\Exception\OutOfBoundsException;
use Gutenberg\Printable\ZPL\Replacer\Exception\VariableNotFoundException;
use Gutenberg\Printable\ZPL\Replacer\Filter\Code128Filter;
use Gutenberg\Printable\ZPL\Replacer\Filter\CutFilter;
use Gutenberg\Printable\ZPL\Replacer\Filter\DateFilter;
use Gutenberg\Printable\ZPL\Replacer\Filter\FilterInterface;
use Gutenberg\Printable\ZPL\Replacer\Filter\SeparatorFilter;

class VariableReplacer implements PreprocessorInterface
{
    const REGEXP_VARIABLE_TAG   = '/<([^\>]*)>/';
    const SEPERATOR_CONTEXT     = '.';

    /**
     * @var FilterInterface[]
     */
    private $filters;

    public function __construct()
    {
        $this->filters = $this->getAvailableFilters();
    }

    /**
     * @return FilterInterface[]
     */
    private function getFilters()
    {
        return [
            new Code128Filter(),
            new CutFilter(),
            new DateFilter(),
            new SeparatorFilter(),
        ];
    }

    public function replace($zpl, $params)
    {
        if (empty($zpl))
            return;

        $zpl = preg_replace_callback(
            self::REGEXP_VARIABLE_TAG,
            function ($matches) use($params) {
                $context = $matches[1];

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
    private function extractValue($params, $context)
    {
        if (is_callable($params))
            return $params($context);

        $value = $params;
        $parts = explode(self::SEPERATOR_CONTEXT, $context);
        for ($i = 0; $i < count($parts); $i++) {
            $filters = explode('|', $parts[$i]);
            $key = array_shift($filters);

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

        return $this->filterValue($filters, $value);
    }

    private function filterValue($filters, $value)
    {
        foreach ($filters as $identifier) {
            $arguments = explode(',', $identifier);

            $filter = trim(array_shift($arguments));
            if (empty($filter))
                continue;

            $filter = $this->getFilter($filter);
            $value = $filter->filterValue($value, $arguments);
        }

        return $value;
    }

    private function getAvailableFilters()
    {
        $map = [];

        foreach ($this->getFilters() as $filter) {
            $map[$filter->getName()] = $filter;
        }

        return $map;
    }

    /**
     * @param $filterName
     * @return FilterInterface
     */
    private function getFilter($filterName)
    {
        if (!isset($this->filters[$filterName])) {
            throw new FilterNotFoundException($filterName);
        }

        return $this->filters[$filterName];
    }
}
