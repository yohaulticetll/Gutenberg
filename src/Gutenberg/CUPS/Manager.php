<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 02.10.15
 * Time: 07:37
 */

namespace Gutenberg\CUPS;


use Gutenberg\Printer\CUPSPrinter;

/**
 * Class Manager
 * @package Gutenberg\CUPS
 */
class Manager
{
    const LPR_PATH = 'lpr';
    const LPINFO_PATH = 'lpinfo';
    const LPADMIN_PATH = 'lpadmin';

    /** @var string */
    private $lprBinary;

    /** @var string */
    private $lpinfoBinary;

    /** @var string */
    private $lpadminBinary;

    /**
     * Manager constructor.
     * @param string $lprBinary
     * @param string $lpinfoBinary
     * @param string $lpadminBinary
     */
    public function __construct($lprBinary = self::LPR_PATH, $lpinfoBinary = self::LPINFO_PATH, $lpadminBinary = self::LPADMIN_PATH)
    {
        $this->lprBinary = $lprBinary;
        $this->lpinfoBinary = $lpinfoBinary;
        $this->lpadminBinary = $lpadminBinary;
    }

    public function getPrinter(PrinterProfileInterface $printerProfile)
    {
        return new CUPSPrinter($this, $printerProfile);
    }

    /**
     * @return string
     */
    public function getLprBinary()
    {
        return $this->lprBinary;
    }

    /**
     * @return string
     */
    public function getLpinfoBinary()
    {
        return $this->lpinfoBinary;
    }

    /**
     * @return string
     */
    public function getLpadminBinary()
    {
        return $this->lpadminBinary;
    }
}