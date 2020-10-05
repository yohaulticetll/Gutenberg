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

    /**
     * VariableReplacer constructor.
     */
    public function __construct()
    {
        $this->createTwigInstance();
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

    public function replace($zpl, array $params = [])
    {
        if (empty($zpl))
            return '';

        return $this->twig->createTemplate($zpl)->render($params);
    }

    private function createTwigInstance()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array());

        foreach ($this->getFilters() as $filter) {
            $twig->addFilter(
                new \Twig_SimpleFilter($filter->getName(), function () use ($filter) {
                    $arguments = func_get_args();
                    $value = array_shift($arguments);
                    return $filter->filterValue($value, $arguments);
                }, ['is_safe' => ['html']])
            );
        }
        $this->twig = $twig;
    }
}
