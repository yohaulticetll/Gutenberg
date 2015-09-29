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
use Gutenberg\Printer\CUPS\Exception\PrinterProfileNotFoundException;
use Gutenberg\Printer\CUPS\PrinterProfile;
use Gutenberg\Printer\CUPS\PrinterProfileInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class CUPSPrinter {
    const LPR_TIMEOUT = 30;

    /**
     * Path to CUPS's lpr binary
     * @var string
     */
    private $lprBinaryPath = 'lpr';

    /**
     * Instance of builder which will create process executor
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * CUPSPrinter constructor.
     * @param ProcessBuilder $processBuilder
     */
    public function __construct(ProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * @param PrintableFileInterface $printable
     * @param PrinterProfileInterface $printerProfile
     */
    public function enqueue(PrintableInterface $printable, PrinterProfileInterface $printerProfile)
    {
        $processBuilder = $this->processBuilder;
        $processBuilder->setArguments($this->getProcessArguments($printable, $printerProfile));
        $processBuilder->setTimeout(self::LPR_TIMEOUT);
        $processBuilder->setInput($printable->getContent());

        try {
            $process = $processBuilder->getProcess();
            $process->run();

            if (!$process->isSuccessful()) {
                $this->handleProcessFailures($process);
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
            $arguments[] = $value !== null ? implode($key == PrinterProfile::MEDIA_SIZE_CUSTOM ? '.' : '=', [$key, $value]) : $key;
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
     * @param Process $process
     */
    protected function handleProcessFailures(Process $process)
    {
        $errorOutput = $process->getErrorOutput();

        if (strpos($errorOutput, 'The printer or class does not exist.') !== false) {
            throw new PrinterProfileNotFoundException(
                'Printer profile does not exist.'
            );
        } else {
            throw new PrinterException(
                $process->getErrorOutput()
            );
        }
    }


    /**
     * @param callable $processCallback
     */
    public function setProcessCallback($processCallback)
    {
        $this->processCallback = $processCallback;
    }
}