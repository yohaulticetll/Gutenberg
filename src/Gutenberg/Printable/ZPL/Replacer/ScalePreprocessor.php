<?php
/**
 * Created by PhpStorm.
 * User: kofel
 * Date: 22.06.15
 * Time: 13:09
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\code;
use Gutenberg\Printable\ZPL\PreprocessorInterface;
use Gutenberg\Printable\ZPL\Replacer\GRFBarcodeReplacer\GRF;
use Gutenberg\Printable\ZPL\Util\ImageBox;

class ScalePreprocessor implements PreprocessorInterface
{
    use ImageReplacerTrait;

    /**
     * Source DPI
     * @var integer
     */
    private $sourceDpi;

    /**
     * Target DPI
     * @var integer
     */
    private $targetDpi;

    /**
     * ScalePreprocessor constructor.
     * @param int $sourceDpi
     * @param int $targetDpi
     */
    public function __construct($sourceDpi, $targetDpi)
    {
        $this->sourceDpi = (int)$sourceDpi;
        $this->targetDpi = (int)$targetDpi;
    }

    /**
     * @param $zpl code
     * @param $params array|callable
     * @return string preprocessed zpl code
     */
    public function replace($zpl, $params)
    {
        $sourceDpi  = $this->sourceDpi;
        $targetDpi  = $this->targetDpi;
        $ratio      = $targetDpi / $sourceDpi;

        if ($ratio == 1.0) {
            return $zpl;
        }

        $zpl = $this->replaceFieldTypeset($zpl, $ratio);
        $zpl = $this->replacePrintWidth($zpl, $ratio);
        $zpl = $this->replacePrintLength($zpl, $ratio);
        $zpl = $this->replaceBarcodeField($zpl, $ratio);
        $zpl = $this->replaceFont($zpl, $ratio);
        $zpl = $this->replaceFontTTF($zpl, $ratio);
        $zpl = $this->replaceGrf($zpl, $ratio);

        return $zpl;
    }

    /**
     * @return int
     */
    public function getSourceDpi()
    {
        return $this->sourceDpi;
    }

    /**
     * @param int $sourceDpi
     */
    public function setSourceDpi($sourceDpi)
    {
        $this->sourceDpi = $sourceDpi;
    }

    /**
     * @return int
     */
    public function getTargetDpi()
    {
        return $this->targetDpi;
    }

    /**
     * @param int $targetDpi
     */
    public function setTargetDpi($targetDpi)
    {
        $this->targetDpi = $targetDpi;
    }

    /**
     * @param $zpl
     * @param $ratio
     * @return mixed
     */
    public function replacePrintWidth($zpl, $ratio)
    {
        return preg_replace_callback(
            '/\^PW(\d+)/',
            function ($matches) use ($ratio) {
                $width = (int)$matches[1];

                $targetWidth = ceil($width * $ratio);

                return sprintf(
                    '^PW%d',
                    $targetWidth
                );
            },
            $zpl
        );
    }

    /**
     * @param $zpl
     * @param $ratio
     * @return mixed
     */
    public function replacePrintLength($zpl, $ratio)
    {
        return preg_replace_callback(
            '/\^LL(\d+)/',
            function ($matches) use ($ratio) {
                $width = (int)$matches[1];

                $targetWidth = ceil($width * $ratio);

                return sprintf(
                    '^LL%d',
                    $targetWidth
                );
            },
            $zpl
        );
    }

    /**
     * @param $zpl
     * @param $ratio
     * @return mixed
     */
    public function replaceFieldTypeset($zpl, $ratio)
    {
        return preg_replace_callback(
            '/\^FT(\d+),(\d+)/',
            function ($matches) use ($ratio) {
                $x = (int)$matches[1];
                $y = (int)$matches[2];

                $targetX = floor($x * $ratio);
                $targetY = floor($y * $ratio);

                return sprintf(
                    '^FT%d,%d',
                    $targetX,
                    $targetY
                );
            },
            $zpl
        );
    }

    /**
     * @param $zpl
     * @param $ratio
     * @return mixed
     */
    public function replaceFont($zpl, $ratio)
    {
        return preg_replace_callback(
            '/\^A([A-Z0-9])([NRIB]),(\d+),(\d+)/',
            function ($matches) use ($ratio) {
                $fontName       = $matches[1];
                $orientation    = $matches[2];
                $h = (int)$matches[3];
                $w = (int)$matches[4];

                $targetH = floor($h * $ratio);
                $targetW = floor($w * $ratio);

                return sprintf(
                    '^A%s%s,%d,%d',
                    $fontName,
                    $orientation,
                    $targetH,
                    $targetW
                );
            },
            $zpl
        );
    }

    /**
     * @param $zpl
     * @param $ratio
     * @return mixed
     */
    public function replaceBarcodeField($zpl, $ratio)
    {
        return preg_replace_callback(
            '/\^BY(\d+),(\d+),(\d+)/',
            function ($matches) use ($ratio) {
                $w = (int)$matches[1];
                $r = (int)$matches[2];
                $h = (int)$matches[3];

                $targetW = ceil($w * $ratio);
                $targetH = ceil($h * $ratio);

                return sprintf(
                    '^BY%s,%d,%d',
                    $targetW,
                    $r,
                    $targetH
                );
            },
            $zpl
        );
    }

    /**
     * @param $zpl
     * @param $ratio
     * @return mixed
     */
    public function replaceFontTTF($zpl, $ratio)
    {
        return preg_replace_callback(
            '/\^A@([NRIB]),(\d+),(\d+)/',
            function ($matches) use ($ratio) {
                $orientation = $matches[1];
                $h = (int)$matches[2];
                $w = (int)$matches[3];

                $targetH = floor($h * $ratio);
                $targetW = floor($w * $ratio);

                return sprintf(
                    '^A@%s,%d,%d',
                    $orientation,
                    $targetH,
                    $targetW
                );
            },
            $zpl
        );
    }

    public function replaceGrf($zpl, $ratio)
    {
        return $this->replaceImages($zpl, function ($grf) use ($ratio) {
            /**
             * @var GRF $grf
             */
            $image = $grf->toImage();

            $imageBox = new ImageBox($image);
            $imageBox->setWidth($imageBox->getWidth() * $ratio);
            $imageBox->setHeight($imageBox->getHeight() * $ratio);

            return GRF::fromImage(
                $imageBox->getReplacedByImage(
                    $image
                )
            );
        });
    }
}