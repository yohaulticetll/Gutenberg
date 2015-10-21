<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 27.11.14
 * Time: 12:58
 */

namespace Gutenberg\CUPS\Exception;


use Exception;
use Gutenberg\CUPS\PrinterProfileInterface;

class PrinterProfileNotFoundException extends \InvalidArgumentException {
    /** @var PrinterProfileInterface */
    private $printerProfile;

    /**
     * PrinterProfileNotFoundException constructor.
     * @param PrinterProfileInterface $printerProfile
     * @param int $message
     * @param Exception $code
     * @param Exception $previous
     */
    public function __construct(PrinterProfileInterface $printerProfile, Exception $previous = null)
    {
        $this->printerProfile = $printerProfile;
        parent::__construct(sprintf('Printer profile "%s" not found.', $printerProfile->getName()), $previous);
    }

    /**
     * @return PrinterProfileInterface
     */
    public function getPrinterProfile()
    {
        return $this->printerProfile;
    }
}