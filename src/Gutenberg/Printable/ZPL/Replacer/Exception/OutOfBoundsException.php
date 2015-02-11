<?php
/**
 * Created by IntelliJ IDEA.
 * User: kofel
 * Date: 06.02.15
 * Time: 09:57
 */

namespace Gutenberg\Printable\ZPL\Replacer\Exception;

class OutOfBoundsException extends \OutOfBoundsException {
    /**
     * @var string
     */
    private $variable;

    public function __construct($variable)
    {
        $this->variable = $variable;

        parent::__construct(sprintf(
            'Unknown key "%s".',
            $variable
        ), 0, null);
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }
}