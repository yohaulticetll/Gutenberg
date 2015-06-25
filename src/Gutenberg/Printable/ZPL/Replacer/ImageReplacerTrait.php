<?php
/**
 * Created by PhpStorm.
 * User: kofel
 * Date: 22.06.15
 * Time: 14:30
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\Replacer\GRFBarcodeReplacer\GRF;

trait ImageReplacerTrait
{
    public function replaceImages($zpl, $callback)
    {
        return preg_replace_callback(
            '/~DG(\d+.GRF),\d+,(\d+),([^\~\^]+)/',
            function ($matches) use ($callback) {
                $name           = $matches[1];
                $bytesPerRow    = $matches[2];
                $bytes          = $matches[3];

                $grf = new GRF($bytes, $bytesPerRow);

                $grf = $callback($grf);

                if (!$grf) {
                    return $matches[0];
                }

                return sprintf('~DG%s,%d,%d,%s',
                    $name,
                    strlen($grf->getBytes()) / 2,
                    $grf->getBytesPerRow(),
                    $grf->getBytes()
                );
            },
            $zpl
        );
    }
}