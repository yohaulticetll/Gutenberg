<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 09.02.15
 * Time: 11:11
 */
namespace Gutenberg\Printable\ZPL;

interface PreprocessorInterface
{
    /**
     * @param $zpl code
     * @param $params array|callable
     * @return string preprocessed zpl code
     */
    public function replace($zpl, $params);
}