<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 29.09.15
 * Time: 11:44
 */

namespace Gutenberg\Printable\ZPL\Replacer\Filter;


class DateFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function getFilter()
    {
        return new DateFilter();
    }

    public function testDateTime()
    {
        $filter = $this->getFilter();
        $date   = new \DateTime('now');
        $format = 'Y-m-d H:i:s';

        $this->assertEquals($date->format($format), $filter->filterValue($date, [$format]));
    }

    public function testDateFromString()
    {
        $filter = $this->getFilter();
        $date   = '2015-01-01';
        $format = 'Y-m-d H:i:s';

        $this->assertEquals((new \DateTime($date))->format($format), $filter->filterValue($date, [$format]));
    }
}
