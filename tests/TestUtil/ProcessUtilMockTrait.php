<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 02.10.15
 * Time: 09:50
 */

namespace TestUtil;


use ProcessUtil\ProcessUtil;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait ProcessUtilMockTrait
{
    public function createProcessMock($successfully = true)
    {
        /** @var \PHPUnit_Framework_TestCase $this */
        $processBuilderMock = $this->getMock(ProcessBuilder::class);
        $processMock = $this->getMock(Process::class, [], [], '', false);

        $processBuilderMock->expects($this->any())
            ->method('getProcess')
            ->willReturn($processMock);

        $processMock->expects($this->any())
            ->method('run')
            ->willReturn(null);

        $processMock->expects($this->any())
            ->method('isSuccessful')
            ->willReturn($successfully);

        ProcessUtil::instance()
            ->setProcessBuilder($processBuilderMock);

        return $processMock;
    }
}