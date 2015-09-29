<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 29.09.15
 * Time: 11:11
 */

namespace Gutenberg\Printable\ZPL\Replacer;


class VariableReplacerTest extends \PHPUnit_Framework_TestCase
{
    public function testRender()
    {
        $expected = 'Hello world!';

        $replacer = $this->getReplacer();
        $ret = $replacer->replace(
            '{{ var }}', [
                'var' => $expected
            ]
        );

        $this->assertEquals($ret, $expected, 'Is working properly');
    }

    public function testRenderRegisteredFilter()
    {
        $expected = 'Hello world!';
        $cutVar = 'a';

        $replacer = $this->getReplacer();
        $ret = $replacer->replace('{{ var|cut(cutVar) }}', [
            'var' => $expected,
            'cutVar' => $cutVar
        ]);

        $this->assertEquals($ret, str_replace($cutVar, '',  $expected), 'Is replacing with valid filter');
    }

    /**
     * @return VariableReplacer
     */
    protected function getReplacer()
    {
        $replacer = new VariableReplacer();
        return $replacer;
    }
}
