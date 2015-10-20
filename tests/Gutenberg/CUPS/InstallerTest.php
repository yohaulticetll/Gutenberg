<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 02.10.15
 * Time: 09:58
 */

namespace Gutenberg\CUPS;


use Gutenberg\CUPS\Exception\DevicesListingFailedException;
use Gutenberg\CUPS\Exception\InvalidPrinterDeviceException;
use Gutenberg\CUPS\Exception\InvalidPrinterDriverException;
use Gutenberg\CUPS\Exception\PrinterConfigurationFailed;
use Symfony\Component\Process\Process;
use TestUtil\ProcessUtilMockTrait;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    use ProcessUtilMockTrait;

    public function testGetDevices()
    {
        $processMock = $this->createProcessMock(true);
        $processMock->expects($this->any())
            ->method('getOutput')
            ->willReturn(
                'bad bad1' . PHP_EOL .
                'bad bad2' . PHP_EOL .
                'direct usb1' . PHP_EOL .
                'bad bad3' . PHP_EOL .
                'direct usb2' . PHP_EOL .
                'line' . PHP_EOL
            );

        $installer = $this->getInstaller();

        $this->assertEquals(['usb1', 'usb2'], $installer->getDevices());
    }

    public function testGetDevicesInvalidLpstatResult()
    {
        $processMock = $this->createProcessMock(true);
        $processMock->expects($this->any())
            ->method('getOutput')
            ->willReturn(
                'null'
            );

        $installer = $this->getInstaller();
        $this->setExpectedException(DevicesListingFailedException::class, 'Invalid "lpinfo" command output.');
        $this->assertEmpty($installer->getDevices());
    }

    public function testGetDevicesInvalidReturnCode()
    {
        $this->createProcessMock(false);

        $installer = $this->getInstaller();
        $this->setExpectedException(DevicesListingFailedException::class, 'The command "lpinfo" failed.');
        $this->assertEmpty($installer->getDevices());
    }

    public function testGetDrivers()
    {
        $processMock = $this->createProcessMock(true);
        $processMock->expects($this->any())
            ->method('getOutput')
            ->willReturn(
                'drv:///sample.drv/zebraep2.ppd Zebra EPL2 Label Printer' . PHP_EOL .
                'drv:///sample.drv/zebra.ppd Zebra ZPL Label Printer'
            );

        $expected = [
            'Zebra EPL2 Label Printer' => 'drv:///sample.drv/zebraep2.ppd',
            'Zebra ZPL Label Printer' => 'drv:///sample.drv/zebra.ppd'
        ];

        $installer = $this->getInstaller();
        $this->assertEquals($expected, $installer->getDrivers());
    }

    public function testGetDriversInvalidLpstatResult()
    {
        $processMock = $this->createProcessMock(true);
        $processMock->expects($this->any())
            ->method('getOutput')
            ->willReturn(
                'null'
            );

        $installer = $this->getInstaller();
        $this->setExpectedException(DevicesListingFailedException::class, 'Invalid "lpinfo" command output.');
        $this->assertEmpty($installer->getDrivers());
    }

    public function testGetDriversInvalidReturnCode()
    {
        $this->createProcessMock(false);

        $installer = $this->getInstaller();
        $this->setExpectedException(DevicesListingFailedException::class, 'The command "lpinfo" failed.');
        $this->assertEmpty($installer->getDrivers());
    }

    public function testConfigure()
    {
        $this->createProcessMock(true);

        $installer = $this->getInstaller();
        $this->assertTrue($installer->configure('printer', 'device'), 'Configure with no driver specified');
        $this->assertTrue($installer->configure('printer', 'device', 'driver'), 'Configure with driver specified');
    }

    public function testConfigureWithNoValidDevice()
    {
        $this->createProcessMock(false)
            ->expects($this->any())
            ->method('getErrorOutput')
            ->willReturn('lpadmin: Bad device-uri');

        $installer = $this->getInstaller();
        $this->setExpectedException(InvalidPrinterDeviceException::class, 'not valid device uri');
        $installer->configure('printer', 'device');
    }

    public function testConfigureWithNoValidDriver()
    {
        $this->createProcessMock(false)
            ->expects($this->any())
            ->method('getErrorOutput')
            ->willReturn('Unable to open PPD');

        $installer = $this->getInstaller();
        $this->setExpectedException(InvalidPrinterDriverException::class, 'not valid driver');
        $installer->configure('printer', 'device', 'driver');
    }

    public function testConfigureWithUnknownException()
    {
        $this->createProcessMock(false)
            ->expects($this->any())
            ->method('getErrorOutput')
            ->willReturn('some error');

        $installer = $this->getInstaller();
        $this->setExpectedException(PrinterConfigurationFailed::class, 'Printer configuration failed.');
        $installer->configure('printer', 'device', 'driver');
    }

    /**
     * @return Installer
     */
    private function getInstaller()
    {
        $manager = new Manager();
        return new Installer($manager);
    }
}
