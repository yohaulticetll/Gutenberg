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
     * @var array
     */
    private $params;

    /**
     * @var PreprocessorInterface[]
     */
    private $preprocessors = [];

    /**
     * @return PreprocessorInterface[]
     */
    private function getPreprocessors()
    {
        return [
            new VariableReplacer(),
            new GRFQrCodeReplacer()
        ];
    }

    /**
     * ZPLPrintable constructor.
     * @param $zpl
     */
    public function __construct($zpl)
    {
        $this->zpl = $zpl;
        $this->preprocessors = $this->getPreprocessors();
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
    public function bindParams($params)
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
        return $this->preprocess($this->zpl, $this->params);
    }

    /**
     * @param $zpl
     * @param $params
     * @return string
     */
    private function preprocess($zpl, $params)
    {
        foreach ($this->preprocessors as $preprocessor) {
            $zpl = $preprocessor->replace($zpl, $params);
        }

        return $zpl;
    }
}