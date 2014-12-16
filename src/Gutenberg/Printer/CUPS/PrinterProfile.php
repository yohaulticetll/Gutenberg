<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 16.12.14
 * Time: 11:25
 */

namespace Gutenberg\Printer\CUPS;


class PrinterProfile implements PrinterProfileInterface {
    /**
     * Name of printer profile
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param $name
     * @param $options
     */
    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }


    /**
     * Get name of CUPS printer profile
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getOption($key, $default = null)
    {
        if (!isset($this->options[$key]))
            return $default;

        return $this->options[$key];
    }

    /**
     * @param string $key
     * @param string $value
     * @return self
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }
}