<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 11.12.14
 * Time: 00:37
 */

namespace Gutenberg\Printer\CUPS;


class ZebraPrinterProfile extends PrinterProfile {
    const LABEL_TOP = 'zeLabelTop';
    const DARKNESS  = 'Darkness';

    /**
     * @return string
     */
    public function getOptions()
    {
        $defaultOptions = [
            'zeLabelTop'  => 0,
            'Darkness'    => 3
        ];

        return array_merge($defaultOptions, $this->options);
    }
}