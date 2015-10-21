<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 27.11.14
 * Time: 11:49
 */

namespace Gutenberg\Printer;


use Gutenberg\CUPS\Exception\PrinterProfileNotFoundException;
use Gutenberg\CUPS\Manager;
use Gutenberg\CUPS\PrinterProfile;
use Gutenberg\CUPS\PrinterProfileInterface;
use Gutenberg\Printable\PrintableFileInterface;
use Gutenberg\Printable\PrintableInterface;
use Gutenberg\Printer\Exception\PrinterException;
use Gutenberg\Printer\Exception\PrinterTimeoutException;
use ProcessUtil\Exception\ExecutableNotFoundException;
use ProcessUtil\ProcessUtil;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class CUPSPrinter
 * @package Gutenberg\Printer
 * @todo ExecutableNotFoundException implementation
 */
class CUPSPrinter {
    const PRINT_TIMEOUT = 3;

    /** @var Manager */
    private $manager;

    /** @var PrinterProfileInterface */
    private $printerProfile;

    /**
     * CUPSPrinter constructor.
     * @param Manager $manager
     * @param PrinterProfileInterface $printerProfile
     */
    public function __construct(Manager $manager, PrinterProfileInterface $printerProfile)
    {
        $this->manager = $manager;
        $this->printerProfile = $printerProfile;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return PrinterProfileInterface
     */
    public function getPrinterProfile()
    {
        return $this->printerProfile;
    }

    /**
     * Get printer's name
     * @return string
     */
    public function getName()
    {
        return $this->getPrinterProfile()->getName();
    }

    /**
     * Push printable to print queue
     * @param PrintableInterface $printable
     * @throws PrinterTimeoutException
     * @throws PrinterException
     * @throws ExecutableNotFoundException
     * @throws PrinterProfileNotFoundException
     */
    public function enqueue(PrintableInterface $printable)
    {
        try {
            ProcessUtil::instance()
                ->executeCommand(
                    $this->getProcessArguments($printable, $this->printerProfile),
                    function ($processBuilder) use ($printable) {
                        /** @var ProcessBuilder $processBuilder */
                        $processBuilder->setTimeout(self::PRINT_TIMEOUT);
                        $processBuilder->setInput($printable->getContent());
                    }
                );
        }
        catch (ProcessTimedOutException $e) {
            throw new PrinterTimeoutException('Print timeout.', 0, $e);
        }
        catch (ProcessFailedException $e) {
            $process = $e->getProcess();

            if (null !== stripos($process->getErrorOutput(), 'The printer or class does not exist.')) {
                throw new PrinterProfileNotFoundException($this->printerProfile, $e);
            }

            throw new PrinterException($e->getMessage(), $e->getCode(), $e);
        }
        catch (RuntimeException $e) {
            throw new PrinterException($e->getMessage(), $e->getCode(), $e);
        }
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
    protected function getProcessArguments(PrintableInterface $printable, PrinterProfileInterface $printerProfile)
    {
        return array_merge(
            [$this->manager->getLprBinary()],
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
}