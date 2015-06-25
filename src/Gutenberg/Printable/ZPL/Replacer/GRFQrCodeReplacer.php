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
    use ImageReplacerTrait;

    public function replace($zpl, $params)
    {
        $zpl = $this->replaceImages(
            $zpl,
            function ($grf) use ($params) {
                $image = $grf->toImage();

                $raw = trim(ImageQRCode::decode($image));
                $replaced = parent::replace($raw, $params);

                if ($raw !== null && $raw != $replaced && !empty($replaced)) {
                    $imageBox   = new ImageBox($image);

                    return GRF::fromImage(
                        $imageBox->getReplacedByImage(
                            ImageQRCode::encode($replaced)
                        )
                    );
                }
            }
        );

        return $zpl;
    }
}
