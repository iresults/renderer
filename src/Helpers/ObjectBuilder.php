<?php

declare(strict_types=1);

namespace Iresults\Renderer\Helpers;

use UnexpectedValueException;

/**
 * Utility to build object instances
 */
abstract class ObjectBuilder
{
    /**
     * Create an instance of the given class
     *
     * @param string $className            Name of the class to create an instance of
     * @param array  $constructorArguments Optional arguments to pass to the constructor
     *
     * @throws UnexpectedValueException if no implementation was found
     */
    public static function createInstance(string $className, array $constructorArguments = []): object
    {
        if (!$className) {
            throw new UnexpectedValueException('No class given in ' . get_called_class(), 1381327896);
        }

        return new $className(...$constructorArguments);
    }
}
