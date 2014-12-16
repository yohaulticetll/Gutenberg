<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 11.12.14
 * Time: 00:37
 */

namespace Gutenberg\Printer\CUPS;


class ZebraPrinterProfile extends PrinterProfile {
    /**
     * @return string
     */
    public function getOptions()
    {
        $defaultOptions = [
            'fit-to-page' => null,
            'zeLabelTop'  => 0,
            'Darkness'    => 3
        ];

        return array_merge($defaultOptions, $this->options);
    }
}