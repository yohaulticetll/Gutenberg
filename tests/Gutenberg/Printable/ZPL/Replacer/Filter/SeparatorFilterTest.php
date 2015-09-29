<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 29.09.15
 * Time: 11:47
 */

namespace Gutenberg\Printable\ZPL\Replacer\Filter;


class SeparatorFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function getFilter()
    {
        return new SeparatorFilter();
    }

    public function testSeparator()
    {
        $filter = $this->getFilter();

        $this->assertEquals('aa:bb:cc:dd:ee:ff', $filter->filterValue('aabbccddeeff',  [2, ':']));
    }
}
