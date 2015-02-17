<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 09.02.15
 * Time: 11:37
 */

namespace Gutenberg\Printable\ZPL\Util;

class ImageBox {
    /**
     * @var Image instance
     */
    private $image;

    /**
     * @var integer x coordinate of box
     */
    private $x;

    /**
     * @var integer y coordinate of box
     */
    private $y;

    /**
     * @var integer width of box
     */
    private $width;

    /**
     * @var integer height of box
     */
    private $height;

    /**
     * @param Image $image
     * @param string $background
     */
    public function __construct(Image $image)
    {
        $this->image        = $image;

        $this->determineImageBox();
    }

    /**
     * @param Image $image
     */
    protected function determineImageBox()
    {
        $image = $this->image;

        $min = [$image->getWidth(), $image->getHeight()];
        $max = [0, 0];

        for ($x = 0; $x < $image->getWidth(); $x++) {
            for ($y = 0; $y < $image->getHeight(); $y++) {
                if ($image->isFilledAt($x, $y)) {
                    $min = [min($x, $min[0]), min($y, $min[1])];
                    $max = [max($x, $max[0]), max($y, $max[1])];
                }
            }
        }

        $this->x = $min[0];
        $this->y = $min[1];

        $this->width = $max[0] - $this->x + 1;
        $this->height = $max[1] - $this->y + 1;
    }

    /**
     * @param Image $replacement
     * @return Image
     */
    public function getReplacedByImage(Image $replacement)
    {
        $image = Image::blank($this->image->getWidth(), $this->image->getHeight());

        return $image->insert(
            $replacement,
            $this->x,
            $this->y,
            $this->width,
            $this->height
        );
    }

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}