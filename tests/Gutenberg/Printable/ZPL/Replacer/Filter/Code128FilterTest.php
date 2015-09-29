<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 29.09.15
 * Time: 11:25
 */

namespace Gutenberg\Printable\ZPL\Replacer\Filter;


class Code128FilterTest extends \PHPUnit_Framework_TestCase
{
    protected function getFilter()
    {
        return new Code128Filter();
    }

    public function testEmptyInputUnchanged()
    {
        $filter = $this->getFilter();
        $this->assertEmpty($filter->filterValue(''));
    }

    public function testValidAssertions()
    {
        $filter = $this->getFilter();

        $this->assertEquals('>;12345678>69', $filter->filterValue('123456789'));
        $this->assertEquals('>:A>523456789', $filter->filterValue('A23456789'));
        $this->assertEquals('>;12345678>6B', $filter->filterValue('12345678B'));
    }
}
