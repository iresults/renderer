<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf\Wrapper;

/**
 * Wrapper class for the FPDF library
 */
class FpdfWrapper extends \FPDF
{
    /**
     * Throws an exception
     *
     * @param string $msg
     * @throws WrapperException
     */
    public function throwException($msg)
    {
        throw new WrapperException($msg);
    }

    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    // OVERWRITES
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
    /**
     * Throws an exception instead of terminating the script on error
     *
     * @param string $msg
     * @throws WrapperException
     */
    function Error($msg)
    {
        $this->throwException($msg);
    }
}

