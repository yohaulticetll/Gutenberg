<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 09:43
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\Replacer\GRFBarcodeReplacer\GRF;
use Gutenberg\Printable\ZPL\Util\ImageBox;
use Gutenberg\Printable\ZPL\Util\ImageQRCode;

class GRFQrCodeReplacer extends VariableReplacer {
    const REGEXP_GRF = '/~DG(\d+.GRF),\d+,(\d+),([^\~\^]+)/';
    const BUILD_GRF  = '~DG%s,%d,%d,%s';

    public function replace($zpl, $params)
    {
        $zpl = preg_replace_callback(
            self::REGEXP_GRF,
            function ($matches) use ($params) {
                $name           = $matches[1];
                $bytesPerRow    = $matches[2];
                $bytes          = $matches[3];

                $grf = new GRF($bytes, $bytesPerRow);
                $image = $grf->toImage();

                $raw = trim(ImageQRCode::decode($image));
                $replaced = parent::replace($raw, $params);

                if ($raw !== null && $raw != $replaced) {
                    $imageBox   = new ImageBox($image);
                    $grf        = GRF::fromImage(
                        $imageBox->getReplacedByImage(
                            ImageQRCode::encode($replaced)//->resize($image->getWidth(), $image->getHeight())
                        )
                    );

                    return sprintf(self::BUILD_GRF,
                        $name,
                        strlen($grf->getBytes()) / 2,
                        $grf->getBytesPerRow(),
                        $grf->getBytes()
                    );
                }

                return $matches[0];
            },
            $zpl
        );

        return $zpl;
    }
}

