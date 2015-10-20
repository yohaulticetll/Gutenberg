<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 02.10.15
 * Time: 09:30
 */

namespace Gutenberg\CUPS;


use Gutenberg\CUPS\Exception\DevicesListingFailedException;
use Gutenberg\CUPS\Exception\InvalidPrinterDeviceException;
use Gutenberg\CUPS\Exception\InvalidPrinterDriverException;
use Gutenberg\CUPS\Exception\PrinterConfigurationFailed;
use ProcessUtil\Exception\ExecutableNotFoundException;
use ProcessUtil\ProcessUtil;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class Installer
 * @package Gutenberg\CUPS
 * @todo ExecutableNotFoundException implementation
 */
class Installer
{
    /** @var Manager */
    protected $manager;

    /**
     * Installer constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get available printer devices
     * @return array|string[]
     * @throws DevicesListingFailedException
     * @throws ExecutableNotFoundException
     */
    public function getDevices()
    {
        try {
            $process = ProcessUtil::instance()
                ->executeCommand([$this->manager->getLpinfoBinary(), '-v --timeout 1']);

            $matches = [];
            $devicesList = [];
            if (!preg_match_all('/direct (.*)/', $process->getOutput(), $matches, PREG_SET_ORDER)) {
                throw new DevicesListingFailedException('Invalid "lpinfo" command output.');
            }

            foreach ($matches as $match) {
                $devicesList[] = $match[1];
            }

            return $devicesList;
        }
        catch (ProcessFailedException $e) {
            throw new DevicesListingFailedException('The command "lpinfo" failed.', 0, $e);
        }
    }

    /**
     * Get available printer drivers
     * @return array|string[]
     * @throws DevicesListingFailedException
     */
    public function getDrivers()
    {
        try {
            $process = ProcessUtil::instance()
                ->executeCommand([$this->manager->getLpinfoBinary(), '-m']);

            $matches = [];
            $driversList = [];
            if (!preg_match_all('/([^\s]*) (.*)/', $process->getOutput(), $matches, PREG_SET_ORDER)) {
                throw new DevicesListingFailedException('Invalid "lpinfo" command output.');
            }

            foreach ($matches as $match) {
                $driversList[$match[2]] = $match[1];
            }

            return $driversList;
        }
        catch (ProcessFailedException $e) {
            throw new DevicesListingFailedException('The command "lpinfo" failed.', 0, $e);
        }
    }

    /**
     * @param $name
     * @param $device
     * @param null $driver
     * @return bool
     * @throws InvalidPrinterDeviceException
     * @throws PrinterConfigurationFailed
     * @throws ExecutableNotFoundException
     * @throws InvalidPrinterDeviceException
     * @throws InvalidPrinterDriverException
     */
    public function configure($name, $device, $driver = null)
    {
        try {
            $arguments = [$this->manager->getLpadminBinary(), '-p', $name, '-v', $device, '-E'];

            if ($driver) {
                $arguments = array_merge($arguments, ['-P', $driver]);
            }

            // in theory we don't need to check process output if it's succedded
            ProcessUtil::instance()
                ->executeCommand($arguments);

            return true;
        }
        catch (ProcessFailedException $e) {
            $output = $e->getProcess()->getErrorOutput();
            if (false !== strpos($output, 'lpadmin: Bad device-uri')) {
                throw new InvalidPrinterDeviceException(sprintf('"%s" is not valid device uri.', $device), 0, $e);
            }
            elseif (false !== strpos($output, 'Unable to open PPD')) {
                throw new InvalidPrinterDriverException(sprintf('"%s" is not valid driver.', $driver), 0, $e);
            }

            throw new PrinterConfigurationFailed('Printer configuration failed.', 0, $e);
        }
    }
}