<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 16.12.14
 * Time: 11:25
 */

namespace Gutenberg\CUPS;


class PrinterProfile implements PrinterProfileInterface {
    const MEDIA_SIZE = 'media';
    const MEDIA_SIZE_CUSTOM = 'media=Custom';
    const FIT_TO_PAGE = 'fit-to-page';

    /**
     * Name of printer profile
     * @var string
     */
    protected $name;

    /**
     * Workstation identifier
     * @var string
     */
    protected $workstation;

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

    /**
     * @return string
     */
    public function getWorkstation()
    {
        return $this->workstation;
    }

    /**
     * Set workstation identifier
     * @param string $workstation
     */
    public function setWorkstation($workstation)
    {
        $this->workstation = $workstation;

        return $this;
    }
}