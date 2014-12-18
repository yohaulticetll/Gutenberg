<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 27.11.14
 * Time: 11:49
 */

namespace Gutenberg\Printer;


use Gutenberg\Printable\PrintableInterface;
use Gutenberg\Printer\CUPS\Exception\PrinterException;
use Gutenberg\Printer\CUPS\Exception\InvalidPrinterProfileException;
use Gutenberg\Printer\CUPS\PrinterProfileInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

class CUPSPrinter {
    const LPR_TIMEOUT = 2;

    /**
     * Path to CUPS's lpr binary
     * @var string
     */
    private $lprBinaryPath = 'lpr';

    /**
     * @var callable
     */
    private $processCallback;

    /**
     * @param PrintableFileInterface $printable
     * @param PrinterProfileInterface $printerProfile
     */
    public function enqueue(PrintableInterface $printable, PrinterProfileInterface $printerProfile)
    {
        $processBuilder = new ProcessBuilder();
        $processBuilder->setArguments($this->getProcessArguments($printable, $printerProfile));
        $processBuilder->setTimeout(self::LPR_TIMEOUT);
        $processBuilder->setInput($printable->getContent());

        try {
            $process = $processBuilder->getProcess();
            if (is_callable($this->processCallback)) {
                call_user_func($this->processCallback, $process);
            }
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();

                if (strpos($errorOutput, 'The printer or class does not exist.') !== false)
                    throw new InvalidPrinterProfileException(
                        'Printer profile does not exist.'
                    );
                else
                    throw new PrinterException(
                        $process->getErrorOutput()
                    );
            }
        }
        catch (RuntimeException $e) {
            throw new PrinterException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    public function getLprBinaryPath()
    {
        return $this->lprBinaryPath;
    }

    /**
     * @param string $lprBinaryPath
     */
    public function setLprBinaryPath($lprBinaryPath)
    {
        $this->lprBinaryPath = $lprBinaryPath;
    }

    /**
     * @param PrinterProfileInterface $printerProfile
     * @return array
     */
    private function getOptionArgumentsByPrinterProfile(PrinterProfileInterface $printerProfile)
    {
        $arguments = [];

        foreach ($printerProfile->getOptions() as $key => $value) {
            $arguments[] = '-o';
            $arguments[] = $value !== null ? implode('=', [$key, $value]) : $key;
        }

        return $arguments;
    }

    /**
     * @param PrintableFileInterface $printable
     * @param PrinterProfileInterface $printerProfile
     * @return array
     */
    public function getProcessArguments(PrintableInterface $printable, PrinterProfileInterface $printerProfile)
    {
        return array_merge(
            [$this->lprBinaryPath],
            $this->getOptionArgumentsByPrinterProfile($printerProfile),
            [
                '-P',
                $printerProfile->getName()
            ]
        );
    }

    /**
     * @param callable $processCallback
     */
    public function setProcessCallback($processCallback)
    {
        $this->processCallback = $processCallback;
    }
}