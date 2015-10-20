<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 03.02.15
 * Time: 10:43
 */

namespace Gutenberg\Printable;


use Gutenberg\Printable\ZPL\PreprocessorInterface;
use Gutenberg\Printable\ZPL\Replacer\GRFQrCodeReplacer;
use Gutenberg\Printable\ZPL\Replacer\VariableReplacer;

class ZPLPrintable implements PrintableInterface {
    /**
     * @var string
     */
    private $zpl;

    /**
     * @var PreprocessorInterface[]
     */
    private $preprocessors = [];

    /**
     * @var array
     */
    private $params = [];

    /**
     * ZPLPrintable constructor.
     * @param string $zpl
     * @param ZPL\PreprocessorInterface[] $preprocessors
     */
    public function __construct($zpl, array $preprocessors)
    {
        $this->zpl = $zpl;
        $this->preprocessors = $preprocessors;
    }

    /**
     * Add preprocessor
     *
     * @param PreprocessorInterface $preprocessor
     * @return $this
     */
    public function addPreprocessor(PreprocessorInterface $preprocessor)
    {
        $this->preprocessors[] = $preprocessor;
        return $this;
    }

    /**
     * Bind printable parameters
     *
     * @param $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Get original ZPL source
     *
     * @return string
     */
    public function getOriginalContent()
    {
        return $this->zpl;
    }

    /**
     * Get preprocessed content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->processContent($this->zpl, $this->params);
    }

    /**
     * @param $zpl
     * @param $params
     * @return string
     */
    private function processContent($zpl, $params)
    {
        foreach ($this->preprocessors as $preprocessor) {
            $zpl = $preprocessor->replace($zpl, $params);
        }

        return $zpl;
    }
}