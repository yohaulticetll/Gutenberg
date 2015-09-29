<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 09:46
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\PreprocessorInterface;
use Gutenberg\Printable\ZPL\Replacer\Filter\Code128Filter;
use Gutenberg\Printable\ZPL\Replacer\Filter\CutFilter;
use Gutenberg\Printable\ZPL\Replacer\Filter\DateFilter;
use Gutenberg\Printable\ZPL\Replacer\Filter\FilterInterface;
use Gutenberg\Printable\ZPL\Replacer\Filter\SeparatorFilter;

class VariableReplacer implements PreprocessorInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct()
    {
        $this->twig = new \Twig_Environment(new \Twig_Loader_String());
        $this->registerFilters();
    }

    /**
     * @return FilterInterface[]
     */
    protected function getFilters()
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

        return $this->twig->render($zpl, $params);
    }

    private function registerFilters()
    {
        foreach ($this->getFilters() as $filter) {
            $this->twig->addFilter(
                new \Twig_SimpleFilter($filter->getName(), function () use ($filter) {
                    $arguments = func_get_args();
                    $value = array_shift($arguments);
                    return $filter->filterValue($value, $arguments);
                })
            );
        }
    }
}
