<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf;

use Iresults\Core\Core;
use Iresults\Renderer\Helpers\ObjectBuilder;
use Iresults\Renderer\Pdf\Wrapper\FpdfWrapper;

class Factory extends Core
{
    /**
     * Return an instance of the available PDF wrapper
     *
     * @param array|null $parameters Parameters to pass to the constructor
     * @return object|PdfInterface|null
     */
    public static function makeInstance(array $parameters = null): ?object
    {
        if (class_exists('FPDF', true)) {
            return ObjectBuilder::createInstance(FpdfWrapper::class, $parameters);
        } else {
            return null;
        }
    }
}
