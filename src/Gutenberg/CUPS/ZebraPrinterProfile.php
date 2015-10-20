<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 11.12.14
 * Time: 00:37
 */

namespace Gutenberg\CUPS;


class ZebraPrinterProfile extends PrinterProfile {
    /**
     * @return string
     */
    public function getOptions()
    {
        $defaultOptions = [
            'raw'  => true
        ];

        return array_merge($defaultOptions, $this->options);
    }
}