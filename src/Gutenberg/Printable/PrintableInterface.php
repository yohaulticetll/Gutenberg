<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 18.11.14
 * Time: 17:13
 */

namespace Gutenberg\Printable;


interface PrintableInterface {
    /**
     * @return string
     */
    public function getContent();
}