<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 27.11.14
 * Time: 12:44
 */

namespace Gutenberg\Printer\CUPS;


interface PrinterProfileInterface {
    /**
     * Get name of CUPS printer profile
     * @return string
     */
    public function getName();

    /**
     * Get workstation identifier
     * @return string
     */
    public function getWorkstation();

    /**
     * @return string
     */
    public function getOptions();

    /**
     * @param string $key
     * @return string
     */
    public function getOption($key);

    /**
     * @param string $key
     * @param string $value
     * @return self
     */
    public function setOption($key, $value);

    /**
     * @param string $workstation
     * @return self
     */
    public function setWorkstation($workstation);
}