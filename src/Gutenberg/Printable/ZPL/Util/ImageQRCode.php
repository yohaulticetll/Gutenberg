<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 10:47
 */

namespace Gutenberg\Printable\ZPL\Util;


use Symfony\Component\Process\ProcessBuilder;

class ImageQRCode {
    /**
     * Decodes specified Image to QR code raw data
     * @param $image Image to decode
     * @return string
     */
    public static function decode(Image $image)
    {
        $process = ProcessBuilder::create([
            'zbarimg',
            '--raw',
            '-q',
            ':'
        ])->setInput($image->getBinary())->getProcess();

        $process->run();

        if ($process->isSuccessful())
            return $process->getOutput();
    }

    /**
     * Encodes string into QR code image
     * @param $data
     * @return Image
     */
    public static function encode($data)
    {
        $process = ProcessBuilder::create([
            'qrencode',
            '-l',
            'M',
            '-s',
            '8',
            '-m',
            '0',
            '-o',
            '-',
            $data
        ])->getProcess();

        $process->run();

        if ($process->isSuccessful()) {
            $image = Image::fromBinary($process->getOutput());
            return $image;
        }
    }
}