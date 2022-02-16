<?php
declare(strict_types=1);

namespace Iresults\Renderer\Helpers;

use BadMethodCallException;
use UnexpectedValueException;

/**
 * An abstract base class for object factories
 */
abstract class AbstractFactory
{
    /**
     * Create an instance of the factory's classes
     *
     * @param array  $constructorArguments Optional arguments to pass to the constructor
     * @param string $className            Name of the class to create an instance of
     * @return object
     * @throws UnexpectedValueException if no implementation was found
     */
    protected static function createInstance(array $constructorArguments = [], string $className = ''): object
    {
        if (!$className) {
            $className = static::getFactoryClass();
            if ($className === null) {
                throw new UnexpectedValueException('No implementation found in ' . get_called_class(), 1381327896);
            }
        }
        if (!$constructorArguments) {
            return new $className();
        }
        $reflect = new \ReflectionClass($className);

        return $reflect->newInstanceArgs($constructorArguments);
    }

    /**
     * Return the name of the class the factory should produce
     *
     * @return string|null
     * @throws BadMethodCallException if the method has not been overwritten
     */
    protected static function getFactoryClass(): ?string
    {
        throw new BadMethodCallException('Please overwrite this static method', 1381327557);
    }
}
