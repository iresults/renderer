<?php
declare(strict_types=1);

namespace Iresults\Renderer\Pdf;

use Iresults\Core\Core;
use Iresults\Renderer\Pdf\Wrapper\FpdfWrapper;
use ReflectionClass;

class Factory extends Core
{
    /**
     * Return the best available PDF object
     *
     * @param array|null $parameters Parameters to pass to the constructor
     * @return object|PdfInterface|null
     */
    static public function makeInstance(array $parameters = null): ?object
    {
        if (class_exists('FPDF', true)) {
            return self::createInstanceOfClassNameWithArguments(FpdfWrapper::class, $parameters);
        } else {
            return null;
        }
    }

    /**
     * Create an instance of the given class with the given arguments
     *
     * @param string $className  The class to create an instance of
     * @param array  $parameters The parameters to pass to the constructor
     * @return    PdfInterface
     */
    static protected function createInstanceOfClassNameWithArguments(string $className, array $parameters): object
    {
        if ($parameters === null) {
            return new $className();
        }
        /*
         * Apply the parameters to the constructor.
         * @see http://stackoverflow.com/questions/2409237/how-to-call-the-constructor-with-call-user-func-array-in-php
         */
        $reflect = new ReflectionClass($className);

        return $reflect->newInstanceArgs($parameters);
    }
}
