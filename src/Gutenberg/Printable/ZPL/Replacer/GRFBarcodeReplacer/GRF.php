<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 10:25
 */

namespace Gutenberg\Printable\ZPL\Replacer\GRFBarcodeReplacer;


use Gutenberg\Printable\ZPL\Util\Image;

class GRF {
    /**
     * @var string
     */
    private $bytes;

    /**
     * @var int
     */
    private $bytesPerRow;

    /**
     * @param $bytes
     * @param $bytesPerRow
     */
    public function __construct($bytes, $bytesPerRow, $raw = false)
    {
        $this->bytesPerRow = (int)$bytesPerRow;
        $this->bytes       = $raw ? $bytes : $this->unpack($bytes);
    }

    /**
     * Convert GRF to Image
     * @return Image
     */
    public function toImage()
    {
        $bytes = $this->bytes;

        $width  = $this->bytesPerRow * 8;
        $height = strlen($bytes) * 4 / $width;

        $image = Image::blank($width, $height);

        $pos = 0;
        foreach (str_split($bytes, 2) as $pair) {
            $value = str_pad(decbin(hexdec($pair)), 8, '0', STR_PAD_LEFT);

            foreach (str_split($value,1) as $dot) {
                $x = $pos % $width;
                $y = intval($pos/$width);

                if ($dot == '1')
                    $image->fillAt($x, $y);

                $pos++;
            }
        }

        return $image;
    }

    /**
     * @param $bytes
     * @return string
     */
    private function unpack($bytes)
    {
        $output = '';
        $offset = 0;
        $multiplicator = 1;
        $charsPerRow = $this->bytesPerRow * 2;

        for ($pos = 0; $pos < strlen($bytes); $pos++) {
            $chr = $bytes[$pos];

            if ($chr == ',' || $chr == '!') {
                $rowBegging = intval($offset / $charsPerRow) * $charsPerRow;
                $rowCurrent = $offset;
                $rowEnd     = $rowBegging + $charsPerRow;
                $padLength  = $rowEnd - $rowCurrent;

                $output .= str_repeat(($chr == ',' ? '0' : 'F'), $padLength);
                $offset += $padLength;
            }
            if ($chr == ':') {
                $output .= substr($output, $offset - $charsPerRow, $charsPerRow);
                $offset += $charsPerRow;
            }
            elseif ($this->isHex($chr)) {
                $output .= str_repeat($chr, $multiplicator);
                $offset += $multiplicator;
            }

            $multiplicator = $this->getMultiplicator($chr);
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * @return int
     */
    public function getBytesPerRow()
    {
        return $this->bytesPerRow;
    }

    /**
     * @param $png string binary content of image
     */
    public static function fromImage(Image $image)
    {
        $bytesPerRow = $image->getWidth() / 8;

        $bits = '';

        for ($y = 0; $y < $image->getHeight(); $y++) {
            for ($x = 0; $x < $image->getWidth(); $x++) {
                $bits  .= $image->isFilledAt($x, $y) ? '1' : '0';
            }
        }

        // split bits array to one-byte packs
        $bytes = implode('', array_map(
            function($byte) {
                return str_pad(dechex(bindec($byte)), 2, '0', STR_PAD_LEFT);
            },
            str_split($bits, 8)
        ));

        return new self($bytes, $bytesPerRow, true);
    }

    /**
     * @param $chr
     * @return bool
     */
    private function isHex($chr )
    {
        switch( $chr )
        {
            case 'A':
            case 'B':
            case 'C':
            case 'D':
            case 'E':
            case 'F':
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
                return true;

            default:
                return false;
        }
    }

    /**
     * @param $chr
     * @return int
     */
    private function getMultiplicator( $chr )
    {
        switch( $chr )
        {
            case 'G': return 1;
            case 'H': return 2;
            case 'I': return 3;
            case 'J': return 4;
            case 'K': return 5;
            case 'L': return 6;
            case 'M': return 7;
            case 'N': return 8;
            case 'O': return 9;
            case 'P': return 10;
            case 'Q': return 11;
            case 'R': return 12;
            case 'S': return 13;
            case 'T': return 14;
            case 'U': return 15;
            case 'V': return 16;
            case 'W': return 17;
            case 'X': return 18;
            case 'Y': return 19;
            case 'g': return 20;
            case 'h': return 40;
            case 'i': return 60;
            case 'j': return 80;
            case 'k': return 100;
            case 'l': return 120;
            case 'm': return 140;
            case 'n': return 160;
            case 'o': return 180;
            case 'p': return 200;
            case 'q': return 220;
            case 'r': return 240;
            case 's': return 260;
            case 't': return 280;
            case 'u': return 300;
            case 'v': return 320;
            case 'w': return 340;
            case 'x': return 360;
            case 'y': return 380;
            case 'z': return 400;

            default:  return 1;
        }
    }
}