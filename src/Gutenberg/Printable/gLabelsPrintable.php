<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 27.11.14
 * Time: 12:53
 */

namespace Gutenberg\Printable;


use Gutenberg\Printable\gLabels\Exception\gLabelsException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

class gLabelsPrintable extends PrintableFile {
    const GLABELS_TIMEOUT = 5;
    /**
     * Path to .glabels file
     * @var \SplFileInfo
     */
    private $gLabelsFile;

    /**
     * Data
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $gLabelsBinaryPath = 'glabels-3-batch';

    public function __construct(\SplFileInfo $gLabelsFile, $data = null)
    {
        if (!is_array($data))
            throw new \InvalidArgumentException(
                '$data has to be data array list'
            );

        $this->gLabelsFile = $gLabelsFile;
        $this->data        = $data;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        if (!$this->file)
            $this->file = $this->convertToPdf();

        return $this->file;
    }

    /**
     * Merges gLabels with CSV file with result as pdf file
     */
    private function convertToPdf()
    {
        $cachedFile = $this->createCacheFile();
        $dataFile   = $this->createDataFile();

        $processBuilder = new ProcessBuilder();
        $processBuilder->setArguments([
            $this->gLabelsBinaryPath,
            '-o',
            (string)$cachedFile,
            '-i',
            (string)$dataFile,
            (string)$this->gLabelsFile
        ]);
        $processBuilder->setTimeout(self::GLABELS_TIMEOUT);

        try {
            $process = $processBuilder->getProcess();
            $process->run();

            if (!$process->isSuccessful() || !$cachedFile->isReadable()) {
                throw new gLabelsException(
                    $process->getErrorOutput()
                );
            }

        }
        catch (RuntimeException $e) {
            throw new gLabelsException($e->getMessage(), $e->getCode(), $e);
        }
        finally {
            unlink((string)$dataFile);
        }

        return $cachedFile;
    }

    /**
     *
     */
    public function __destruct()
    {
        // if file is arleady cached, have to remove it when script ends
        if ($this->file)
            unlink((string)$this->file);
    }

    /**
     * On wakeup cachedFile won't exist
     */
    public function __wakeup()
    {
        $this->file = null;
    }

    /**
     * Create unique file
     * @return \SplFileInfo
     */
    private function createCacheFile()
    {
        while (true) {
            $filename = sys_get_temp_dir() . '/' . uniqid(sha1(__CLASS__), true) . '.pdf';
            if (!file_exists($filename)) break;
        }

        return new \SplFileInfo($filename);
    }

    private function createDataFile()
    {
        while (true) {
            $filename = sys_get_temp_dir() . '/' . uniqid(sha1(__CLASS__), true) . '.csv';
            if (!file_exists($filename)) break;
        }

        $dataFile = new \SplFileInfo($filename);
        $write = $dataFile->openFile('w');

        $header = false;
        foreach ($this->data as $row) {
            if (!$header) {
                $write->fputcsv(array_keys($row));
                $header = true;
            }

            $write->fputcsv(array_values($row));
        }

        return $dataFile;
    }

    /**
     * @return mixed
     */
    public function getGLabelsBinaryPath()
    {
        return $this->gLabelsBinaryPath;
    }

    /**
     * @param mixed $gLabelsBinaryPath
     */
    public function setGLabelsBinaryPath($gLabelsBinaryPath)
    {
        $this->gLabelsBinaryPath = $gLabelsBinaryPath;
    }
}