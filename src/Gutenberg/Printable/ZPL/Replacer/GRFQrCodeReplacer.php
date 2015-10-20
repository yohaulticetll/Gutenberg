<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 09:43
 */

namespace Gutenberg\Printable\ZPL\Replacer;


use Gutenberg\Printable\ZPL\PreprocessorInterface;
use Gutenberg\Printable\ZPL\Replacer\GRFBarcodeReplacer\GRF;
use Gutenberg\Printable\ZPL\Util\ImageBox;
use Gutenberg\Printable\ZPL\Util\ImageQRCode;

class GRFQrCodeReplacer implements PreprocessorInterface {
    use ImageReplacerTrait;

    /** @var VariableReplacer */
    private $variableReplacer;

    /**
     * GRFQrCodeReplacer constructor.
     * @param VariableReplacer $variableReplacer
     */
    public function __construct(VariableReplacer $variableReplacer)
    {
        $this->variableReplacer = $variableReplacer;
    }

    public function replace($zpl, array $params = [])
    {
        $variableReplacer = $this->variableReplacer;
        $zpl = $this->replaceImages(
            $zpl,
            function ($grf) use ($variableReplacer, $params) {
                $image = $grf->toImage();

                $raw = trim(ImageQRCode::decode($image));
                $replaced = $variableReplacer->replace($raw, $params);

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
