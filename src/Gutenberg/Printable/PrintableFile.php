<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 28.11.14
 * Time: 11:33
 */

namespace Gutenberg\Printable;


class PrintableFile implements PrintableFileInterface {
    /**
     * @var \SplFileInfo
     */
    protected $file;

    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $content = '';
        $h = fopen((string)$this->getFile(), 'r');

        while (!feof($h)) {
            $content .= fread($h, 1024*1024);
        }

        return $content;
    }
}