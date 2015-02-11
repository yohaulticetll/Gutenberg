<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 09.02.15
 * Time: 14:31
 */

namespace Gutenberg\Printable\ZPL\Util;


class Image {
    /**
     * @var resource
     */
    private $handle;

    private $whiteColor;

    private $blackColor;

    public static function fromBinary($binary)
    {
        $handle     = imagecreatefromstring($binary);
        $whiteColor = imagecolorresolve($handle, 0xFF, 0xFF, 0xFF);
        $blackColor = imagecolorresolve($handle, 0x00, 0x00, 0x00);

        return new self($handle, $whiteColor, $blackColor);
    }

    public static function blank($width, $height)
    {
        $handle     = imagecreate($width, $height);
        $whiteColor = imagecolorallocate($handle, 0xFF, 0xFF, 0xFF);
        $blackColor = imagecolorallocate($handle, 0x00, 0x00, 0x00);

        return new self($handle, $whiteColor, $blackColor);
    }

    private function __construct($handle, $whiteColor, $blackColor)
    {
        $this->handle       = $handle;
        $this->whiteColor   = $whiteColor;
        $this->blackColor   = $blackColor;
    }

    public function getWidth()
    {
        return imagesx($this->handle);
    }

    public function getHeight()
    {
        return imagesy($this->handle);
    }

    public function getBinary()
    {
        ob_start();
        imagepng($this->handle);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * @return resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    public function insert(Image $replacement, $x, $y, $width, $height)
    {
        imagecopyresized(
            $this->handle,
            $replacement->getHandle(),
            $x,
            $y,
            0,
            0,
            $width,
            $height,
            $replacement->getWidth(),
            $replacement->getHeight()
        );

        return $this;
    }

    public function fillAt($x, $y)
    {
        imagesetpixel($this->handle, $x, $y, $this->blackColor);
    }

    public function unfillAt($x, $y)
    {
        imagesetpixel($this->handle, $x, $y, $this->whiteColor);
    }

    public function isFilledAt($x, $y)
    {
        $color = imagecolorat($this->handle, $x, $y);

        return $color == $this->blackColor;
    }
}