<?php
/*
 *  Copyright notice
 *
 *  (c) 2014 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @author COD
 * Created 14.02.14 16:22
 */

namespace Iresults\Renderer\Helpers;

/**
 * An abstract base class for object factories
 */
abstract class AbstractFactory {
	/**
	 * Creates an instance of the factories classes
	 *
	 * @param array  $constructorArguments Optional arguments to pass to the constructor
	 * @param string $className            Name of the class to create an instance of
	 * @throws \UnexpectedValueException if no implementation was found
	 * @return object
	 */
	static protected function _createInstance($constructorArguments = array(), $className = '') {
		if (!$className) {
			$className = static::_getFactoryClass();
			if ($className === FALSE) {
				throw new \UnexpectedValueException('No implementation found in ' . get_called_class(), 1381327896);
			}
		}
		if (!$constructorArguments) {
			return new $className();
		}
		$reflect  = new \ReflectionClass($className);
		$instance = $reflect->newInstanceArgs($constructorArguments);
		return $instance;
	}

	/**
	 * Returns the name of the class the factory should produce
	 *
	 * @throws \UnexpectedValueException if the method has not been overwritten
	 * @return string
	 */
	static protected function _getFactoryClass() {
		throw new \UnexpectedValueException('Please overwrite this static method', 1381327557);
	}
}