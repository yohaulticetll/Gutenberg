<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 27.11.14
 * Time: 11:48
 */

namespace Gutenberg\Printable;


interface PrintableFileInterface extends PrintableInterface {
    /**
     * @return \SplFileInfo
     */
    public function getFile();
}