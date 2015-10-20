<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 02.10.15
 * Time: 09:18
 */

namespace Gutenberg\CUPS;


use Gutenberg\Printer\CUPSPrinter;

class ManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPrinter()
    {
        $printerProfileMock = $this->getMock(PrinterProfileInterface::class);
        $printerProfileMock
            ->expects($this->any())
            ->method('getName')->willReturn(true);

        $manager = new Manager();
        $printer = $manager->getPrinter($printerProfileMock);
        $this->assertInstanceOf(CUPSPrinter::class, $printer);
        $this->assertEquals($printerProfileMock, $printer->getPrinterProfile());
    }
}
