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
 * Created 14.02.14 16:17
 */


namespace Iresults\Renderer\Pdf\Wrapper;

use Iresults\Renderer\Exception;

/**
 * Wrapper class for the FPDF library
 *
 * @package Iresults\Renderer\Pdf\Wrapper
 */
class FpdfWrapper extends \FPDF {
	/**
	 * Throws an exception
	 *
	 * @param string $msg
	 * @throws WrapperException
	 */
	public function throwException($msg) {
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
	function Error($msg) {
		$this->throwException($msg);
	}
}

